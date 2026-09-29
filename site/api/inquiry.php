<?php
// Inquiry form handler. Receives the POST from contact.html, checks it, and emails it to the company.
// Runs on any host with PHP 7.2 or newer. Settings live in config.php (copy config.example.php) or in
// environment variables INQUIRY_TO, INQUIRY_FROM, RESEND_API_KEY, MAIL_DRIVER.

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
header('X-Content-Type-Options: nosniff');
ini_set('display_errors', '0');

const MAX_PDF = 5 * 1024 * 1024;
const ROLES = ['Buyer', 'Seller / Supplier', 'Mandate', 'Investor / Partner', 'Solar & EV client'];

function respond($status, $body)
{
    http_response_code($status);
    echo json_encode($body);
    exit;
}

function config()
{
    static $cfg = null;
    if ($cfg !== null) {
        return $cfg;
    }
    $cfg = [
        'to' => '',
        'from_email' => '',
        'from_name' => 'Jabo & Associates Website',
        'mail_driver' => 'mail',
        'resend_api_key' => '',
        'min_seconds' => 2.5,
        'rate_limit' => 5,
        'rate_window' => 3600,
    ];
    $file = __DIR__ . '/config.php';
    if (is_file($file)) {
        $loaded = require $file;
        if (is_array($loaded)) {
            $cfg = array_merge($cfg, $loaded);
        }
    }
    $env = ['INQUIRY_TO' => 'to', 'INQUIRY_FROM' => 'from_email', 'RESEND_API_KEY' => 'resend_api_key', 'MAIL_DRIVER' => 'mail_driver'];
    foreach ($env as $name => $key) {
        $value = getenv($name);
        if ($value !== false && $value !== '') {
            $cfg[$key] = $value;
        }
    }
    return $cfg;
}

function clean($value, $max = 200, $multiline = false)
{
    if (!is_string($value)) {
        return '';
    }
    $value = trim($value);
    if (!preg_match('//u', $value)) {
        return '';
    }
    $value = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $value);
    if (!$multiline) {
        $value = preg_replace('/[\r\n]+/', ' ', $value);
    }
    preg_match('/^.{0,' . (int) $max . '}/us', $value, $m);
    return isset($m[0]) ? trim($m[0]) : '';
}

function esc($value)
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function post($key, $max = 200, $multiline = false)
{
    return clean(isset($_POST[$key]) ? $_POST[$key] : '', $max, $multiline);
}

// Allow at most rate_limit inquiries per visitor address in rate_window seconds.
function rate_limited($cfg)
{
    $limit = (int) $cfg['rate_limit'];
    if ($limit <= 0) {
        return false;
    }
    $dir = __DIR__ . '/rate';
    if (!is_dir($dir) && !@mkdir($dir, 0750, true)) {
        $dir = sys_get_temp_dir() . '/jabo-rate';
        if (!is_dir($dir) && !@mkdir($dir, 0750, true)) {
            return false;
        }
    }
    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'unknown';
    $path = $dir . '/' . sha1($ip) . '.json';
    $now = time();
    $window = (int) $cfg['rate_window'];
    $fh = @fopen($path, 'c+');
    if (!$fh) {
        return false;
    }
    flock($fh, LOCK_EX);
    $times = json_decode((string) stream_get_contents($fh), true);
    $times = is_array($times) ? array_values(array_filter($times, function ($t) use ($now, $window) {
        return $t > $now - $window;
    })) : [];
    $blocked = count($times) >= $limit;
    if (!$blocked) {
        $times[] = $now;
    }
    ftruncate($fh, 0);
    rewind($fh);
    fwrite($fh, json_encode($times));
    flock($fh, LOCK_UN);
    fclose($fh);
    if (mt_rand(1, 50) === 1) {
        foreach ((array) glob($dir . '/*.json') as $old) {
            if (@filemtime($old) < $now - 2 * $window) {
                @unlink($old);
            }
        }
    }
    return $blocked;
}

function http_post_json($url, array $headers, $json)
{
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $json,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 20,
        ]);
        $body = curl_exec($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        return [$status, (string) $body];
    }
    $context = stream_context_create(['http' => [
        'method' => 'POST',
        'header' => implode("\r\n", $headers),
        'content' => $json,
        'timeout' => 20,
        'ignore_errors' => true,
    ]]);
    $body = @file_get_contents($url, false, $context);
    $status = 0;
    if (isset($http_response_header[0]) && preg_match('#\s(\d{3})\s#', $http_response_header[0], $m)) {
        $status = (int) $m[1];
    }
    return [$status, (string) $body];
}

