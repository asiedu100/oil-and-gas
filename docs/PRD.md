# Product Requirements Document: Jabo & Associates Corporate Website (Phase 1)

| | |
|---|---|
| **Version** | 1.0 (draft for client review) |
| **Date** | 28 September 2026 |
| **Prepared by** | @Mrr_Asiedu, MRA Systems |
| **Client** | Jabo & Associates Company Limited, Accra, Ghana |
| **Domain** | jaboassociates.business |
| **Status** | Website built and reviewed locally; awaiting client inputs and deployment (see section 14) |

---

## 1. Summary

Jabo & Associates is a Ghana-registered company trading in petroleum products, precious and rare earth minerals, fertilizers, solar energy and electric vehicles. Its long-term vision is a transparent commodities exchange for Africa.

Phase 1 is a corporate website that presents the company credibly to buyers, sellers, partners and investors, and turns visitors into trade inquiries. It is not the exchange, and it must not read as if the exchange is live.

## 2. Problem and opportunity

- The company has no web presence. Serious buyers, banks and partners check a company online before they engage, and the fuel-trading sector is full of fake offers, so a trader with no website looks less trustworthy.
- Inquiries currently arrive through personal contacts and WhatsApp. There is no single place that says what the company trades, who runs it and how to start a trade.
- The client's source document contained wording that experienced buyers treat as a fraud signal (see `CLIENT-APPROVALS.md`). The website must present the same business in a credible way.

## 3. Goals and non-goals

### Goals
1. **Establish credibility.** Look like an international trading house: calm, confident, trustworthy.
2. **Explain the business clearly.** Four business areas, the products, how a trade works, and who the team is.
3. **Capture qualified inquiries.** A structured form that tells the team who is writing (buyer, seller, mandate, investor, solar client), what they want and how much.
4. **Set the right expectations about the exchange.** Present it as a vision under development, with wording that avoids legal risk.
5. **Be easy for the company to maintain** by a developer who works in plain HTML, CSS and JavaScript.

### Non-goals (Phase 1)
- Buyer accounts, LOI/ICPO submission workflow, KYC upload, deal dashboard (Phase 2).
- Any live trading, price feeds, futures, options, clearing or settlement (Phase 3).
- Live Platts prices (needs a paid licence).
- A content management system, blog or multi-language site.
- E-commerce or online payments. Bank details are never published.

## 4. Users

| Persona | What they want | What the site must give them |
|---|---|---|
| **Buyer or buyer's mandate** (fuel or fertilizer importer, trader) | To know the company is real, what it can supply, and how a purchase works | Product list, the four-step trade process, verification and inspection language, an inquiry form with quantity, port and delivery terms |
| **Seller or supplier** (refinery, producer, mineral seller) | To know the company can move product and who to contact | Business overview, team credentials, inquiry form |
| **Investor or partner** (including JV and exchange partners) | Vision, leadership, seriousness | About, Exchange page, team profiles, "Partner With Us" route |
| **Solar and EV client** (hotel, building owner, transport operator) | What is offered and how to start | Solar & EV page, "Request a Solar Consultation" route |
| **Due-diligence reviewer** (bank, compliance officer) | Registration details, licences, contact details, consistent story | Footer legal details, legal pages, consistent contact details, team bios |

All users are assumed to be business users on desktop or mobile. A large share will be on mobile, including WhatsApp users, so mobile must be first-class.

## 5. Scope and phases

| Phase | What it is | Status |
|---|---|---|
| **1. Corporate website** | 8 main pages plus 3 legal pages and a 404, team profiles, commodity listings, inquiry form that sends to email, WhatsApp link | **This PRD** |
| **2. Buyer inquiry portal** | Buyer accounts, LOI/ICPO submission, KYC document upload, admin dashboard to track each deal | Optional, quoted separately |
| **3. Exchange platform** | Live trading, price feeds, derivatives, clearing and settlement | Out of scope: needs SEC Ghana licensing, a licensed data feed and a specialist team |

## 6. Information architecture

