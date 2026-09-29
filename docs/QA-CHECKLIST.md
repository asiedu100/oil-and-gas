# QA Checklist and Test Record

Use this before launch and after every significant change. Tick boxes as you go and note the date, browser and who tested. Section 2 records what has already been tested; section 3 onward is what remains.

Legend: ☑ passed, ☐ not yet done, ✗ failed (log it in section 11).

---

## 1. Test environment

| Item | Value |
|---|---|
| Site under test | Local copy served from `site/`, then the live domain after deployment |
| Browsers to cover | Chrome, Edge, Firefox, Safari (macOS); Safari on iPhone; Chrome on Android |
| Screen widths | 360, 390, 768, 1024, 1366, 1920 px |
| Network | Fast, and throttled to "Fast 3G" once for the home page |

## 2. Results so far (28 September 2026)

Automated checks in headless Chromium against the local site, at 1366×850 (desktop) and 390×844 (phone).

| Check | Result |
|---|---|
| All 12 pages render at both sizes | ☑ |
| No horizontal scrolling on any page at either size | ☑ |
| No JavaScript errors in the console | ☑ |
| No failed requests (images, fonts, CSS, JS) other than Google Maps, which the test environment could not reach | ☑ |
| Fonts load: Inter, Newsreader and Courier Prime | ☑ |
| Mobile menu opens; floating WhatsApp button is visible on phone width | ☑ |
| Team bio dialog opens from "Read full profile" and closes with Esc | ☑ |
| Contact form: links pre-fill role and commodity; quantity and terms appear for Buyer and Seller, port only for Buyer; nothing for Investor or Mandate | ☑ |
| Contact form: empty submit is blocked by the browser; a non-PDF file is rejected with a message | ☑ |
| Contact form: success and error messages show correctly (server response simulated) | ☑ |
| Products table stacks into rows on phone width instead of scrolling sideways | ☑ |
| Form handler (`inquiry.php`, PHP 8.1 built-in server, emails written to a test folder): valid submission with PDF; missing field; bad email; email header injection attempt; phone without country code; unknown role; honeypot filled (no email sent); too-fast submission; missing timing field; file that is not really a PDF; PDF over 5 MB; wrong HTTP method; accented names; 6000-character message | ☑ all behaved correctly |
| Sent emails inspected: subject decoded correctly, reply-to set, plain-text and HTML parts present, PDF attached intact, HTML in the name and message escaped | ☑ |
| PHP `mail()` path exercised with a stand-in mail program: message delivered to it with the sender envelope set | ☑ (real delivery not yet tested) |
| Rate limit: 3 per hour test setting refused the 4th valid inquiry; five earlier invalid requests did not use up the quota | ☑ |
| Full browser test against the PHP handler: instant submit shows the "take a moment" message and keeps the visitor's data; retry after 3 seconds succeeds; form clears; exactly one email produced | ☑ |
| Apache rules (`.htaccess`, tested on a local Apache): `http` and `www` on the real domain redirect to `https://jaboassociates.business` with the query string kept; no redirect loop behind an HTTPS proxy; temporary addresses not redirected; branded 404; `config.php`, `config.example.php`, `api/rate/`, `.htaccess` and folder listings refused; `inquiry.php` allowed; security headers (also on the 404), file types, compression and caching correct | ☑ |
| Page weight measured (see the technical documentation, section 11) | ☑ |
| Visual review of every page, including new photo layouts and team photos | ☑ |

**Not yet tested:** real email delivery on the chosen host (and whether it lands in the inbox), Nginx and static-host configurations, the Google Map, Firefox, Safari, real phones, Lighthouse and accessibility audits, screen readers, keyboard-only walkthrough, slow networks, the live domain.

## 3. Navigation and content

| ID | Check | Result |
|---|---|---|
| N-01 | Every header link goes to the right page; the current page is highlighted | ☐ |
| N-02 | Logo returns to Home from every page | ☐ |
| N-03 | Header "Send an Inquiry" opens the Contact page | ☐ |
| N-04 | Every footer link works (quick links and the three legal pages) | ☐ |
| N-05 | Home tiles link to the right pages; "Precious & Rare Earth Minerals" lands on the minerals card on Products | ☐ |
| N-06 | Every "Request a Quote", "Partner With Us" and "Request a Solar Consultation" button opens Contact with the right selections | ☐ |
| N-07 | WhatsApp links open a chat with +233 24 423 9557 with the greeting filled in | ☐ |
| N-08 | Phone link dials on a phone; email links open a mail app | ☐ |
| N-09 | A wrong URL shows the branded 404 page with full styling | ☐ |
| N-10 | Run a link checker across the whole site and fix any broken links | ☐ |
| N-11 | Pages look right when printed (headers and footers do not break the text) | ☐ |

## 4. Inquiry form

Run these on the live site (and again after any change to the form or function). Check the receiving inbox each time.

