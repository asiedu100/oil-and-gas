# Technical Documentation

Jabo & Associates corporate website, Phase 1. Plain HTML, CSS and JavaScript, with one serverless function for the inquiry form.

This guide is for whoever maintains the site. It explains how it is built, how to change things safely, and where the limits are. For what the site must do and why, see `PRD.md`. For going live, see `DEPLOYMENT-RUNBOOK.md`.

---

## 1. Overview

| | |
|---|---|
| Stack | HTML5, CSS3, vanilla JavaScript (no framework, no build step, no npm packages) |
| Pages | 12 static HTML files in `site/` |
| Backend | One PHP file: `site/api/inquiry.php` (the inquiry form handler) |
| Email | PHP `mail()` by default, or the Resend API (chosen in `site/api/config.php`) |
| Spam protection | Hidden honeypot field, a minimum-time check, and a per-visitor rate limit (no external service) |
| Hosting | **Not decided yet.** The site is host-agnostic; see `DEPLOYMENT-RUNBOOK.md` |
| Domain | jaboassociates.business |
| Fonts | Newsreader, Inter, Courier Prime, self-hosted as woff2 |
| Images | Responsive webp files, several widths per photo |
| URLs | Pages end in `.html` (the home page is `/`), so they work on every host with no server setup |

**How it works in one paragraph:** the browser loads plain HTML pages that share one stylesheet (`css/styles.css`) and two small scripts (`js/main.js` on every page, `js/contact.js` on the contact page). When a visitor submits the inquiry form, the browser posts it to `api/inquiry.php`. The PHP file checks the data and the spam signals, then emails the inquiry to the company inbox. Nothing is stored in a database.

## 2. Project structure

```
oil/
├── site/                         <- everything that gets published
│   ├── index.html                Home
│   ├── about.html
│   ├── team.html                 team cards and the bio dialogs
│   ├── products.html
│   ├── exchange.html
│   ├── services.html
│   ├── solar-ev.html
│   ├── contact.html              inquiry form
│   ├── privacy.html, terms.html, disclaimer.html
│   ├── 404.html                  uses root-absolute paths (/css/...)
│   ├── favicon.ico                multi-size icon (16/32/48), built from the real logo
│   ├── robots.txt
│   ├── sitemap.xml
│   ├── .htaccess                 Apache rules: HTTPS, www redirect, 404, headers, caching
│   ├── api/
│   │   ├── inquiry.php           form handler
│   │   ├── config.example.php    copy to config.php on the server (config.php is never committed)
│   │   ├── .htaccess             only inquiry.php may be requested from the web
│   │   └── .user.ini             lets the form accept a PDF up to 5 MB
│   ├── css/styles.css            the only stylesheet
│   ├── js/main.js                menu, footer year, team dialogs
│   ├── js/contact.js             form behaviour, pre-fill, timing signal
│   ├── fonts/                    three woff2 files
│   └── images/                   webp photos, logo-mark.webp, favicon-16/32.png, apple-touch-icon.png, og.jpg, team/
├── tools/optimize-image.py       turns a photo into site-ready webp files
├── docs/                         this documentation
├── .gitignore
└── (original photos)             loose JPEGs in the project root: source material, not published
```

Only the `site/` folder is published. The original photos in the project root (about 80 MB of Unsplash originals plus the team photos) are source material. Keep them out of the published folder and, if you use Git, either leave them out of the repository or move them to a `source-assets/` folder (already in `.gitignore`).

Files created at run time on the server, inside `site/api/` (never commit them; the `.htaccess` there blocks them from the web): `config.php` (your settings), `rate/` (rate-limit counters) and, only if you use the local-testing driver, `outbox/`.

## 3. Running the site locally

PHP's built-in server runs the whole site, including the form handler, with one command:

```bash
cp site/api/config.example.php site/api/config.php
# edit site/api/config.php and set 'mail_driver' => 'log' so emails are written to site/api/outbox/ instead of sent
php -S localhost:8000 -t site -d upload_max_filesize=6M -d post_max_size=8M
# open http://localhost:8000/index.html
```

Submit the contact form and open the `.eml` file it writes to `site/api/outbox/` in any mail program (or a text editor) to see exactly what the company would receive. Delete `config.php`, `outbox/` and `rate/` when you are done testing.

If you do not need the form, `python3 -m http.server 8000 -d site` serves the pages alone (the form will show its error message).

The built-in server ignores `.htaccess`, so the server rules are not exercised locally.

## 4. How the pages are built

Every page has the same skeleton:

1. `<head>`: title, description, canonical URL, Open Graph tags, font preloads, stylesheet, scripts (`main.js`, plus `contact.js` on the contact page).
2. Skip link, `<header class="site-header">`, `<main id="main">`, `<footer class="site-footer">`, floating WhatsApp button.