| Page | File | Purpose | Primary action |
|---|---|---|---|
| Home | `index.html` | Tagline, four business areas, short about, team preview | Send an inquiry |
| About | `about.html` | Story, mission, vision, values, social responsibility | Meet the team |
| Our Team | `team.html` | Six profile cards, each opening a full bio | Contact us |
| Products & Commodities | `products.html` | Petroleum, fertilizers, precious and rare earth minerals; how a trade works | Request a quote |
| Commodity Exchange Platform | `exchange.html` | The vision and four principles, clearly marked as planned | Partner with us |
| Services | `services.html` | Commodity finance, trade finance, price transparency, risk management, ESG | Talk to our team |
| Solar Energy & EVs | `solar-ev.html` | Solar installations, EV charging and batteries, growth plans | Request a solar consultation |
| Contact | `contact.html` | Inquiry form, phone, email, WhatsApp, address, map | Submit inquiry |
| Privacy Policy, Terms of Use, Trade Disclaimer | `privacy.html`, `terms.html`, `disclaimer.html` | Legal protection and transparency | none |
| Not found | `404.html` | Recover lost visitors | Home or inquiry |

**Global elements:** header with logo, tagline and navigation; a gold "Send an Inquiry" button in the header; footer with logo, short company line, quick links, contact details, registration number (once supplied) and the three legal links; a sticky WhatsApp button on mobile.

## 7. Functional requirements

Priority: **M** = must, **S** = should, **C** = could. Status: **Built**, **Pending** (waiting on client input) or **Deferred**.

### 7.1 Site-wide

| ID | Requirement | Pri | Status |
|---|---|---|---|
| FR-01 | Header shows the logo mark, company name, tagline "Minerals \| Petroleum \| Green Energy", main navigation and an "Send an Inquiry" button; it stays visible while scrolling | M | Built |
| FR-02 | Navigation collapses into a Menu button on screens under about 1040 px wide | M | Built |
| FR-03 | Footer on every page: logo, short company line, quick links, address, postal address, phone, email, WhatsApp link, links to the three legal pages | M | Built |
| FR-04 | Footer shows the company registration number | M | Built: CS201151021 (client to confirm the legal name it should sit next to, see `CLIENT-APPROVALS.md`) |
| FR-05 | A floating WhatsApp button appears on mobile on every page and opens a chat with +233 24 423 9557 | M | Built |
| FR-06 | Every page has a clear inquiry call to action | M | Built |
| FR-07 | Custom 404 page with links home and to the inquiry form | S | Built |

### 7.2 Content pages

| ID | Requirement | Pri | Status |
|---|---|---|---|
| FR-10 | **Home:** hero with the headline and two buttons; numbers strip; four business-area tiles; short about; leadership row with photos; closing call to action | M | Built (numbers strip needs client confirmation) |
| FR-11 | **About:** story, mission, vision, four values, a trading-partners list, social responsibility | M | Built (current social projects pending; trading-partner names and public-naming approval pending, see `CLIENT-APPROVALS.md`) |
| FR-12 | **Team:** six cards with photo, name, title and short bio; "Read full profile" opens the full bio in a dialog that closes with the button, the backdrop or Esc | M | Built (5 of 6 photos in) |
| FR-13 | **Products:** four-step trade process; petroleum products grouped in five groups; fertilizers; precious and rare earth minerals; pricing note; "Request a Quote" | M | Built (mineral list pending) |
| FR-14 | **Exchange:** headline, intro, "in development, not live or licensed" notice, four principles, looking ahead, "Partner With Us" | M | Built |
| FR-15 | **Services:** commodity finance, trade finance, price transparency, risk management and hedging, sustainability and ESG | M | Built (direct vs partner-bank wording pending) |
| FR-16 | **Solar & EV:** solar installations, EV charging and battery storage, value chain, "Request a Solar Consultation" | M | Built |
| FR-17 | Call-to-action links pre-fill the inquiry form (role and commodity) where relevant | S | Built |
| FR-18 | Privacy Policy, Terms of Use and Trade Disclaimer pages | M | Built (draft, needs lawyer review) |
| FR-19 | Partner logos section | C | Deferred (needs logos and written permission; a text-only partner list is built under FR-11) |

### 7.3 Inquiry form

