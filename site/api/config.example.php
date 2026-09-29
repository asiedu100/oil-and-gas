<?php
// Copy this file to config.php (same folder) and fill it in.
// config.php must never be public or committed to Git. The .htaccess in this folder blocks it on Apache;
// on other servers add the equivalent rule (see docs/DEPLOYMENT-RUNBOOK.md).

return [
    // Inbox that receives the inquiries
    'to' => 'info@jaboassociates.business',

    // Sender shown on the email. Use an address on your own domain (for example one you created in your hosting panel).
    'from_email' => 'inquiries@jaboassociates.business',
    'from_name' => 'Jabo & Associates Website',

    // How the email is sent:
    //   'mail'   - PHP's built-in mail(): simplest, works on most shared hosting
    //   'resend' - Resend.com API: best delivery, needs an account and a verified domain
    //   'log'    - writes the email to api/outbox/ instead of sending it: LOCAL TESTING ONLY
    'mail_driver' => 'mail',
    'resend_api_key' => '',

    // Spam controls
    'min_seconds' => 2.5,   // fastest a human could plausibly complete the form
    'rate_limit' => 5,      // inquiries allowed per visitor address ...
    'rate_window' => 3600,  // ... in this many seconds
];