**The header and footer are repeated in every file.** This keeps the site free of tooling, but it means a change to navigation, footer text or contact details must be made in each file. See section 5.

The current page in the navigation is marked with `aria-current="page"` on its link; when you add or copy a page, move that attribute to the right link.

## 5. Common tasks

### Change a piece of text
Open the page in `site/` and edit the text. Use `&amp;` for `&`, and keep typographic apostrophes (`’`) consistent with the rest of the copy.

### Change contact details everywhere
The same details appear in several places. Use your editor's "Find in Files" across `site/` (or a shell command) so nothing is missed.

| Detail | Where it appears |
|---|---|
| Phone `+233 24 423 9557` and `tel:+233244239557` | footer of every page; `contact.html` panel |
| WhatsApp link `https://wa.me/233244239557?text=...` | footer of every page, the floating button on every page, `contact.html` panel |
| Email `info@jaboassociates.business` | footer of every page; `contact.html` panel; the three legal pages |
| Address | footer of every page; `contact.html`; the Organization data in `index.html` (`ld+json`); the map `src` in `contact.html` |
| Domain `https://jaboassociates.business` | canonical and `og:` tags on every page, `sitemap.xml`, `robots.txt`, `index.html` structured data, and the two `RewriteCond` lines in `site/.htaccess` |

Example, changing the email everywhere (Linux or macOS):

```bash
grep -rl "info@jaboassociates.business" site | xargs sed -i 's/info@jaboassociates.business/NEW@address/g'
```

On macOS use `sed -i ''` instead of `sed -i`. After a bulk change, search again for the old value and open a few pages to check.

Also update `to` in `site/api/config.php` on the server. That is where inquiries are actually sent, and it is separate from the address shown on the page.

### Add the registration number, licences or office hours
- **Registration number:** in the footer of each page there is an HTML comment showing where to add `Company registration no. XXXX.` after the copyright text.
- **Office hours:** `contact.html` has an HTML comment inside the contact panel showing where to add an "Office hours" block (copy one of the existing `<div><h3>…</h3><p>…</p></div>` blocks).
- **Licences:** no licence section exists yet. The simplest place is the About page or the footer; ask for wording from the client first.

### Edit or add a team member
Each person appears in three places:

1. `team.html`: a card (`<article class="team-card">`) with a "Read full profile" button whose `data-bio="slug"` matches a dialog.
2. `team.html`: the dialog `<dialog class="bio" id="bio-slug">` containing the full bio.
3. `index.html`: the leadership row (`<div class="person">`).

To add a person, copy an existing card, dialog and `.person` block and change the slug, name, title and text everywhere. The slug must be unique, lowercase and match between the button and the dialog `id` (`bio-` plus the slug).

**Add or replace a photo**

```bash
python3 tools/optimize-image.py KWABENA.jpeg team/kwabena --crop 0,0,700,700 --widths 250,720 --sizes "(max-width: 640px) 100vw, (max-width: 980px) 50vw, 33vw"
```

Pick the crop so the face is roughly centred in a square (left,top,right,bottom in pixels of the original). The script writes `site/images/team/kwabena-250.webp` and `kwabena-720.webp` and prints an `<img>` tag. Then replace the initials placeholder in the three places:

- Team card: `<div class="team-photo"><span class="initials" aria-hidden="true">KT</span></div>` becomes `<div class="team-photo">` plus the `<img>` tag with `alt="Full name"`.
- Dialog: the same, with `alt=""` and `sizes="92px"`.
- Home leadership row: `<span class="avatar"><img src="images/team/kwabena-250.webp" width="250" height="250" alt="" loading="lazy" decoding="async"></span>`.

Existing crops and files: `rev-jb`, `bernard`, `david`, `rev-gifty`, `adomako`. Kwabena is still on initials.

### Add or change products
The petroleum table is in `products.html` (`<table>` inside `.table-wrap`): each row is a group with the products as `<li>` pills. If you add a new product **group**, also add it to the "Commodity of interest" dropdown in `contact.html` so buyers can select it, and check the links that pre-fill it (`contact.html?commodity=...` must match the option text exactly).

### Change the commodity dropdown or the roles
Edit the `<option>` lists in `contact.html`. The role list is also checked on the server, so if you add or rename a role you **must** update `ROLES` in `site/api/inquiry.php`, or those submissions will be rejected.