| ID | Requirement | Pri | Status |
|---|---|---|---|
| FR-20 | Fields: full name, company, country (dropdown), email, phone or WhatsApp with country code, "I am a" (Buyer, Seller / Supplier, Mandate, Investor / Partner, Solar & EV client), commodity of interest (product groups plus Other), message | M | Built |
| FR-21 | Quantity, unit (MT, barrels, litres) and delivery terms (FOB, CIF, CFR, DAP, Not sure) appear only for Buyer or Seller; destination port appears only for Buyer | M | Built |
| FR-22 | Optional supporting document: PDF only, up to 5 MB (LOI, ICPO or company profile) | M | Built for the PHP handler. On Netlify (using Formspree, see `DEPLOYMENT-RUNBOOK.md` Track C), this depends on the Formspree plan supporting file uploads — confirm before launch |
| FR-23 | Browser validation for required fields, email format, and international phone format; PDF type and size checked before upload | M | Built |
| FR-24 | The same validation is repeated on the server, including a check that the file really is a PDF | M | Built |
| FR-25 | A valid submission is emailed to the company inbox, with the visitor's email as reply-to, the details in a table and the PDF attached | M | Built; needs the host's mail settings in `config.php` to work live |
| FR-26 | Spam protection that needs no outside service: hidden honeypot field, minimum time on the form, and a per-visitor rate limit | M | Built |
| FR-27 | Clear success and error messages; on error the visitor is pointed to WhatsApp and phone | M | Built |
| FR-28 | Automatic WhatsApp notification to the team when an inquiry arrives | S | Deferred (needs WhatsApp Business API and approved message templates) |
| FR-29 | Response promise of 48 hours is shown on the site | M | Built (the company must honour it) |

### 7.4 Contact page extras

| ID | Requirement | Pri | Status |
|---|---|---|---|
| FR-30 | Office address, postal address, phone, WhatsApp button, general email | M | Built (email is a proposal) |
| FR-31 | Google Map of the Dansoman office | S | Built (verify in a real browser) |
| FR-32 | Office hours | S | Pending |
| FR-33 | Individual team emails are not published; the form goes to the general inbox | M | Built |

## 8. Content rules

These come from the client brief and the risk review. They apply to every future edit.

1. **The exchange is planned, not live.** Use "will offer" and "we are building". Change to present tense only for features the client shows are live and licensed.
2. **No live prices.** The site shows commodity names and invites a quote. Platts data needs a paid licence.
3. **No guarantees about trading outcomes.** The original "maximum potential loss is limited to the premium paid" line is removed.
4. **No personal details in bios:** no birth dates, family details, hobbies or the health charity name.
5. **Bank details never appear on the site.** They belong in contracts and invoices.
6. **Internal business-plan material stays off the public site:** pricing strategy, lead-generation channels, acquisition cost figures and unsourced market-size figures.
7. **No copyrighted images.** Use licensed stock (Unsplash) until the client supplies their own photos.
8. **Consistent name:** "François-David Haïck" everywhere, pending client confirmation.
9. **Wording that mimics fuel-offer templates is avoided** (for example "official refinery mandates under penalty of perjury"). See `CLIENT-APPROVALS.md`.

## 9. Design requirements

| Area | Requirement |
|---|---|
| Feel | International trading house: calm, confident, trustworthy, not flashy |
| Colour | Gold `#DB9E07` main accent; sunrise red `#EA4932` sparingly; near-black `#111111` for text and dark sections; white and warm off-white `#FAF7ED` backgrounds. Darker gold `#7A5600` for gold-coloured text on light backgrounds so it stays readable |
| Type | Serif headlines (Newsreader) and sans-serif body (Inter); the wordmark uses a bold typewriter face (Courier Prime) to echo the logo |
| Imagery | Large photos of tankers, ports, refineries, gold, solar farms and EV charging from licensed stock; photo-led sections on every page |
| Layout | Generous white space, short sections, an inquiry button on every page, sticky WhatsApp on mobile |
| Team page | Uniform square photo crops and consistent backgrounds |

## 10. Non-functional requirements

