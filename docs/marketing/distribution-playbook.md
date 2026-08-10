# Distribution Playbook — Getting the First 100 Real Users

> Internal working doc, not for publication. Written against the actual position:
> listed 2026-03-12, ~0 active installs, 0 ratings, 0 support threads.

---

## The trap to avoid first

The instinct at 0 installs is to post "check out my new SEO plugin" everywhere. This mostly gets removed, and can get you banned from the exact communities you'll want later. Nearly every relevant subreddit and Facebook group treats a first-post promo as spam, regardless of the plugin's quality.

The pattern that works is unglamorous: **be visibly useful in a place for weeks before you mention what you built.** People install things made by someone they've seen be helpful. They ignore links from strangers.

## What the numbers say we actually need

Two separate problems, and they need different fixes:

1. **Nobody can find it.** wp.org search ranks on installs + ratings, so a 0/0 plugin is invisible for every term worth having. Fixed only by traffic from outside.
2. **Nobody trusts it.** 0 ratings on an SEO plugin is a hard stop for most people — a bad SEO plugin can quietly cost them traffic. Fixed only by the first ~10 genuine reviews.

Problem 2 is the harder one and depends on solving problem 1 first. **Getting to roughly 10 honest reviews is the single highest-value milestone available.** Below that, growth is manual; above it, wp.org search starts doing some of the work.

Reviews cannot be bought, traded, or solicited from friends who don't use the plugin — that violates WordPress.org guidelines and is grounds for removal from the directory. The only route is real users who chose to say something.

## Channels, ranked by realistic payoff

### 1. Your own content (highest control, compounds)
The two drafts in this folder. Publish on anupammondal.in, which is already linked from the plugin listing.

The `llms.txt` guide is the better bet: it targets a query with genuine and rising volume and almost no competition, and it answers the question completely whether or not the reader installs anything. Tutorial content that happens to mention your plugin outperforms content about your plugin.

Timeline: months, not days. This is the compounding one.

### 2. WP Tavern / WordPress news outlets
[WP Tavern](https://wptavern.com/) covers new and interesting plugins, and the AI/GEO angle is legitimately novel — very few SEO plugins ship `llms.txt` handling. A short, factual email to a writer describing what's genuinely new (not a press release) occasionally lands. Low probability, disproportionate payoff if it does.

### 3. Reddit — carefully
- **r/WordPress** — read the rules first; self-promo is restricted. Answering existing questions about SEO plugins, AI crawlers, or `llms.txt` is fine and puts you in front of the right people. Mention authorship explicitly if you reference your plugin.
- **r/ProWordPress** — smaller, more technical, more tolerant of "I built this" when there's substance.
- **r/SEO** — very skeptical of plugin promotion. Treat as read-only unless answering a genuine question.

Rule of thumb: if the comment would still be useful with your plugin link removed, it's fine to post. If removing the link makes it pointless, don't.

### 4. WordPress Facebook groups
"Advanced WordPress" and similar have large, active audiences and explicit self-promo rules — usually a designated day or thread. Follow them exactly; moderators are strict and bans are permanent.

### 5. Indie Hackers / Product Hunt / Dev.to
Lower relevance (audiences aren't primarily WordPress site owners) but the barrier is low and the AI/GEO story travels better here than a generic SEO plugin would. Dev.to is a reasonable second home for the `llms.txt` guide.

### 6. Do NOT use
- The WordPress.org support forums for promotion — fastest way to get the plugin pulled.
- Review swaps, incentivized reviews, review requests to non-users — all against directory guidelines.
- Comment links on other people's SEO posts.

## The in-plugin lever

The one thing on the product side that directly attacks the ratings problem: a **dismissible review prompt** after ~2 weeks of genuine use — shown once, easy to dismiss permanently, never blocking. This is standard, accepted practice and it's why competitors have thousands of ratings and you have zero. Currently listed as open work in STRATEGY.md Phase 5.

Ship that *before* the traffic arrives, not after. Users who install during a traffic spike and are never asked are a permanently missed opportunity.

## Honest expectations

- **Weeks 1–8:** near-flat. Content gets indexed, ranks nowhere yet. Manual channels produce single-digit installs.
- **Months 2–4:** if the `llms.txt` guide ranks, first steady trickle. First reviews become possible.
- **Months 4–8:** with ~10 reviews and a few hundred installs, wp.org search begins contributing and growth stops being entirely manual.

Anyone promising faster than this for a new plugin in the most saturated category on wp.org is selling something. The compounding is real, but it starts slowly, and the first 100 users are earned one at a time.