| ID | Scenario | Expected | Result |
|---|---|---|---|
| FT-01 | Buyer, with quantity, unit, port, delivery terms and a small PDF | Success message; email arrives with every field, the reply-to set to the sender, and the PDF attached | ☐ |
| FT-02 | Seller / Supplier, no PDF | Success; email shows quantity and terms, no port | ☐ |
| FT-03 | Arrive from "Partner With Us" | Role preselected to Investor / Partner; no trade fields; email arrives | ☐ |
| FT-04 | Arrive from "Request a Solar Consultation" | Role and commodity preselected; email arrives | ☐ |
| FT-05 | Submit an empty form | Browser highlights required fields; nothing is sent | ☐ |
| FT-06 | Phone without a country code (for example `0244239557`) | Blocked with a helpful message | ☐ |
| FT-07 | Invalid email | Blocked | ☐ |
| FT-08 | Attach a `.docx` or image | Message: the document must be a PDF | ☐ |
| FT-09 | Attach a PDF over 5 MB | Message: 5 MB or less | ☐ |
| FT-10 | Rename a text file to `.pdf` and attach it | Server rejects it: "The document must be a PDF." | ☐ |
| FT-11 | Fill the hidden `extra_info` field (browser developer tools) | Site shows success but **no email** arrives | ☐ |
| FT-12 | Submit within about two seconds of the page loading | "Please take a moment to review your inquiry" message; the form keeps what was typed; submitting again a few seconds later works | ☐ |
| FT-13 | With a wrong Resend key, or mail switched off (test environment only) | Error message pointing to WhatsApp and phone; no email | ☐ |
| FT-14 | Message containing `<script>alert(1)</script>` | Arrives as plain text; nothing runs | ☐ |
| FT-15 | Message longer than 5000 characters | Accepted, truncated to 5000 characters | ☐ |
| FT-16 | Double-click Submit | Button disables; exactly one email | ☐ |
| FT-17 | Name with accents (François, Adjoa) | Shown correctly in the email | ☐ |
| FT-18 | After success | Form clears, trade fields hide, message is visible | ☐ |
| FT-19 | Reply from the company inbox | Goes to the visitor's email address | ☐ |
| FT-20 | Submit from a phone on mobile data | Works, page scrolls to the message | ☐ |
| FT-21 | Send more than the rate limit (5) valid inquiries within an hour from one connection | The extra ones are refused with the "Too many inquiries" message; the earlier ones were delivered | ☐ |
| FT-22 | Attach a PDF of about 4.9 MB | Accepted and delivered (proves the host's PHP upload limits are high enough) | ☐ |

## 5. Responsive and cross-browser

For each browser and width, check Home, Products, Team, Contact:

- ☐ No horizontal scrolling
- ☐ Menu works; text is readable; buttons are tappable (about 44 px)
- ☐ Images load and are not distorted; the hero text is legible over the photo
- ☐ Tiles, mosaic, strips and collage keep their arrangement (no gaps or overlaps)
- ☐ Team dialog opens, scrolls if long, and closes
- ☐ Products table stacks on phones
- ☐ The floating WhatsApp button does not cover a button or form field

| Browser | Desktop | Phone / tablet |
|---|---|---|
| Chrome | ☐ | ☐ |
| Edge | ☐ | n/a |
| Firefox | ☐ | ☐ |
| Safari | ☐ | ☐ (iPhone) |

## 6. Accessibility

Target: WCAG 2.1 AA.

| ID | Check | Result |
|---|---|---|
| A-01 | Keyboard only: tab through Home and Contact; order is logical; a focus ring is always visible | ☐ |
| A-02 | Menu button, team dialog (focus moves in, Esc closes, focus returns) and form all work by keyboard | ☐ |
| A-03 | Skip link appears on first Tab and jumps to the content | ☐ |
| A-04 | Run an automated audit (axe DevTools or Lighthouse accessibility) on every page; fix all errors | ☐ |
| A-05 | Screen reader pass (NVDA, VoiceOver or TalkBack) on Home, Team dialog and the form: labels, errors and status messages are announced | ☐ |
| A-06 | Colour contrast: check gold text on white is not used (only on dark backgrounds or as `--gold-deep`), and white text over every photo is readable | ☐ |
| A-07 | Zoom to 200% and enlarge text only: nothing is cut off or overlaps | ☐ |
| A-08 | Turn on "reduce motion" in the operating system: hover zooms and smooth scroll stop | ☐ |
| A-09 | Every content image has meaningful alt text; decorative images are empty | ☑ (checked in build) |
| A-10 | Form errors are understandable without colour alone | ☐ |

## 7. Performance

Run Lighthouse (mobile, in an incognito window) on the live site and record the scores.

| Page | Performance | Accessibility | Best practices | SEO | LCP (s) | CLS |
|---|---|---|---|---|---|---|
| Home | | | | | | |
| Products | | | | | | |
| Team | | | | | | |
| Contact | | | | | | |

Targets: scores of 90 or more; LCP 2.5 s or less; CLS 0.1 or less. If Performance is low, the likely causes are the largest photos (`hero-port-1920.webp` and the 1920 px images), so try a lower quality setting with `tools/optimize-image.py --quality 68`, or a smaller top width.

## 8. SEO validation

| ID | Check | Result |
|---|---|---|
| S-01 | `/robots.txt` and `/sitemap.xml` load on the live domain and use `jaboassociates.business` | ☐ |
| S-02 | View source of each page: title, description, canonical and `og:` tags are present and correct | ☑ (checked in build) |
| S-03 | One H1 per page | ☑ (checked in build) |
| S-04 | Google Rich Results Test passes for the home page structured data | ☐ |
| S-05 | Share the home page URL in WhatsApp, LinkedIn and X/Twitter: the **branded** preview image (logo, wordmark, tagline and headline — not a plain photo) appears with the right title and description on each | ☐ |
| S-06 | The browser tab, bookmarks bar and an iOS "Add to Home Screen" all show the real logo mark, not a generic placeholder icon | ☐ |
| S-07 | Search Console: property verified, sitemap submitted, no errors after a few days | ☐ |
| S-08 | `www` and `http` redirect to `https://jaboassociates.business` with a 301 | ☐ |
| S-09 | The 404 page has a `noindex` tag and returns a 404 status | ☐ |

## 9. Security and privacy

| ID | Check | Result |
|---|---|---|
| SEC-01 | The site loads only over HTTPS; the certificate is valid | ☐ |
| SEC-02 | Run the live domain through a headers checker after adding `_headers` (see the runbook) | ☐ |
| SEC-03 | No secrets in the repository or in what was uploaded: `api/config.php` is not in Git, and the Resend key (prefix `re_`) does not appear in any file under `site/` or in the Git history | ☐ |
| SEC-04 | In a browser, `/api/config.php`, `/api/config.example.php`, `/api/rate/` and `/.htaccess` are refused or not found; `config.php` exists only on the server, and its `mail_driver` is not `log` | ☐ |
| SEC-05 | The function returns generic error messages (no stack traces or keys) | ☑ (reviewed in code) |
| SEC-06 | Repeated rapid submissions do not flood the inbox (honeypot, timing check and rate limit; add a CAPTCHA if abuse appears) | ☐ |
| SEC-07 | The Privacy Policy matches what the site actually does (no tracking cookies; inquiries go by email only) | ☐ |
| SEC-08 | Open a test PDF attachment received by email safely; confirm attachments are not scanned automatically so staff know to be careful | ☐ |

## 10. Content review

Read every page once more on the live site with the client's approval sheet beside you.

- ☐ "François-David Haïck" spelled the same everywhere (or the client's correction applied)
- ☐ Exchange wording is "planned" everywhere; no page describes it as live or licensed
- ☐ No live prices, guarantees of returns or "limited loss" claims
- ☐ No birth dates, family details, hobbies or the health charity name in any bio
- ☐ No bank details anywhere
- ☐ Phone, address and email identical in the footer, Contact page, legal pages and structured data
- ☐ Footer registration number (CS201151021) matches the Certificate of Incorporation on file, on every page
- ☐ Licences added (or a recorded decision to omit)
- ☐ Trading-partner names on the About page ("Zibo Energy Group Co., Ltd." and "Isaiah Oil and Gas Limited") are spelled correctly and the client has confirmed they may be named publicly
- ☐ Legal name used in the footer, structured data and legal pages matches what the client confirms (certificate vs. "Jabo & Associates Company Limited")
- ☐ TIN (C0061274291) does not appear anywhere on the public site
- ☐ Numbers strip confirmed by the client
- ☐ No leftover placeholder text (search the files for "TODO", "XXXX", "lorem", "PASTE")
- ☐ No references to the old domain `jaboassociates.com` (search `site/`)
- ☐ Spelling and grammar pass (the copy mostly uses British spelling, such as "standardised" and "organisations", with "fertilizers" as in the client's document; keep it consistent)
- ☐ Every photo is licensed; container photos with third-party branding replaced if the client supplies photos
- ☐ The three legal pages have been reviewed by a lawyer

## 11. Defect log

| # | Date | Page | Browser and device | What happened | Severity | Fixed on | Retested |
|---|---|---|---|---|---|---|---|
| | | | | | | | |

Severity: **Blocker** (cannot launch), **Major** (visible or business-affecting), **Minor** (cosmetic).

## 12. Release sign-off

| Gate | Passed | By | Date |
|---|---|---|---|
| Sections 3 to 6 complete, no open Blocker or Major defects | ☐ | | |
| Performance and SEO sections complete | ☐ | | |
| Security and privacy section complete | ☐ | | |
| Content review and client approvals complete (`CLIENT-APPROVALS.md`) | ☐ | | |
| Post-deploy smoke test complete (`DEPLOYMENT-RUNBOOK.md`, section 9) | ☐ | | |