| Area | Requirement | Current position |
|---|---|---|
| **Performance** | Fast on mid-range phones over mobile data; images responsive and lazy-loaded | First visit weighs about 0.6 MB on mobile and up to about 1.5 MB on desktop; fonts self-hosted; no third-party scripts except the map. Formal Lighthouse run still to do |
| **Accessibility** | Aim for WCAG 2.1 AA: keyboard operable, visible focus, labelled form fields, alt text, sufficient contrast, skip link | Built in; formal audit still to do |
| **Browsers** | Current Chrome, Edge, Firefox and Safari (desktop and mobile) | Tested in Chromium only so far |
| **SEO** | Unique titles and descriptions, canonical URLs, sitemap, robots, structured data, one H1 per page | Built; see `SEO-PLAN.md` for gaps |
| **Security** | HTTPS only; no secrets in the repository; server-side validation; spam protection; safe handling of uploads | See `TECHNICAL-DOCUMENTATION.md` section 10 and the headers in `DEPLOYMENT-RUNBOOK.md` |
| **Privacy** | Collect only what is needed to answer an inquiry; no tracking cookies; comply with Ghana's Data Protection Act, 2012 (Act 843) | Privacy Policy drafted; lawyer review needed |
| **Reliability** | Static pages plus one small PHP handler | Depends on the chosen host and on email delivery (host mail or Resend) |
| **Maintainability** | Plain HTML, CSS and JavaScript; no build step, no framework | Achieved; header and footer are repeated in each page (see documentation) |
| **Portability** | Not tied to one hosting platform | Static files plus one PHP file; runs on typical PHP hosting or a VPS; a static-only host needs a form service or a port of the handler |
| **Language** | English only | As specified |

## 11. Technical decisions

| Decision | Choice | Reason |
|---|---|---|
| Stack | Plain HTML, CSS and JavaScript | Owner's requirement; nothing to install or build; easy to hand over |
| Hosting | **Not decided.** The site is host-agnostic: static files plus one PHP file | The owner has not chosen a host and does not want a Cloudflare dependency. `DEPLOYMENT-RUNBOOK.md` gives the host requirements and three tracks |
| Form backend | One PHP file (`site/api/inquiry.php`) | PHP is available on almost every host; settings and keys stay on the server, not in the browser |
| Email delivery | PHP `mail()` by default; the Resend API as an option | `mail()` needs no extra account; Resend gives better delivery if the host's mail is unreliable |
| Spam protection | Honeypot, minimum-time check and rate limit | No third-party account or cookie; a CAPTCHA can be added later if needed |
| URLs | Pages end in `.html` | Works on every host with no rewrite rules |
| Fonts | Self-hosted woff2 | No third-party requests; faster; private |
| Images | Responsive webp, several widths per photo | Small downloads on mobile |
| Earlier directions | The first build used Astro, then a Cloudflare-based form backend; both were replaced at the owner's request | Recorded in `TECHNICAL-DOCUMENTATION.md` |

## 12. Dependencies and assumptions

**Dependencies**
- Client inputs listed in `CLIENT-APPROVALS.md` (email, licences, photos, confirmations, sign-off).
- Access to the DNS for jaboassociates.business and knowledge of who hosts the company email, so the site can be connected without breaking email.
- A hosting plan with PHP, HTTPS and email (see the requirements in `DEPLOYMENT-RUNBOOK.md`); optionally a Resend account; and, recommended, Google Search Console and a Google Business Profile.

**Assumptions**
- The domain jaboassociates.business is registered to the client and can be pointed at the chosen host. The original brief mentioned jaboassociates.com; the owner has confirmed .business.
- The website address is the bare domain (`https://jaboassociates.business`), with `www` redirecting to it. This can be reversed.
- The company can respond to inquiries within 48 hours.
- Stock photography is acceptable until real photos exist.
- The proposed general email `info@jaboassociates.business` is a placeholder until confirmed.

## 13. Risks and mitigations

| Risk | Impact | Mitigation |
|---|---|---|
| Describing a regulated exchange as live before it is licensed | Legal and regulatory exposure (SEC Ghana) | All exchange wording is "planned"; explicit notice on the Exchange page; Trade Disclaimer; client to confirm with a lawyer |
| Copy that resembles fraudulent fuel-offer templates | Serious buyers and banks distrust the company | Rewritten as "refinery partners" with a verified four-step trade process |
| Fake or scam inquiries and spam | Wasted time; risk of fraud | Buyer verification step, proof-of-funds language, honeypot, timing check, rate limit, "warning against fraud" in the Trade Disclaimer |
| Personal data in bios or forms | Privacy and identity-fraud risk | Personal details removed; form collects business details only; privacy policy |
| Inquiry emails not delivered (mail blocked, spam folder, bad DNS) | Lost business | Enable SPF and DKIM for the domain (or use the Resend driver); send test inquiries before launch; monitor the inbox and the PHP error log; consider a second recipient |
| Stock photos misleading or copyrighted | Trust and legal issues | Only Unsplash-licensed photos; captions are area labels, not claims; container photos show third-party carrier branding, so replace with client photos when available |
| Low-resolution logo and headshots | Site looks less professional | Request the logo source file and better photos (see `CLIENT-APPROVALS.md`); current crops are fine at card size |
| Domain and email misconfiguration during go-live | Company email stops working | Copy existing DNS records before any change; keep MX records intact (see the deployment runbook) |
| Scope creep toward Phase 2 or 3 | Delays, cost | Phase boundaries are explicit in section 5; extra work is quoted separately |
| Host limits or changes (for example the host blocks PHP `mail()`, or has no PHP) | Form stops working | Resend driver as an alternative; handler is small and can be ported; the runbook lists host requirements and options |
| Host not chosen yet, and the wrong type is bought | Rework, delay | Check the requirement list in the runbook before buying; prefer standard PHP hosting |

