# Jabo & Associates Website

Corporate website for **Jabo & Associates Company Limited** (Minerals | Petroleum | Green Energy), Accra, Ghana. Domain: **jaboassociates.business**.

Plain HTML, CSS and JavaScript. No framework and no build step. One PHP file sends the inquiry form to email. Hosting is not decided yet, and the site is built to run on any typical host.

Prepared by @Mrr_Asiedu, MRA Systems.

## Quick start

Run the whole site, including the form handler, with PHP's built-in server:

```bash
cp site/api/config.example.php site/api/config.php    # then set 'mail_driver' => 'log' for local testing
php -S localhost:8000 -t site -d upload_max_filesize=6M -d post_max_size=8M
# open http://localhost:8000/index.html
```

Test emails are written to `site/api/outbox/` instead of being sent. Delete `config.php`, `outbox/` and `rate/` afterwards. To view the pages only: `python3 -m http.server 8000 -d site`.

## What is where

| Path | What it is |
|---|---|
| `site/` | The website. Only this folder is published |
| `site/*.html` | 12 pages (Home, About, Team, Products, Exchange, Services, Solar & EVs, Contact, three legal pages, 404) |
| `site/css/styles.css` | The only stylesheet |
| `site/js/` | `main.js` (menu, team dialogs) and `contact.js` (form) |
| `site/images/`, `site/fonts/` | Optimised photos and self-hosted fonts |
| `site/api/inquiry.php` | Inquiry form handler (PHP). Settings go in `site/api/config.php`, copied from `config.example.php` |
| `site/.htaccess` | Apache rules: HTTPS, www redirect, 404 page, headers, caching |
| `tools/optimize-image.py` | Turns a photo into site-ready webp files and prints the `<img>` tag |
| `docs/` | Documentation (below) |

The loose JPEG files in this folder are original photos (source material) and are not published.

## Documentation

| Document | Read it for |
|---|---|
| [`docs/PRD.md`](docs/PRD.md) | Product requirements: goals, users, scope, requirements, risks, open questions |
| [`docs/TECHNICAL-DOCUMENTATION.md`](docs/TECHNICAL-DOCUMENTATION.md) | How the site is built and how to edit it: structure, common tasks, images, design system, form, limits, troubleshooting |
| [`docs/DEPLOYMENT-RUNBOOK.md`](docs/DEPLOYMENT-RUNBOOK.md) | Going live on any host: host requirements, three tracks (shared hosting, own server, static host), domain and DNS, email delivery, rollback, launch checklist |
| [`docs/CLIENT-APPROVALS.md`](docs/CLIENT-APPROVALS.md) | For the client: wording changes to approve, statements to confirm, information still needed, sign-off |
| [`docs/SEO-PLAN.md`](docs/SEO-PLAN.md) | What SEO is in place, what to improve, and the off-site plan |
| [`docs/QA-CHECKLIST.md`](docs/QA-CHECKLIST.md) | Test record and pre-launch checklist |

## Status (28 September 2026)

**Built:** all pages, the inquiry form, responsive layout, photography, team photos (five of six), SEO basics, documentation.

**Waiting on the client:** company email, office hours, licences held, Kwabena Nyarko Twumasi's photo, logo source file, which legal name to use (certificate vs. website), confirmation of the two trading-partner names, confirmations and sign-off (see `docs/CLIENT-APPROVALS.md`).

**Before launch:** choose a host (it needs PHP, HTTPS and email), deploy, create the form settings, connect the domain, lawyer review of the legal pages, full QA pass.

## Rules to remember when editing

- The exchange is **planned, not live**. Keep "will offer" and "we are building".
- No live prices, no bank details, no personal details in bios, no copyrighted images.
- The header and footer are repeated in every HTML file: change them everywhere.
- Roles in `contact.html` must match `ROLES` in `site/api/inquiry.php`.
- Never commit `site/api/config.php` or make it reachable from the web, and never use the `log` mail driver on the live site.
