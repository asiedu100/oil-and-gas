# Deployment Runbook

How to take the site live at **jaboassociates.business** and keep it running. The hosting provider is **not decided yet**, so this runbook is written to work with any host. Read section 1 to choose, then follow the track that matches.

The website itself (everything in `site/`) is plain static files and runs anywhere. The only part that depends on the host is the **inquiry form handler**, `site/api/inquiry.php`, which needs PHP.

Where a step depends on a dashboard (hosting panel, registrar, email provider), screens differ between providers. Follow the intent of each step. The server rules in Track A were tested on Apache and the form handler was tested on PHP 8.1 (see `QA-CHECKLIST.md`); the Nginx and static-host notes in Tracks B and C are **not tested** and are marked as such.

---

## 1. Choosing a host

### What the host must provide (ask before you buy)

| Requirement | Why | Track A hosts |
|---|---|---|
| **PHP 7.2 or newer** (8.x preferred) | Runs the inquiry form handler | Standard |
| **HTTPS certificate** (free Let's Encrypt or similar) | Browsers and search engines expect HTTPS | Usually one click |
| Ability to send email from PHP (`mail()`), or an SMTP service | Delivers inquiries | Standard on cPanel; some hosts restrict it |
| Email mailboxes on the domain (for `info@` and a sender address) | The inbox that receives inquiries | Usually included |
| `.htaccess` support (Apache) | HTTPS, headers, caching, blocking private files | Standard on cPanel |
| File uploads of at least 6 MB | The form accepts PDFs up to 5 MB | Set through `.user.ini` or the PHP settings screen |
| SSH, SFTP or a file manager | To upload files | Standard |

### Which track

| Track | Host type | Form handler | Status |
|---|---|---|---|
| **A: shared hosting / cPanel** | Typical paid web hosting with PHP and email | `site/api/inquiry.php` as is | Tested locally; recommended |
| **B: your own server (VPS)** | Ubuntu or similar with Apache or Nginx and PHP-FPM | `site/api/inquiry.php` with server rules | Apache rules tested; Nginx snippet untested |
| **C: static-only host** (Netlify, Vercel, GitHub Pages and similar) | No PHP | A form service, or a port of the handler to that host's functions | `netlify.toml` is in the repo (publish directory, headers, redirects); the form itself still needs Option 1 or 2 in section 5 before it will work |

If you have no strong preference, pick a Track A host. It needs the least setup and is what the code was tested against.

## 2. Before you start

Collect these. If any are missing, stop and ask the client.

| Need | Why |
|---|---|
| Login to the domain registrar for jaboassociates.business | To point the domain at the host |
| A copy (screenshot or export) of **all existing DNS records** | So nothing, especially email, breaks when records change |
| Who hosts the company email now (if anyone) | The MX and SPF records must stay intact |
| The inbox that will receive inquiries | Goes into `to` in `config.php` |
| Client sign-off on the content (`CLIENT-APPROVALS.md`) and the legal pages | Do not launch unreviewed legal text or unapproved wording |
| Hosting account for the chosen track | Where the files go |

## 3. Track A: shared hosting / cPanel

### 3.1 Add the domain and turn on HTTPS
1. In the hosting panel, add `jaboassociates.business` as the main domain (or an addon domain) and note its document root, usually `public_html`.
2. Point the domain to the host (section 6), then issue the free SSL certificate (in cPanel: **SSL/TLS Status** and run AutoSSL). Include the `www` name.

### 3.2 Create the mailboxes
Create, in the hosting panel:
- The inbox that receives inquiries (for example `info@jaboassociates.business`), if it does not exist.
- A sender address on the same domain (for example `inquiries@jaboassociates.business`). Using the domain's own address as the sender is what gives good delivery.

### 3.3 Upload the site
Upload the **contents** of the `site/` folder into the document root, so that `index.html` sits directly in `public_html`, along with `css/`, `js/`, `images/`, `fonts/` and `api/`.

- Use SFTP or the file manager. **Show hidden files** in your client: `.htaccess` (root and `api/`) and `api/.user.ini` are dotfiles and are often skipped by mistake.
- Do not upload `docs/`, `tools/`, the original photos or `node_modules`.

### 3.4 Create the form settings file
On the server (file manager or SFTP), copy `api/config.example.php` to `api/config.php` and edit it:

```php
return [
    'to' => 'info@jaboassociates.business',
    'from_email' => 'inquiries@jaboassociates.business',
    'from_name' => 'Jabo & Associates Website',
    'mail_driver' => 'mail',        // or 'resend' (see 3.6)
    'resend_api_key' => '',
    'min_seconds' => 2.5,
    'rate_limit' => 5,
    'rate_window' => 3600,
];
```

Never use `'log'` on the live site (it writes emails to a folder instead of sending them). Never commit `config.php`. Set its permissions to `640` or `600` if your host allows it.

### 3.5 Check the private files are private
Open these in a browser. Each should be **refused (403) or not found**, never shown:
- `https://jaboassociates.business/api/config.php`
- `https://jaboassociates.business/api/config.example.php`
- `https://jaboassociates.business/.htaccess`

`https://jaboassociates.business/api/inquiry.php` opened in a browser should show `{"error":"Method not allowed."}`, which is correct.

### 3.6 Make sure the emails arrive
1. Send a real test inquiry from the contact page (scenario FT-01 in `QA-CHECKLIST.md`, with a PDF close to 5 MB). Check the inbox and the spam folder.
2. **If it does not arrive or lands in spam:**
   - In the hosting panel, find **Email Deliverability** (or the equivalent) and make sure **SPF** and **DKIM** are enabled and valid for the domain. Add a DMARC record to DNS if the host does not: name `_dmarc`, type TXT, value `v=DMARC1; p=none; rua=mailto:info@jaboassociates.business`.
   - Test the score with a mail-tester service.
   - If `mail()` is blocked or unreliable on the host, switch to **Resend**: create an account at resend.com, add and verify the domain `jaboassociates.business` (add the DNS records it shows; if the company already has email on the domain, use a subdomain such as `send.jaboassociates.business` so the existing SPF record is not disturbed), create an API key with sending permission, then set `'mail_driver' => 'resend'` and `'resend_api_key' => '...'` in `config.php`. `from_email` must be an address on the verified domain.
3. If the PDF upload is refused as too large, the host is ignoring `api/.user.ini`. Raise the limits in the panel instead (cPanel: **MultiPHP INI Editor**): `upload_max_filesize = 6M` and `post_max_size = 8M`.

### 3.7 What the included `.htaccess` does
`site/.htaccess` (root) applies only on the real domain: it redirects `http` to `https`, redirects `www` to the bare domain, hides dotfiles and blocks the form handler's private files by name (`config.php`, `config.example.php`, `rate/`, `outbox/`) as a second layer alongside `site/api/.htaccess`, serves the branded 404 page (but leaves `/.well-known/` reachable, which certificate issuance needs, and never redirects it), adds security headers, sets file types, compresses text and sets browser caching. `site/api/.htaccess` separately blocks everything in `api/` except `inquiry.php`.

Both `.htaccess` files use `mod_rewrite` for this blocking, not `Require`, on purpose: `Require` needs the AllowOverride `Limit` setting specifically, and on a host that doesn't grant it, `Require` inside `.htaccess` doesn't fail quietly — it makes Apache return a 500 error for **every** request in the affected directory, including legitimate ones (this would break the contact form entirely, not just leave something unprotected). `mod_rewrite` only needs `FileInfo`, which the redirects above already require, so if it's ever missing, the whole site is obviously broken rather than silently insecure. If your host somehow doesn't grant `FileInfo` either, nothing in this file will work at all — check with the smoke test in section 7.

- It uses `Options -Indexes` (a separate, unrelated AllowOverride setting, `Options`). If the host returns an "Internal Server Error" after upload, its `AllowOverride` setting does not allow that; delete the `Options -Indexes` line and turn off directory listing in the panel instead.
- If the domain ever changes, update the hostname in the two `RewriteCond` lines.
- The commented `Strict-Transport-Security` line should be enabled only after HTTPS is confirmed to work everywhere on the domain, because browsers remember it.

## 4. Track B: your own server (VPS)

Set up Apache (or Nginx) with PHP-FPM, a TLS certificate (Let's Encrypt), and mail delivery (the server's mail agent, or use the Resend driver, which needs nothing installed). Then:

1. Copy the contents of `site/` into the web root, and create `api/config.php` as in 3.4 (owned by the web user, permissions `640`).
2. **Apache:** enable `mod_rewrite`, `mod_headers`, `mod_expires`, `mod_deflate` and allow overrides (`AllowOverride All`) for the web root. The included `.htaccess` files then work as described in 3.7.
3. **Nginx:** `.htaccess` files are ignored, so add the equivalent rules. Starting point (**not tested here; check with `nginx -t`**):

```nginx
server {
    listen 80;
    server_name jaboassociates.business www.jaboassociates.business;
    return 301 https://jaboassociates.business$request_uri;
}

server {
    listen 443 ssl http2;
    server_name www.jaboassociates.business;
    # ssl_certificate lines ...
    return 301 https://jaboassociates.business$request_uri;
}

server {
    listen 443 ssl http2;
    server_name jaboassociates.business;
    # ssl_certificate lines ...

    root /var/www/jabo/site;
    index index.html;
    error_page 404 /404.html;
    client_max_body_size 8m;

    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header X-Frame-Options "DENY" always;
    add_header Permissions-Policy "camera=(), microphone=(), geolocation=()" always;

    location ~ /\. { deny all; }

    location = /api/inquiry.php {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;   # match your PHP version
    }
    location /api/ { deny all; }       # blocks config.php, outbox and rate data

    location ~* \.(webp|jpg|jpeg|svg)$ { expires 30d; }
    location ~* \.woff2$ { expires 1y; }
    location ~* \.(css|js)$ { expires 1d; }

    gzip on;
    gzip_types text/css application/javascript image/svg+xml;
}
```

4. In `php.ini` (or a pool file), set `upload_max_filesize = 6M` and `post_max_size = 8M`; `.user.ini` is read by PHP-FPM but check the `user_ini.filename` setting.
5. Repeat 3.5 and 3.6 on the server.

## 5. Track C: static-only host (no PHP)

Netlify, Vercel, GitHub Pages and similar serve the pages perfectly, but they cannot run `inquiry.php` (its source would be downloadable as text; it holds no secrets because `config.php` is never uploaded, but `netlify.toml` blocks the whole `api/` folder anyway, tidily). The form needs one of:

**Option 1: a form service** (Formspree, Web3Forms, Getform and similar; no code) — chosen for this project:
1. Sign up at the chosen service and create a form. Formspree: **Add Form**, give it a name (e.g. "Jabo & Associates inquiries"), set the destination to the real inquiry inbox, and copy the endpoint it gives you (`https://formspree.io/f/YOUR_FORM_ID`).
2. In `site/contact.html`, find the `<form id="inquiry-form" ...>` tag (an HTML comment sits right above it) and change `action="api/inquiry.php"` to that endpoint.
3. Nothing else needs to change: `contact.js` already sends the request with `Accept: application/json`, treats an HTTP success as delivered, and reads the error text whether the service returns it as `{ "error": "..." }`, `{ "message": "..." }` or Formspree's own `{ "errors": [{ "message": "..." }] }`. The page already carries a `_gotcha` hidden field, which is Formspree's honeypot convention, alongside the one `inquiry.php` uses — both are harmless no-ops to whichever backend doesn't recognise them.
4. Confirm your plan supports file uploads before relying on the "Supporting document" field — this varies by service and plan, and if it isn't supported the field should be removed from `contact.html` (ask for this to be done, since it also needs the accompanying `<label>` and PDF-check code in `contact.js` removed) rather than left in place silently failing.
5. Verify the destination email in Formspree's dashboard (it sends a confirmation link the first time) and send a real test inquiry once live.

**Option 2: port the handler** to the host's functions (Netlify Functions, Vercel Functions). The logic is short (validation, honeypot, timing check, PDF check, send through the Resend API) and can be ported from `inquiry.php`; ask for this to be done once you've decided against Option 1.

### 5.1 Deploying this repo to Netlify specifically

`netlify.toml` is already in the repo root and Netlify reads it automatically — you should not need to set anything by hand in the dashboard.

1. Netlify dashboard, **Add new site**, then **Import an existing project**, and connect the `oil-and-gas` GitHub repository.
2. **Branch to deploy:** this repo's only branch is `develop` (not `main`). Set Netlify's "Production branch" to `develop` — if the site was created before this was set correctly, open **Site configuration → Build & deploy → Branches** and fix it there; that alone is the most common reason a push doesn't appear to deploy at all.
3. Build settings: Netlify should read `publish = "site"` and no build command from `netlify.toml` automatically. If the dashboard shows a different publish directory (for example blank, or `.`), override it to `site` and redeploy.
4. Deploy. `netlify.toml` also sets the security headers, image/font/CSS/JS caching, and the `www` → bare-domain redirect equivalent to what `site/.htaccess` does for Apache (tested by validating the file's structure; not deployed and checked live here).
5. Connect the domain: **Domain settings**, add `jaboassociates.business`, and follow Netlify's DNS instructions (it can manage DNS for you, or give you records to add at your registrar). Netlify issues the HTTPS certificate automatically once DNS points to it.
6. Do Option 1 or 2 above before relying on the contact form — it will not work until one of them is done.

## 6. Point the domain

Do this after the files are on the host and working on a temporary address (many hosts give one).

1. **Copy every existing DNS record first**, especially MX and TXT records used by any email already on the domain.
2. At the registrar, either:
   - change the **nameservers** to the host's (some hosts do the DNS for you), or
   - keep DNS at the registrar and edit the records: an **A record** for `jaboassociates.business` to the host's IP address, and a **CNAME** (or A record) for `www` pointing to the bare domain. Your host will show the values.
3. Keep or re-create any MX records so company email keeps working. Send and receive a test email on the company address afterwards.
4. Wait for DNS to propagate (often minutes, sometimes hours). Check from a phone on mobile data too.
5. Issue or renew the HTTPS certificate once the domain resolves to the host.
6. Confirm `http://` and `www` redirect to `https://jaboassociates.business` (the included `.htaccess` does this on Apache).

## 7. Post-deploy smoke test

Do this on the live domain, on a phone and a desktop:

- [ ] `https://jaboassociates.business` loads; `www` and `http` redirect to it
- [ ] All 12 pages load, with no broken images and no console errors
- [ ] Header menu works on mobile; the floating WhatsApp button opens the right chat
- [ ] Team "Read full profile" dialogs open and close
- [ ] Every "Request a Quote" and "Partner With Us" link pre-fills the form
- [ ] Inquiry form: submit as Buyer (with a PDF), as Seller, and as Investor; each arrives in the inbox with the right details, reply-to and attachment
- [ ] Spam controls: submitting twice within seconds of page load shows the "take a moment" message; more than 5 inquiries in an hour from one connection are refused
- [ ] `/api/config.php` and `/.htaccess` are refused
- [ ] A wrong URL shows the branded 404 page with styling
- [ ] `/robots.txt` and `/sitemap.xml` load with the right domain
- [ ] Sharing the home page link in WhatsApp shows the preview image and description
- [ ] Company email still sends and receives
- [ ] The map loads on the contact page
- [ ] Run Lighthouse on mobile for Home and Products; note the scores in `QA-CHECKLIST.md`

## 8. Search engines

1. **Google Search Console:** add the domain (DNS TXT verification is best), then submit `https://jaboassociates.business/sitemap.xml`.
2. **Bing Webmaster Tools:** import the site from Search Console.
3. **Google Business Profile** for the Accra office (see `SEO-PLAN.md`).

## 9. Monitoring and routine care

| Task | How often |
|---|---|
| Check that inquiries are arriving and being answered within 48 hours | Daily at first, then weekly |
| Look for the "We could not send your inquiry" cause: the host's PHP error log (cPanel: **Errors** or `error_log` in the `api` folder) | On demand, and weekly at first |
| Check the mail provider's bounce reports (Resend dashboard if used, or the mailbox bounces) | Weekly |
| Renew the domain and hosting; check the registrar contact details | Yearly, and set reminders |
| Confirm the HTTPS certificate is renewing | Monthly |
| Search Console: errors, coverage, queries | Monthly |
| Keep PHP on a supported version (hosting panel setting) | Twice a year |
| Review content: team changes, licences, new photos | Quarterly |
| Rotate the Resend API key if it is ever exposed | As needed, or yearly |
| Delete old files in `api/rate/` if they grow (they clean themselves up) | Rarely |

## 10. Rollback

- **Bad content change:** re-upload the previous copy of the changed file (keep a dated backup before every upload, or use Git).
- **`.htaccess` causing errors (500):** rename it to `.htaccess.off` in the file manager; the site works without it (only the redirects and headers stop). Fix the line and rename it back.
- **Form broken:** as a stop-gap, edit the contact page message to direct visitors to WhatsApp and phone while you fix the cause. Restore the last working `config.php`.
- **DNS problem after changing records:** restore the previous nameservers or records from the copy taken in section 2.

## 11. Costs to expect

Check current pricing before committing. The certain costs are the domain renewal and the hosting plan. Email sending through `mail()` is included with most hosts; Resend has a free tier that covers a low volume of inquiries. Automatic WhatsApp alerts, if added later, carry WhatsApp Business API costs.

## 12. Launch-day checklist

- [ ] Client has approved the copy and the content changes
- [ ] Lawyer has reviewed Privacy, Terms, Disclaimer and the Exchange page wording
- [ ] Company email, registration number and office hours added to the pages
- [x] Kwabena Nyarko Twumasi's photo added
- [ ] Hosting chosen and set up; files uploaded, including the hidden files
- [ ] `api/config.php` created on the server with the real inbox and sender; driver is not `log`
- [ ] Test inquiry with a large PDF arrives (inbox, not spam); SPF and DKIM are valid
- [ ] `/api/config.php` and `/.htaccess` are refused
- [ ] DNS pointed; HTTPS valid; `www` and `http` redirect; company email verified
- [ ] Smoke test (section 7) passed
- [ ] Search Console and Business Profile set up
- [ ] Someone named to answer inquiries within 48 hours
