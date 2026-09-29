# SEO Plan

Search engine optimisation for jaboassociates.business: what is already in the site, what to fix next, and what has to happen outside the site after launch.

**Be realistic about outcomes.** A new domain takes months to earn visibility, and fuel and commodity trading is a crowded field full of scam offers, so search engines and users are cautious. The strongest early wins are: appearing for the company's own name, appearing on the Google map for the Accra office, and being easy for a banker or buyer to verify. Nobody can promise rankings; the plan below is what gives the site a fair chance.

---

## 1. What is already in place

| Area | State |
|---|---|
| Page titles and descriptions | Unique on every page (titles 25 to 55 characters; descriptions 46 to 177 characters) |
| Canonical URLs | On every page, using `https://jaboassociates.business` (pages end in `.html`; the home page is `/`) |
| Open Graph tags | On every page, with a 1200×630 branded preview image (`images/og.jpg`: logo, wordmark, tagline and headline over a photo, not a plain stock crop) |
| Twitter Card tags | On every page (`summary_large_image`), mirroring each page's own title and description |
| Favicon | Real logo mark, not a placeholder: `favicon.ico` (16/32/48), `favicon-16.png`, `favicon-32.png`, and an `apple-touch-icon.png` (180×180) for iOS home-screen bookmarks |
| Headings | Exactly one H1 per page, with H2s beneath |
| Images | Descriptive alt text on content images; empty alt on decorative ones; explicit width and height; responsive webp; lazy loading below the fold |
| Speed | Self-hosted fonts, preloaded; one small stylesheet; no framework; first mobile visit about 0.6 MB on the home page |
| Mobile | Responsive layout and viewport tag; sticky WhatsApp button |
| Crawling | `robots.txt` pointing to `sitemap.xml` (11 URLs); the 404 page is `noindex` |
| Structured data | Organization (name, URL, phone, address) on the home page |
| Language | `lang="en"` on every page |
| Semantics | Landmarks (`header`, `nav`, `main`, `footer`), skip link, `aria-current` on the active link |

## 2. On-site improvements to make next

Ordered by value for effort. None are done yet.

### 2.1 Rewrite titles and descriptions (high value, 30 minutes)

Current titles such as "About | Jabo & Associates" say nothing about the business. Suggested replacements (titles are at most 60 characters and descriptions at most 155, so they should not be cut off in search results):

| Page | Suggested title | Suggested description |
|---|---|---|
| Home | Jabo & Associates \| Petroleum, Minerals & Green Energy | Ghana-based trading of petroleum products, gold and rare earth minerals, fertilizers, solar and EVs. Send an inquiry and hear back within 48 hours. |
| About | About Jabo & Associates \| Ghana Commodities Company | Founded in 2021 by partners with over ten years of West African commodity trading. Our mission, values and approach to fair, transparent trade. |
| Team | Our Team \| Jabo & Associates Leadership | Meet the leadership of Jabo & Associates: experience in development planning, fintech, oil and gas ventures, architecture and responsible tourism. |
| Products | Petroleum, Fertilizer & Gold Trading \| Jabo & Associates | Diesel, jet fuel, gasoline, fuel oils, bitumen, base oils, fertilizers and gold. Spot and contract supply with independent inspection. Request a quote. |
| Exchange | Commodities Exchange Platform \| Jabo & Associates | We are building a transparent commodities exchange for Africa. Learn about our planned platform and how to partner with us. Not yet live or licensed. |
| Services | Commodity & Trade Finance \| Jabo & Associates | Commodity finance, trade finance, price transparency, risk management and ESG support for traders and producers. Talk to our team. |
| Solar & EVs | Solar & EV Charging in West Africa \| Jabo & Associates | Hybrid solar systems with battery storage, plus EV charging for businesses in West Africa. Request a solar consultation. |
| Contact | Contact & Trade Inquiries \| Jabo & Associates | Buying, supplying or partnering? Send an inquiry to Jabo & Associates in Accra, Ghana. Call, WhatsApp or use the form. Reply within 48 hours. |

Edit both the `<title>`/`description` and the `og:title`/`og:description` tags in each page. Use `&amp;` for `&` inside HTML attributes.

### 2.2 Social sharing tags — done
Twitter Card tags (`summary_large_image`, title, description, image) are on every page, mirroring that page's own Open Graph title and description. All pages currently share one preview image (`images/og.jpg`, built from the real logo, wordmark and tagline over a photo, matching the site's actual colours and fonts). Giving the home page, Products and Solar & EVs their own image is still a worthwhile future improvement for richer link previews on LinkedIn and WhatsApp.

### 2.3 Strengthen structured data (medium value, 1 hour)
- The schema `logo` field now points to `images/apple-touch-icon.png` (a clean, square crop of the real logo mark), replacing the earlier stock photo. Swap it for a sharper file once the client supplies the logo source (see `CLIENT-APPROVALS.md`) — schema.org and Google both want a plain logo image, not a photo or a wide social-share card.
- Add `sameAs` links once the company has LinkedIn or other profiles.
- Add opening hours once confirmed.
- Add `BreadcrumbList` markup on inner pages.
- Validate in Google's Rich Results Test and the Schema Markup Validator after every change.

### 2.4 URL style (decided; revisit only if the host allows)
Pages use `.html` addresses (for example `/about.html`) in links, canonical tags and the sitemap, so every one of them works on any host with no server setup. Cleaner addresses (`/about`) are nicer to read but need rewrite rules on the server; if the chosen host supports them, switch the links, canonical tags and sitemap **together**, and redirect the old `.html` addresses to the new ones with a 301. Do not mix the two styles, or search engines will see duplicate pages.