## 14. Status and remaining work

**Done:** all pages and the form; responsive layout; images; SEO basics; documentation.

**Waiting on the client:** company email, office hours, licences held, photo of Kwabena Nyarko Twumasi, logo source file, confirmation of the numbers strip, mineral list, finance model wording, social responsibility projects, confirmation of the two trading-partner names and permission to name them publicly, which legal name to use (certificate vs. website), approval of the content changes.

**Before launch:** deploy to Netlify; get a Formspree account and endpoint and update `contact.html`; connect the domain and DNS; lawyer review of legal pages and Exchange wording; full QA pass (see `QA-CHECKLIST.md`); submit to Search Console.

**Decision needed:** automatic WhatsApp notification (FR-28) is not in the current build.

## 15. Success measures (proposed, to be agreed with the client)

| Measure | How it is measured | Target |
|---|---|---|
| Qualified inquiries per month | Count of inquiry emails, split by "I am a" | To be set by the client after the first month of data |
| Response time | Time from inquiry to first reply | Within 48 hours, as promised on the site |
| Inquiry completion rate | Form submissions divided by contact page visits (needs analytics) | Set a baseline first |
| Site quality | Lighthouse scores on mobile | 90 or higher for performance, accessibility, best practices and SEO |
| Search visibility | Impressions and clicks in Google Search Console for brand and product terms | Track from launch; set targets after 3 months |

## 16. Out of scope

Buyer accounts and deal tracking, live prices, exchange functionality, payments, CMS or blog, multiple languages, customer analytics dashboards, and any paid advertising or social media management.

## 17. Open questions

1. ~~Which hosting provider will be used?~~ Decided: Netlify, with Formspree for the inquiry form (see `DEPLOYMENT-RUNBOOK.md`, Track C). Still open: the Formspree endpoint, and whether its plan supports file uploads (`CLIENT-APPROVALS.md`, decision 8).
2. Which email address should receive inquiries, and who hosts the company email?
3. Is jaboassociates.business the only domain, and who controls its DNS?
4. Does the company provide finance directly or through partner banks?
5. Which precious and rare earth minerals should be listed?
6. Which licences does the company hold (NPA, Minerals Commission, SEC Ghana, others)?
7. Who gives final approval on copy and on the changes table?
8. Is "François-David Haïck" the correct spelling (the source document used two)?
9. Should the site link to social media accounts (LinkedIn, Facebook, others)? None are shown today.
10. Is an automatic WhatsApp alert worth the extra setup and cost?
11. Which legal name should the site use: the certificate's "JABO & ASSOCIATES LTD" or "Jabo & Associates Company Limited", used throughout today?
12. May Zibo Energy Group Co., Ltd. and Isaiah Oil and Gas Limited be named publicly on the site as trading partners, and is the spelling correct?

## 18. Glossary

| Term | Meaning |
|---|---|
| **LOI** | Letter of Intent: a buyer's written statement of intent to buy |
| **ICPO** | Irrevocable Corporate Purchase Order |
| **KYC** | Know Your Customer: identity and background checks |
| **Mandate** | An intermediary authorised to act for a buyer or seller |
| **SGS** | An independent inspection company used to verify cargo quality and quantity |
| **Platts** | Price benchmark provider widely used for oil pricing (paid licence for data) |
| **FOB, CIF, CFR, DAP** | Delivery terms that set who pays freight, insurance and risk at each stage |
| **ESG** | Environmental, social and governance standards |
| **MT** | Metric tonne |
| **NPA** | National Petroleum Authority (Ghana) |
| **SEC Ghana** | Securities and Exchange Commission, Ghana |
| **PV** | Photovoltaic (solar panels) |
| **EV** | Electric vehicle |
