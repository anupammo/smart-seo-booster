# Road to 1,000 Active Installs — Working Checklist

**Baseline, measured 2026-08-17 (WordPress.org plugin API):**

| Metric | Value |
|---|---|
| Listed since | 2026-03-12 (5 months) |
| Active installs | **0** |
| Ratings | **0** |
| Support threads | 0 |
| All-time downloads | 724 (almost entirely mirrors and scanners, not humans) |
| Current version | 2.2.3 |
| Plugin Check | Clean at maximum strictness |

> **The one sentence that matters:** the product is not the bottleneck. Every roadmap phase shipped, the build is verifiably clean, and installs stayed at zero. WordPress.org search ranks on installs and ratings, so a plugin with neither is invisible for every competitive term. Only traffic from *outside* wp.org breaks that loop.

---

## The two gates

Everything below serves one of these. Nothing else matters yet.

**Gate 1 — First ~10 real users.** Hand-earned. No shortcut exists.

**Gate 2 — First ~10 genuine reviews.** This is the compounding threshold. Below it, every install is manual; above it, wp.org search starts doing the work for you. Reviews cannot be bought, swapped, or solicited from non-users — that breaches the directory guidelines and risks removal.

---

## Phase 1 — Publish (Week 1)

Nothing here needs code. All of it is blocked only on you.

- [ ] **Publish the llms.txt guide** (`docs/marketing/llms-txt-wordpress-guide.md`) on anupammondal.in. Highest-probability ranker: real and rising query volume, almost no competition.
- [ ] **Publish the comparison post** (`docs/marketing/lightweight-seo-plugin-comparison.md`). Keep the authorship disclosure — it is what makes it credible.
- [ ] Internally link both from the plugin landing page and from any existing SEO posts.
- [ ] Submit both URLs in Google Search Console for indexing.
- [ ] Confirm the landing page is indexed: `site:anupammondal.in/wordpress-plugin/smart-seo-booster`.

## Phase 2 — Seed (Weeks 1–4)

- [ ] Answer 3–5 genuine questions/week on r/WordPress, r/ProWordPress, or the wp.org forums where llms.txt, AI crawlers, schema or Core Web Vitals come up. **Rule: if the comment would still be useful with your link removed, it is fine to post. If removing the link makes it pointless, do not.**
- [ ] Email one WP-news outlet (WP Tavern is the obvious one) with a short, factual note on the llms.txt/GEO angle — genuinely novel among SEO plugins. Not a press release.
- [ ] Post the llms.txt guide to a WordPress Facebook group **on its designated self-promo day**. Read the rules first; bans are permanent.
- [ ] Cross-post the guide to Dev.to.
- [ ] Ask 3–5 people you actually know with WordPress sites to install it and tell you honestly what breaks.

## Phase 3 — Convert (ongoing)

- [ ] Respond to every support thread within 48h. Response time visibly affects ratings, and ratings affect ranking.
- [ ] Fix whatever the deactivation survey reveals, and say so in the changelog. Visible responsiveness is itself a growth lever.
- [ ] Ship a small update at least every 60 days — users and the directory both reward recency.
- [ ] Re-check the wp.org tags quarterly; drop any that never produce impressions.

## Phase 4 — Compound (Months 3–12)

- [ ] Reach 10 ratings → wp.org search begins contributing.
- [ ] Add Google Search Console insights (the top-cited gap vs Rank Math).
- [ ] Add SEOPress / All in One SEO importers to widen the switching funnel.
- [ ] One new long-tail guide per month on the same pattern as the llms.txt post.

---

## Realistic timeline

Anchored on reviews and traffic, not on features.

| Milestone | Real trigger | Window |
|---|---|---|
| 0 → 10 | External content ranks, or a WP-news mention; every install hand-earned | Months 1–3 from first publishing |
| 10 → 50 | First ~5 genuine reviews arrive via the in-plugin prompt | Months 3–6 |
| 50 → 500 | 10+ ratings; wp.org search starts contributing instead of burying you | Months 6–12 |
| 500 → 1,000 | Long-tail tags earn their own traffic; category presence | Months 12–18 |

**"ASAP" honestly:** 1,000 active installs is a 12–18 month goal in the most saturated category on wp.org, starting from zero with no reviews. Anyone promising faster is selling something. The first 100 users are earned one at a time; after that it compounds.

---

## What is already done (do not redo)

- ✅ Product complete — schema, sitemaps, breadcrumbs, redirects, WooCommerce, Local SEO, Page Speed/Core Web Vitals, FAQ/HowTo/TOC blocks, Flesch readability, 17 languages
- ✅ Plugin Check clean at max strictness, verified against a live WordPress install
- ✅ CI runs lint (PHP 7.4–8.3), Plugin Check, activation and debug-notice gates on every push
- ✅ Listing optimised — tags retargeted to winnable terms, title/description within limits, 13 screenshots, 17 FAQ entries
- ✅ In-plugin review prompt (delayed, gated on real use, permanently dismissible)
- ✅ Deactivation survey to capture why people leave
- ✅ Post-update notice linking to the changelog

## Things that will get the plugin removed — never do these

- Buying, swapping, or incentivising reviews
- Soliciting reviews from people who do not use the plugin
- Promoting in the wp.org support forums
- Redirecting users to an external site on activation or update (see README note)
- Any tracking or phone-home without explicit opt-in consent