### 2.5 Sitemap details
Add `<lastmod>` dates to `sitemap.xml` and update them when a page's content changes.

### 2.6 Content depth (ongoing)
Pages are short by design. Search engines and cautious buyers reward specific, verifiable detail. Candidates, only where the company can back them up:
- On Products: a short paragraph or section per product group describing typical specifications, standards (EN590, Jet A-1, GOST) and how quantity, port and delivery terms are handled.
- A "Verification and inspection" explanation (who inspects, what documents are required).
- On About: licences and registrations once supplied, and named leadership credentials (already on the Team page).
- A short "Insights" section later (market notes, trade FAQs). This is a new content programme, not part of Phase 1.

Rules for all copy: no superlatives that cannot be proved ("best", "guaranteed", "lowest price"); no keyword stuffing; keep the exchange wording as "planned".

### 2.7 Redirect the alternate address
Make sure `www.jaboassociates.business` redirects to the bare domain with a permanent (301) redirect, and that only one version is in the sitemap and canonical tags (see the deployment runbook).

## 3. Keyword and content themes

These are starting themes drawn from what the company sells, not measured search volumes. **Run real keyword research** (Search Console data after launch, Google Keyword Planner, or a paid tool) before investing in new content.

| Theme | Example phrases | Best page |
|---|---|---|
| Brand | Jabo & Associates, Jabo Associates Ghana | Home, About |
| Petroleum products | EN590 diesel, D2 diesel, Jet A-1, JP-54, bitumen, base oils SN150 and SN500, furnace oil, Mazut M100, petroleum coke | Products |
| Precious minerals | gold trading Ghana, rare earth minerals West Africa | Products |
| Fertilizers | urea, DAP, NPK supplier | Products |
| Finance | commodity finance, trade finance, letters of credit, inventory financing | Services |
| Exchange | African commodities exchange, commodity trading platform | Exchange |
| Solar and EV | solar installation Ghana, hybrid solar with battery storage, EV charging for business | Solar & EVs |
| Local | commodity trading company Accra, oil and gas company Accra | Home, Contact |

Buyers searching for bulk fuel are often targeted by scammers, so many will search the company name plus words such as "reviews" or "scam". Having a complete, consistent, verifiable web presence (registration number, licences, named team, real address, Business Profile, LinkedIn page) is the best answer.

## 4. Off-site and local SEO

| Action | Notes |
|---|---|
| **Google Search Console** | Add the domain, submit `sitemap.xml`, watch for errors and see what people search |
| **Bing Webmaster Tools** | Import from Search Console |
| **Google Business Profile** | Create it for the Dansoman office with the same name, address and phone as the site. Verification is by the method Google offers (often a postcard or phone). Add photos and the website link |
| **Consistent name, address, phone** | Use exactly "Jabo & Associates Company Limited", the same address and +233 24 423 9557 everywhere |
| **LinkedIn company page** | Link to the site; add the page to the site's structured data and footer |
| **Business directories** | Reputable Ghanaian and trade directories, the chamber of commerce, and industry associations the company genuinely belongs to. Avoid paid link schemes |
| **Partner and press mentions** | A link from a partner, a bank or a news story is worth more than many directory listings |
| **Domain choice** | `.business` carries no ranking disadvantage in Google, but some buyers instinctively trust `.com`. If `jaboassociates.com` is available or already owned, register it and redirect it to the main site (also protects against look-alike scams) |

## 5. Technical checklist

| Check | Status |
|---|---|
| HTTPS with valid certificate | After deployment |
| One canonical host, `www` redirect | After deployment |
| `robots.txt` and `sitemap.xml` reachable | Built; verify live |
| No accidental `noindex` on real pages | Verified in build |
| Titles and descriptions unique | Built; rewrite per 2.1 |
| Structured data valid | Test after deployment |
| Core Web Vitals in the green (mobile) | Run Lighthouse and PageSpeed Insights after deployment |
| Images have alt text; no broken images | Built; recheck after any edit |
| Mobile usability | Built; recheck in Search Console |
| No duplicate content across URLs | Canonicals set |
| 404 page returns a real 404 status | The included `.htaccess` does this on Apache (verified locally); check on the live host |
| Legal pages present and linked | Built; drafts |

## 6. Measurement

| What | Tool | Notes |
|---|---|---|
| Impressions, clicks, queries, indexing | Google Search Console | Free; check monthly |
| Visits, top pages, sources | A cookie-free analytics tool (for example Plausible, self-hosted Matomo configured without cookies, or the statistics in the hosting panel) | The Privacy Policy says the site uses no tracking cookies; keep that true or update the policy |
| Inquiries received | The company inbox, split by role and commodity | The most important number |
| Inquiry rate | Inquiries divided by contact page visits | Needs analytics; set a baseline in month 1 |
| Speed | PageSpeed Insights and Lighthouse | Re-run after adding images or scripts |

Report monthly: impressions and clicks for brand terms and product terms, number of inquiries by type, and any pages Google could not index.

## 7. Roadmap

| When | Do |
|---|---|
| Before launch | Rewrite titles and descriptions (2.1); add Twitter tags (2.2); decide on link style (2.4); obtain the logo file |
| Launch week | Deploy; verify redirects, HTTPS, sitemap; set up Search Console and Bing; create the Google Business Profile; create the LinkedIn page; validate structured data |
| Month 1 | Check indexing of all pages; fix any coverage errors; record baseline traffic and inquiries |
| Month 2 to 3 | Add licences and registration details; expand Products content (2.6); review search queries in Search Console and adjust titles; earn first partner and directory links |
| Ongoing | Keep content current; replace stock photos with real ones; review keywords quarterly; keep name, address and phone consistent everywhere |