function encode_header($text)
{
    return '=?UTF-8?B?' . base64_encode($text) . '?=';
}

// Builds the message headers and body (multipart: plain text + HTML, with an optional PDF attached).
function build_mime($fromName, $fromEmail, $replyTo, $text, $html, $attachment)
{
    $mixed = 'mix_' . bin2hex(random_bytes(8));
    $alt = 'alt_' . bin2hex(random_bytes(8));
    $headers = [
        'From: ' . encode_header($fromName) . ' <' . $fromEmail . '>',
        'Reply-To: ' . $replyTo,
        'MIME-Version: 1.0',
        'Content-Type: multipart/mixed; boundary="' . $mixed . '"',
    ];
    $body = "--$mixed\r\nContent-Type: multipart/alternative; boundary=\"$alt\"\r\n\r\n"
        . "--$alt\r\nContent-Type: text/plain; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
        . chunk_split(base64_encode($text)) . "\r\n"
        . "--$alt\r\nContent-Type: text/html; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n"
        . chunk_split(base64_encode($html)) . "\r\n"
        . "--$alt--\r\n";
    if ($attachment) {
        $name = $attachment['name'];
        $body .= "--$mixed\r\nContent-Type: application/pdf; name=\"$name\"\r\n"
            . "Content-Transfer-Encoding: base64\r\nContent-Disposition: attachment; filename=\"$name\"\r\n\r\n"
            . chunk_split(base64_encode($attachment['data'])) . "\r\n";
    }
    $body .= "--$mixed--\r\n";
    return [implode("\r\n", $headers), $body];
}

function send_inquiry($cfg, $subject, $replyTo, $text, $html, $attachment)
{
    $to = $cfg['to'];
    $fromEmail = $cfg['from_email'];
    if ($to === '' || $fromEmail === '') {
        error_log('inquiry.php: "to" or "from_email" is not configured');
        return false;
    }
    $driver = $cfg['mail_driver'];

    if ($driver === 'resend') {
        $payload = [
            'from' => $cfg['from_name'] . ' <' . $fromEmail . '>',
            'to' => [$to],
            'reply_to' => $replyTo,
            'subject' => $subject,
            'text' => $text,
            'html' => $html,
        ];
        if ($attachment) {
            $payload['attachments'] = [['filename' => $attachment['name'], 'content' => base64_encode($attachment['data'])]];
        }
        list($status) = http_post_json(
            'https://api.resend.com/emails',
            ['Authorization: Bearer ' . $cfg['resend_api_key'], 'Content-Type: application/json'],
            json_encode($payload)
        );
        if ($status < 200 || $status >= 300) {
            error_log('inquiry.php: Resend returned HTTP ' . $status);
        }
        return $status >= 200 && $status < 300;
    }

    list($headers, $body) = build_mime($cfg['from_name'], $fromEmail, $replyTo, $text, $html, $attachment);

    if ($driver === 'log') {
        // Local testing only: writes the email to api/outbox instead of sending it.
        $dir = __DIR__ . '/outbox';
        if (!is_dir($dir) && !@mkdir($dir, 0750, true)) {
            return false;
        }
        $file = $dir . '/' . date('Ymd-His') . '-' . bin2hex(random_bytes(3)) . '.eml';
        $message = 'To: ' . $to . "\r\nSubject: " . encode_header($subject) . "\r\n" . $headers . "\r\n\r\n" . $body;
        return file_put_contents($file, $message) !== false;
    }

    return mail($to, encode_header($subject), $body, $headers, '-f' . $fromEmail);
}

// ---------------------------------------------------------------------------

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    respond(405, ['error' => 'Method not allowed.']);
}

if (empty($_POST) && empty($_FILES) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    respond(400, ['error' => 'The upload was too large. Attach a PDF of 5 MB or less, or send your message without the attachment.']);
}

$cfg = config();

// Spam check 1: a hidden field that people never see or fill in. Bots do; they get a fake success.
if (post('extra_info') !== '') {
    respond(200, ['ok' => true]);
}