### Add a new page
1. Copy a simple page such as `services.html` to `newpage.html`.
2. Change `<title>`, description, canonical URL, `og:` tags and the content.
3. Add the link to the header navigation and footer quick links in **every** page (and set `aria-current` on the new page's own header link).
4. Add the URL to `site/sitemap.xml`.

### Change a colour, font or spacing
Edit the variables at the top of `css/styles.css` (see section 7). Do not hard-code colours elsewhere.

### Update the numbers strip on the Home page
It is the `<div class="stats">` block in `index.html`. The client still has to confirm the figures.

## 6. Images

### Adding a photo
```bash
python3 tools/optimize-image.py ~/Downloads/photo.jpg tanker --alt "Fuel tanker at a port" --sizes "(max-width: 860px) 100vw, 50vw"
```
This creates `site/images/tanker-480.webp`, `-800`, `-1280` and `-1920` (never larger than the original) and prints a complete `<img>` tag. Needs Python 3 and `pip install pillow`.

Conventions used across the site:
- Every `<img>` has `width` and `height` (prevents layout jumps), `loading="lazy"` (except the top-of-page image, which uses `loading="eager" fetchpriority="high"`), `decoding="async"`, and a `srcset` and `sizes`.
- Content images have descriptive `alt` text. Decorative images (backgrounds, tiles with a heading next to them) use `alt=""`.
- Full-width backgrounds are `<img class="band-bg">` inside a `.section--photo` section (or `.cta-band`), not CSS backgrounds.
- Focal point is controlled with `style="object-position: X Y"`.

### Inventory and sources
All photos are from Unsplash (free to use under the Unsplash License; check the current terms before launch). The original file names in the project root carry the photographer and photo id.

| Name in `site/images/` | Subject | Original file |
|---|---|---|
| hero-port | Port cranes at sunset | foto-k-YTh-Yu_BEXw |
| petroleum-refinery | Refinery, blue sky | buddy-an-Ol9KJUYKVwg |
| gold-bars | Stacked gold bars | 3d-render-VAadjJpiW_Q |
| containers | Container port, aerial | ali-mkumbwa-Annl9CjEaEs |
| solar-farm | Solar farm in farmland | raphael-cruz-IwY-27ceRCA |
| tankers-aerial | Tankers at sea, aerial | made-from-the-sky-tZLkFrh2EII |
| port-cranes | Cranes loading a ship | julia-taubitz-2S2sVoteMqc |
| refinery-tanks | Refinery and storage tanks | road-ahead-cmfr_isw5Hc |
| solar-plant | Large solar plant, aerial | darmau-fo29TwLF4to |
| solar-panels | Solar panels, blue sky | soren-h-omfN1pW-n2Y |
| ev-station | EV at a night charging station | yrka-pictured-UUG01f0n_88 |
| ev-charging | Charging plug in an EV | chuttersnap-xJLsHl0hIik |
| offshore-platform | Offshore oil platform | gabriel-xavier-XquCLVbTYLE |
| tanker-truck | Tanker truck beside water | serhat-tug-BIuikf2Rlv0 |
| refinery-bw | Refinery towers, black and white | danny-burke-_hRakdmTtF8 |
| barges | Barges, aerial | siarhei-palishchuk-YJs7Mp_ow1Y |
| ev-home | EV beside a wall charger | zaptec-AbjcC_stuXs |
| solar-closeup | Solar panel close-up | benjamin-jopen-2SfssudtyIA |
| ev-plug | Hand plugging an EV | priscilla-du-preez-5AQSfYoLUT0 |

Also in `site/images/`: `logo-mark.webp` (cropped from the low-resolution business-card logo), `favicon-16.png` / `favicon-32.png` / `apple-touch-icon.png` (generated from `logo-mark.webp`, see below), `og.jpg` (the branded 1200×630 share image, see below), and `team/` (five team photos, each in two sizes). Two originals in the project root are unused: `kevin-musumbu-vnDzGR5Ji5g` and `soren-h-1PKAYeA_nZ4`.

Replace stock photos with the client's own when they are supplied. The two container photos show third-party carrier branding, so they are the first to swap.

### Favicon
`site/favicon.ico` (16/32/48px) and `site/images/favicon-16.png` / `favicon-32.png` / `apple-touch-icon.png` (180×180) are all generated from `site/images/logo-mark.webp`. Every page's `<head>` links all four:
```html
<link rel="icon" href="favicon.ico" sizes="any">
<link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32.png">
<link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16.png">
<link rel="apple-touch-icon" sizes="180x180" href="images/apple-touch-icon.png">
```
(`404.html` uses the same four links with a leading `/`, since it can be served at any URL.) The `apple-touch-icon.png` is also what `index.html`'s structured-data `logo` field points to — it's the one clean, square, opaque-background crop of the mark in the project, which is what schema.org and Google expect (not a photo or the wide share image).

To regenerate all four after a logo change:
```python
from PIL import Image
src = Image.open("site/images/logo-mark.webp").convert("RGB")
for sz in (16, 32):
    src.resize((sz, sz), Image.LANCZOS).save(f"site/images/favicon-{sz}.png")
src.save("site/favicon.ico", sizes=[(16, 16), (32, 32), (48, 48)])
canvas, pad = 180, 14  # ~8% padding so iOS's rounded-corner mask doesn't clip the mark
apple = Image.new("RGB", (canvas, canvas), "white")
apple.paste(src.resize((canvas - 2 * pad, canvas - 2 * pad), Image.LANCZOS), (pad, pad))
apple.save("site/images/apple-touch-icon.png")
```
Check the 16px result actually renders as a recognisable icon and isn't an illegible blur before committing — a highly detailed logo may need a tighter, higher-contrast crop at the smallest sizes.

### Social share image (`og.jpg`)
`site/images/og.jpg` is what appears when a page link is pasted into WhatsApp, Slack, iMessage, LinkedIn or X — every page's `og:image` and `twitter:image` point to it. It's a designed 1200×630 card (logo, wordmark, tagline, the Home page's headline, over a photo with the same dark gradient as the real hero), not a plain photo, built by rendering a small standalone HTML file with the site's own fonts and colours through headless Chromium and screenshotting it at the exact target size — the same technique used for this project's QA screenshots. There's no saved template for it in the repository; to update it (for a new headline, a sharper logo, or per-page images), recreate that HTML file reusing `site/css/styles.css`'s variables and `site/fonts/`, render it with Playwright at `viewport={"width": 1200, "height": 630}`, and save the screenshot over `site/images/og.jpg`.

## 7. Design system

All in `css/styles.css`, organised top to bottom: font-face rules, variables, base, layout, header, hero, components, footer, forms, image-led components.

### Variables (`:root`)

| Variable | Value | Use |
|---|---|---|
| `--gold` | `#DB9E07` | Main accent, buttons, rules |
| `--gold-deep` | `#7A5600` | Gold-coloured text on light backgrounds (passes contrast; plain gold does not) |
| `--red` | `#EA4932` | Rare accent (glow in banners, required-field asterisk) |
| `--ink` | `#111111` | Text and dark sections |
| `--cream` | `#FAF7ED` | Alternate section background |
| `--muted` | `#58554B` | Secondary text |
| `--line` | `#E7E1CF` | Borders |
| `--serif` | Newsreader | Headings |
| `--sans` | Inter | Body |

Wordmark uses Courier Prime Bold (`.brand-name`).

### Main components

| Class | What it is |
|---|---|
| `.container`, `.narrow` | Centred content column (max 1160 px; 760 px for `.narrow`) |
| `.section`, `.section--cream`, `.section--dark` | Page sections with vertical padding and background |
| `.section--photo` (+ `.soft`, `.band`) | Full-width photo section: needs an `<img class="band-bg">` as its first child. `.soft` uses a lighter left-to-right overlay |
| `.hero`, `.page-hero` | Home hero and inner-page hero (photo background with gradient overlay) |
| `.btn`, `.btn--gold`, `.btn--dark`, `.btn--outline` | Buttons |
| `.grid`, `.grid-2/3/4`, `.split`, `.split--reverse` | Layout helpers; `.split--reverse` puts the image first on wide screens |
| `.card`, `.card--plain`, `.glass` | Content cards; `.glass` is for use on photos |
| `.tiles` / `.tile` | Photo tiles (home business areas) |
| `.collage`, `.mosaic`, `.strip` | Image arrangements |
| `.steps` / `.step` | Numbered process (Products page) |
| `.table-wrap` + `table` | Products table (stacks into rows on phones) |
| `.team-card`, `dialog.bio`, `.people` / `.person` / `.avatar` | Team cards, bio dialog, home leadership row |
| `.cta-band`, `.cta-band--photo` | Closing call-to-action (with optional photo) |
| `.form`, `.form-row`, `.field`, `.form-status` | Inquiry form |
| `.wa-float` | Floating WhatsApp button (mobile only) |

### Breakpoints
1040 px (menu collapses), 980 (four-column grids become two), 860 (split layouts and footer stack; WhatsApp button appears), 760 (stats, mosaic and strips reflow; hero overlay becomes uniform), 640 (all grids single column; table stacks), 520 (tiles single column), 480 (tighter side padding).

### Accessibility features in the CSS/markup
Skip link, visible focus ring (`:focus-visible`), labelled inputs, `aria-live` status for the form, dialogs use the native `<dialog>` element (focus trapped, closes with Esc), `aria-current` on the active nav link, `aria-expanded` on the menu button, alt text on content images, gold text on light backgrounds uses the darker `--gold-deep`, and hover effects and smooth scrolling are switched off for visitors who ask their device for reduced motion.

## 8. JavaScript

There are two small files with no dependencies.

**`js/main.js` (every page)**
- Mobile menu: toggles `data-open` on `#site-nav` and updates `aria-expanded` and the button label.
- Footer year: writes the current year into `#year`.
- Team dialogs: `[data-bio="slug"]` buttons call `showModal()` on `#bio-slug`; the close button and clicks on the backdrop call `close()`.

**`js/contact.js` (contact page only)**
- Reads `?role=` and `?commodity=` from the URL and pre-selects matching options (used by the "Request a Quote" and "Partner With Us" links).
- Shows quantity, unit and delivery terms for Buyer and Seller / Supplier, and destination port for Buyer only.
- Records when the page loaded. On submit: checks the PDF (type and 5 MB), runs the browser's validation, posts a `FormData` to the form's `action` (`api/inquiry.php`) with an `elapsed` field and an `Accept: application/json` header, treats an HTTP success as delivered, and shows the returned `error` or `message` text on failure. Because it only relies on the HTTP status and that text, the form can be pointed at a form service instead (see the runbook, Track C).

The page has no JavaScript-free fallback for the form: without JavaScript the form would post to the endpoint and show raw JSON. This is acceptable for the target audience but is noted in section 13.

## 9. The inquiry form and handler

### Flow
1. Visitor fills the form on `contact.html`.
2. `contact.js` validates in the browser and posts to `api/inquiry.php`, adding an `elapsed` field: how many milliseconds the visitor spent on the page.
3. `inquiry.php` runs, in this order:
   1. Rejects anything that is not a POST (405) and detects an over-large upload (400, with a clear message).
   2. **Honeypot:** if the hidden `extra_info` field has anything in it, returns success without sending (silently drops bots).
   3. **Timing:** if `elapsed` is missing or under `min_seconds` (2.5 s by default), returns a visible message asking the visitor to review and submit again. The form keeps the visitor's data, so a genuine fast submitter just clicks Submit again.
   4. **Rate limit check:** if this visitor address has already reached `rate_limit` (5) sends in the last `rate_window` (one hour), reject with 429 here — cheaply, before any field validation or file handling, so an already-limited visitor never makes the server do real work.
   5. **Validation:** required fields; email format; phone must start with `+` and contain at least 7 digits or separators; role and commodity must each be one of the values the dropdowns offer.
   6. **Attachment:** at most 5 MB. The first 4 bytes are checked against `%PDF` before the rest of the file is read, so a non-PDF is rejected without buffering it into memory; the file name is sanitised (kept intact for a valid non-ASCII name, e.g. a CJK filename, and never overwritten for the edge case where the sanitised name is exactly `"0"`).
   7. Builds the email (plain text and HTML, every value escaped) and sends it. The visitor's email is set as reply-to, so replying goes straight to them.
   8. **Only on a successful send**, this attempt is recorded against the rate limit. A mistyped field, a validation failure, or a mail outage never consumes part of the allowance — only inquiries that were actually delivered count.
4. The handler returns JSON: `{ "ok": true }` (200), `{ "error": "..." }` (400, 405, 429, or 502 if sending failed).

### Fields

| Field (`name`) | Required | Notes |
|---|---|---|
| `name`, `company` | Yes | |
| `email` | Yes | |
| `phone` | Yes | Must include the country code, for example `+233 24 000 0000` |
| `country` | Yes | Dropdown |
| `role` | Yes | Buyer, Seller / Supplier, Mandate, Investor / Partner, Solar & EV client |
| `commodity` | Yes | Dropdown |
| `quantity`, `unit` | No | Shown for Buyer and Seller |
| `port` | No | Shown for Buyer |
| `terms` | No | FOB, CIF, CFR, DAP, Not sure |
| `message` | Yes | Up to 5000 characters |
| `document` | No | PDF up to 5 MB |
| `extra_info` | n/a | Honeypot; must stay empty |
| `elapsed` | Added by the page script | Milliseconds on the page; used for the timing check |

**Email received by the company:** subject `New inquiry: <role> / <commodity> / <company>`, a table of the details, then the message, with the PDF attached.

### Settings (`site/api/config.php`)

Copy `config.example.php` to `config.php` on the server and edit it. The same values can instead be supplied as environment variables `INQUIRY_TO`, `INQUIRY_FROM` (sender address), `RESEND_API_KEY` and `MAIL_DRIVER`, which override the file.

| Setting | Meaning |
|---|---|
| `to` | Inbox that receives inquiries |
| `from_email`, `from_name` | Sender shown on the email; use an address on the company's own domain |
| `mail_driver` | `mail` (PHP `mail()`, default), `resend` (Resend API, best delivery), or `log` (writes the email to `api/outbox/`; **local testing only**) |
| `resend_api_key` | Needed only for the `resend` driver |
| `min_seconds` | Fastest plausible human completion time (default 2.5) |
| `rate_limit`, `rate_window` | Inquiries allowed per visitor address per window in seconds (defaults 5 and 3600) |

### Requirements on the host
PHP 7.2 or newer; the `mail()` function enabled (or the Resend driver with outbound HTTPS); PHP upload limits of at least 6 MB and 8 MB post size (`api/.user.ini` sets these where supported). No PHP extensions beyond the standard ones are required; `curl` is used for Resend if available. If `curl` isn't installed, it falls back to PHP's `http://` stream wrapper, which silently depends on `allow_url_fopen = On` in `php.ini` — some hardened hosts turn this off. If neither is available, the Resend driver always fails (see Failure modes below).

### Failure modes
- **Email cannot be sent** (bad settings, blocked `mail()`, wrong Resend key, or — for the Resend driver specifically — no `curl` extension and `allow_url_fopen` disabled): the visitor sees "We could not send your inquiry right now" plus a pointer to WhatsApp and phone. The inquiry is **not** stored anywhere, so it is lost. The cause is written to the PHP error log (`error_log`); check it.
- **Emails land in spam:** enable SPF and DKIM for the domain in the hosting panel, send from an address on the domain, add a DMARC record, or switch to the Resend driver (see the runbook).
- **PDF refused as too large although it is under 5 MB:** the host's PHP limits are lower than `.user.ini` asked for; raise them in the hosting panel.
- **Role list out of sync** between `contact.html` and `ROLES` in `inquiry.php`: submissions are rejected with "Please choose who you are."
- **Rate limit blocks a genuine office:** many visitors behind one shared connection count as one address; raise `rate_limit` if this happens.
- **Visitor's device clock wrong:** not a problem; the timing check uses time measured in the page, not the clock time.

## 10. Security and privacy notes

- **Secrets:** the sender and inbox settings and the optional Resend API key live only in `api/config.php` on the server (or in environment variables). They are not in any HTML or JavaScript. `config.php` is listed in `.gitignore`, and it's blocked from the web **twice**: once by `site/.htaccess` at the root (by name, alongside `config.example.php`, `rate/` and `outbox/`) and once by `site/api/.htaccess` (everything in `api/` except `inquiry.php`). Both use `mod_rewrite` rather than `Require`, deliberately: `Require` needs the AllowOverride `Limit` setting, which this project doesn't otherwise need, and a host that doesn't grant it doesn't just leave the file exposed — it makes Apache return a 500 error for every request under the affected directory (tested locally; see the decision log). `mod_rewrite` only needs `FileInfo`, which the HTTPS redirects already require, so if these rules ever stop working, the site is visibly broken rather than quietly leaking. On Nginx, the equivalent `deny` rule is in the runbook. **After every deployment, open `/api/config.php` in a browser and confirm it is refused.**
- **Input handling:** every value is trimmed, checked to be valid UTF-8, stripped of control characters and length-limited; the email body escapes HTML; the subject and other one-line values cannot carry line breaks; the sender's email must pass PHP's email validator before it is used as reply-to; the attachment name is sanitised. There is no database, so no SQL injection surface, and no user content is rendered back into any page.
- **Uploads:** PDF only, 5 MB, verified by content signature. The file is forwarded as an email attachment and is not stored on the server. It is not virus-scanned, so the team should open attachments with care.
- **Spam:** honeypot, minimum time on the form, and a per-address rate limit (counters stored as small files in `api/rate/`, which clean themselves up). None of this needs an outside service. It stops simple bots and floods, but a determined human or a browser-driving bot can get through; if that becomes a problem, add a CAPTCHA service (hCaptcha, reCAPTCHA or similar) in front of the same handler.
- **Data held:** submissions live only in the company mailbox (and, briefly, the server's mail queue). The rate-limit files store only a hash of the visitor's address and timestamps. The Privacy Policy tells visitors what is collected and why, and says the site uses no advertising or tracking cookies. If you add analytics, use a cookie-free tool or update the policy.
- **Headers and HTTPS:** the included `.htaccess` sets HTTPS redirect and security headers on Apache; Nginx and static-host equivalents are in the runbook.
- **Third parties contacted by visitors' browsers:** Google Maps (contact page iframe) only. Everything else, including fonts, is self-hosted.
- **Legal pages:** drafted, not lawyer-reviewed.

## 11. Performance

Measured in headless Chromium on 28 September 2026, scrolling each page fully so lazy images load. Sizes are transferred bytes on a first visit and include CSS, fonts and images.

| Page | Desktop (1366 px) | Mobile (390 px) |
|---|---|---|
| Home | about 1.3 MB | about 0.6 MB |
| About / Team / Products | about 1.0 MB | about 0.2 to 0.3 MB |
| Exchange / Services | about 1.5 MB | about 0.2 to 0.3 MB |
| Contact | about 0.2 MB | about 0.06 MB |

Figures for later pages were taken in one browser session, so shared images were partly cached; treat the first row as the realistic upper bound. Techniques in use: responsive webp with `srcset`, lazy loading, explicit image dimensions, preloaded fonts, one small stylesheet (about 23 KB), and no framework code. A formal Lighthouse run is on the QA checklist.

## 12. Browser support

Written for current versions of Chrome, Edge, Firefox and Safari. The site uses the native `<dialog>` element, CSS grid, `clamp()`, `aspect-ratio`, `backdrop-filter` and webp; all are supported in current browsers. `backdrop-filter` gracefully degrades to a semi-transparent card. Only Chromium has been tested so far.

## 13. Known limitations and technical debt

1. **Header and footer are duplicated across 12 files.** Any navigation or contact change needs a bulk edit (section 5). If this becomes painful, the smallest fix is a tiny build script or a static-site generator; that is a deliberate future decision, not part of Phase 1.
2. **Inquiries are not stored.** If email delivery fails, the inquiry is lost. A future option is to also append each submission to a log file or a database on the server.
3. **No automatic WhatsApp notification.** Only click-to-chat links. Needs the WhatsApp Business API.
4. **The form needs JavaScript.**
5. **URLs end in `.html`** (for example `/about.html`). This works everywhere with no server setup. Cleaner addresses (`/about`) are possible on hosts with rewrite rules, but then the canonical tags, sitemap and links must all change together.
6. **No analytics, no cookie banner.** Not needed while nothing is tracked.
7. **Single language, no CMS.**
8. **Stock photography** and a logo cropped from a low-resolution image, until the client supplies originals.
9. **No security headers file yet** (recommended content is in the runbook).
10. **Team photo for Kwabena Nyarko Twumasi is missing.**

## 14. Troubleshooting

| Symptom | Likely cause and fix |
|---|---|
| Form says "Something went wrong" locally | The PHP server is not running, or you used the Python server. Use `php -S localhost:8000 -t site` and create `api/config.php` |
| "We could not send your inquiry right now" | `to` or `from_email` empty in `config.php`; `mail()` disabled on the host; wrong Resend key or a sender not on a verified domain. The reason is in the PHP error log |
| Test inquiry appears to succeed but nothing arrives (live site) | `mail_driver` is still `log`; or the email went to spam; check SPF and DKIM |
| "Please take a moment to review your inquiry" | The visitor submitted in under 2.5 seconds. Clicking Submit again works. If real users hit this, lower `min_seconds` |
| "Too many inquiries from your connection" | Rate limit reached for that address. Wait, or raise `rate_limit` in `config.php` |
| "The document must be 5 MB or smaller" for a smaller PDF | Host PHP limits are lower than 6 MB. Raise `upload_max_filesize` and `post_max_size` in the hosting panel |
| Every page shows a 500 error after uploading | The `.htaccess` is not accepted by the host. Rename it to `.htaccess.off`, confirm the site loads, then remove the `Options -Indexes` line (or the failing line) and restore it |
| Pages load but the form URL shows PHP source or downloads | The host does not run PHP (static-only host). Use a form service or port the handler (see the runbook, Track C) |
| `/api/config.php` opens in the browser | **Fix immediately.** The `.htaccess` in `api/` was not uploaded or is ignored; move the file out of the web root or add a deny rule; rotate any keys it contains |
| An image is blurry | Source is too small; re-run `tools/optimize-image.py` from a larger original |
| An image is missing | File name typo, or only some widths were generated; check the `srcset` names exist in `site/images/` |
| Team dialog does not open | `data-bio` slug does not match the dialog `id` (`bio-` plus slug) |
| Map is blank | Blocked by an ad or privacy extension, or the network blocks Google; open the page in a normal browser profile |
| Pre-filled dropdown is empty | The link's `commodity` or `role` value does not exactly match an option's text |
| 404 page has no styling on nested URLs | `404.html` must keep root-absolute paths (`/css/styles.css`) because it is served at any URL |

## 15. Decision log

| Date | Decision | Reason |
|---|---|---|
| 2026-09-28 | Corporate website only in Phase 1; exchange described as planned | Licensing and legal risk; per the client brief |
| 2026-09-28 | Initial build used the Astro framework | First proposal |
| 2026-09-28 | Rebuilt as plain HTML, CSS and JavaScript at the owner's request; Astro files removed | Owner's required stack; simpler hand-over |
| 2026-09-28 | Domain set to jaboassociates.business (the brief had .com) | Owner's instruction |
| 2026-09-28 | Canonical address is the bare domain (no `www`) | Simpler, one canonical host; reversible |
| 2026-09-28 | Photos used more heavily across pages, with brighter heroes | Owner feedback that images were under-used |
| 2026-09-28 | Automatic WhatsApp notification deferred | Requires WhatsApp Business API and templates; click-to-chat covers the need for now |
| 2026-09-28 | First proposed Cloudflare Pages, a Cloudflare function and Turnstile | Free tier, no server to run |
| 2026-09-28 | Owner will not use Cloudflare; hosting provider not yet chosen | Owner's instruction |
| 2026-09-28 | Form handler rewritten in PHP (`site/api/inquiry.php`); Cloudflare function and Turnstile removed | PHP runs on almost every host; no dependence on a specific platform |
| 2026-09-28 | Spam defence is honeypot plus timing check plus rate limit, all self-contained | No third-party service to sign up for; can add a CAPTCHA later |
| 2026-09-28 | Canonical URLs, sitemap and links use `.html` | Works on every host without rewrite rules; clean URLs can be added later on hosts that support them |
| 2026-09-28 | Included `.htaccess` for Apache hosts (HTTPS, www redirect, headers, caching, 404, private files) | Most likely host type; harmless where ignored |
| 2026-09-29 | Full code-review pass across PHP, HTML/CSS, JS and the image tool; every finding with a concrete, reproducible failure was fixed and tested (see the fixes below). Findings that were speculative, environment-dependent, or would trade the "no build step" requirement for a marginal cleanup (for example, using SSI to deduplicate the header/footer) were left as documented, deliberate trade-offs rather than changed | Owner asked to "fix every bug"; fixed what was demonstrably broken, documented what was a judgement call |
| 2026-09-29 | `site/.htaccess` and `site/api/.htaccess` rewritten to block dotfiles and the form handler's private files with `mod_rewrite` instead of `Require` | Tested on a simulated host granting `FileInfo` but not `Limit` (a real, plausible cPanel configuration): the old `Require`-based rules didn't just leave those files exposed, they made Apache return a 500 for every request in the affected directory — including `inquiry.php` itself, which would have silently broken the contact form in production. `mod_rewrite` only needs `FileInfo`, already required for the HTTPS redirects, so the same host that lets the rest of the site work also lets this work |
| 2026-09-29 | `inquiry.php`'s rate limiter split into a cheap early check and a record-on-success-only step | The previous single function recorded every attempt that passed validation, before the email was actually sent, so a mail outage plus a few natural retries could lock out a visitor who had never successfully sent anything — confirmed by deliberately breaking the mail config and sending repeated requests. It also read the full attachment into memory before this check could reject an already-limited request |
| 2026-09-29 | `inquiry.php`: directory creation (`rate/`, `outbox/`) uses a shared `ensure_dir()` that tolerates a losing race on `mkdir()` | Two requests arriving at the same instant on a fresh deploy could otherwise split one visitor's rate-limit counts across two different directories, quietly doubling their effective allowance |
| 2026-09-29 | `inquiry.php`: attachment filename sanitising uses a Unicode-aware pattern when the name is valid UTF-8, with a byte-level fallback otherwise; the "name became exactly `0`" case is preserved instead of falling back to `document.pdf` | Confirmed by test: a 2-character CJK filename was previously mangled into 6+ underscores, and a file literally named `0` was silently renamed, both due to PHP gotchas (`\w` byte-matching without `/u`, and `"0"` being falsy) |
| 2026-09-29 | `inquiry.php`: `commodity` is now validated against the same 9 options `contact.html` offers, matching how `role` was already checked | No server-side check meant a request that bypassed the form entirely could put arbitrary text into the outbound email's subject and body |
| 2026-09-29 | `site/products.html`: the "Request a Quote" button under the full petroleum table no longer pre-fills "Diesel & gas oils" | The table lists five different product groups; pre-filling one of them regardless of which the visitor actually wanted silently mis-filed part of every inquiry from that button |
| 2026-09-29 | `site/css/styles.css`: removed a dead, conflicting `.split-media img { aspect-ratio: 4 / 3.4 }` rule | A later, unconditional rule (`4 / 3.6`) already overrode it everywhere; the first was dead code that would mislead anyone editing it expecting an effect |
| 2026-09-29 | `tools/optimize-image.py`: `--crop` and `--widths` are validated, and an absolute `name` argument is rejected | Previously: a malformed `--crop` or `--widths` crashed with a raw Python traceback instead of a usable message, and a `name` that looked like a web path (e.g. `/hero`) silently wrote files under the filesystem root instead of `site/images` |