// Spam check 2: the page reports how long the visitor spent on the form. Scripts submit instantly.
$elapsed = isset($_POST['elapsed']) && is_numeric($_POST['elapsed']) ? (float) $_POST['elapsed'] / 1000 : 0;
if ($elapsed < (float) $cfg['min_seconds']) {
    respond(400, ['error' => 'Please take a moment to review your inquiry, then submit it again.']);
}

$d = [
    'name' => post('name'),
    'company' => post('company'),
    'country' => post('country'),
    'email' => post('email'),
    'phone' => post('phone', 40),
    'role' => post('role'),
    'commodity' => post('commodity'),
    'quantity' => post('quantity', 30),
    'unit' => post('unit', 20),
    'port' => post('port'),
    'terms' => post('terms', 20),
    'message' => post('message', 5000, true),
];

foreach (['name', 'company', 'country', 'email', 'phone', 'role', 'commodity', 'message'] as $required) {
    if ($d[$required] === '') {
        respond(400, ['error' => 'Please fill in all required fields.']);
    }
}
if (!filter_var($d['email'], FILTER_VALIDATE_EMAIL)) {
    respond(400, ['error' => 'Please enter a valid email address.']);
}
if (!preg_match('/^\+[0-9 ()\-]{7,}$/', $d['phone'])) {
    respond(400, ['error' => 'Please include the country code in your phone number.']);
}
if (!in_array($d['role'], ROLES, true)) {
    respond(400, ['error' => 'Please choose who you are.']);
}

$attachment = null;
if (isset($_FILES['document']) && $_FILES['document']['error'] !== UPLOAD_ERR_NO_FILE) {
    $file = $_FILES['document'];
    if ($file['error'] === UPLOAD_ERR_INI_SIZE || $file['error'] === UPLOAD_ERR_FORM_SIZE || $file['size'] > MAX_PDF) {
        respond(400, ['error' => 'The document must be 5 MB or smaller.']);
    }
    if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name'])) {
        respond(400, ['error' => 'The document could not be uploaded. Please try again.']);
    }
    $data = file_get_contents($file['tmp_name']);
    if ($data === false || substr($data, 0, 4) !== '%PDF') {
        respond(400, ['error' => 'The document must be a PDF.']);
    }
    $name = preg_replace('/[^\w.\- ]/', '_', basename($file['name']));
    $attachment = ['name' => substr($name, 0, 80) ?: 'document.pdf', 'data' => $data];
}

// Spam check 3: limit how often one visitor address can send inquiries. Only inquiries that
// would actually be emailed count, so a visitor who mistypes a field is never locked out.
if (rate_limited($cfg)) {
    respond(429, ['error' => 'Too many inquiries from your connection. Please try again later.']);
}

$rows = [
    'Name' => $d['name'],
    'Company' => $d['company'],
    'Country' => $d['country'],
    'Email' => $d['email'],
    'Phone / WhatsApp' => $d['phone'],
    'I am a' => $d['role'],
    'Commodity' => $d['commodity'],
];
if ($d['quantity'] !== '') {
    $rows['Quantity'] = trim($d['quantity'] . ' ' . $d['unit']);
}
if ($d['port'] !== '') {
    $rows['Destination port'] = $d['port'];
}
if ($d['terms'] !== '') {
    $rows['Delivery terms'] = $d['terms'];
}

$text = '';
$html = '<table cellpadding="6" style="font-family:sans-serif;font-size:14px;border-collapse:collapse">';
foreach ($rows as $label => $value) {
    $text .= $label . ': ' . $value . "\r\n";
    $html .= '<tr><td style="color:#666"><b>' . esc($label) . '</b></td><td>' . esc($value) . '</td></tr>';
}
$text .= "\r\n" . str_replace("\n", "\r\n", str_replace("\r\n", "\n", $d['message'])) . "\r\n";
$html .= '</table><p style="font-family:sans-serif;font-size:14px;white-space:pre-wrap">' . esc($d['message']) . '</p>';

$subject = 'New inquiry: ' . $d['role'] . ' / ' . $d['commodity'] . ' / ' . $d['company'];

try {
    $sent = send_inquiry($cfg, $subject, $d['email'], $text, $html, $attachment);
} catch (Throwable $e) {
    error_log('inquiry.php: ' . $e->getMessage());
    $sent = false;
}

if (!$sent) {
    respond(502, ['error' => 'We could not send your inquiry right now.']);
}
respond(200, ['ok' => true]);
