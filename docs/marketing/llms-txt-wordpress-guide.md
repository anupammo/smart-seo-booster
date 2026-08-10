# How to Add an llms.txt File to WordPress (and Control AI Crawlers)

> **Draft for publication on anupammondal.in.** Target queries: "llms.txt WordPress",
> "how to add llms.txt", "block AI crawlers WordPress", "GPTBot robots.txt".
> These have low competition and rising volume — this is the piece most likely to rank.

---

AI assistants increasingly answer questions *about* your site without sending anyone to it. Two different levers control how that happens, and they're often confused with each other:

- **`llms.txt`** — a proposed file that gives AI models a clean, curated summary of your site so they represent it *accurately*.
- **`robots.txt` directives** — the existing, widely-respected mechanism that controls whether AI crawlers may access your content *at all*.

This guide covers both, manually and with a plugin.

## What llms.txt actually is (and what it isn't)

`llms.txt` is a Markdown file at the root of your domain — `example.com/llms.txt` — that describes what your site is and links to your most important content. The idea, [proposed in late 2024](https://llmstxt.org/), is that LLMs face the same problem search engines did: your HTML is full of navigation, cookie banners, and markup noise that obscures the actual content. A curated Markdown summary sidesteps that.

**Be realistic about it.** `llms.txt` is a *proposal*, not a ratified standard, and no major AI provider has publicly committed to consuming it. Anyone telling you it will transform your AI visibility is overselling. What it does do:

- Costs essentially nothing to publish.
- Gives you a single canonical place to state what your site is about, in your own words.
- Positions you if adoption does materialize.

Treat it as cheap insurance, not a growth strategy.

## What an llms.txt file looks like

The format is deliberately plain Markdown:

```markdown
# Acme Coffee Roasters

> Small-batch coffee roasted in Portland since 2011. Wholesale and direct.

## Pages
- [About](https://example.com/about/): Our sourcing and roasting process.
- [Wholesale](https://example.com/wholesale/): Pricing and minimums for cafés.

## Guides
- [Brew ratios](https://example.com/brew-ratios/): Recommended coffee-to-water ratios.
- [Storage](https://example.com/storage/): How to keep beans fresh after opening.
```

An H1 with the site name, a blockquote summary, then grouped links with one-line descriptions. That's the whole specification.

## Method 1: Create the file manually

1. Write the file in any text editor and save it as `llms.txt`.
2. Upload it to your WordPress root — the same folder as `wp-config.php` and `wp-load.php` — via SFTP or your host's file manager.
3. Confirm it loads at `https://yourdomain.com/llms.txt`.

**The catch:** it's now a static file. Every new post, every retitled page, every deleted URL makes it staler. For a five-page brochure site that's fine. For anything that publishes regularly, you'll stop updating it within a month.

## Method 2: Generate it automatically

A plugin can build the file from your actual content so it never goes stale. [Smart SEO Booster](https://wordpress.org/plugins/smart-seo-booster/) (free, and the plugin I build) does this under **Smart SEO → Settings → AI & GEO → Serve an llms.txt file**. It generates the file from your published pages and posts, using each item's meta description where you've set one.

*Disclosure: I'm the author of that plugin. The manual method above works perfectly well and costs nothing.*

If your permalinks are set to "Plain," virtual files like this won't resolve — visit **Settings → Permalinks** and click Save once to flush the rewrite rules.

## Controlling AI crawlers (this part is real and enforced)

Separate from `llms.txt`, and considerably more consequential: you can tell AI crawlers not to use your content for training. These directives go in `robots.txt`, and the major providers do honor them.

```
User-agent: GPTBot
Disallow: /

User-agent: Google-Extended
Disallow: /

User-agent: CCBot
Disallow: /

User-agent: ClaudeBot
Disallow: /

User-agent: PerplexityBot
Disallow: /

User-agent: Applebot-Extended
Disallow: /
```

What each one does:

| Agent | Controls |
|---|---|
| `GPTBot` | OpenAI's training crawler |
| `Google-Extended` | Whether Google may use your content for Gemini training — **does not affect Search ranking** |
| `CCBot` | Common Crawl, the dataset many models train on |
| `ClaudeBot` | Anthropic's crawler |
| `PerplexityBot` | Perplexity's crawler |
| `Applebot-Extended` | Apple Intelligence training (separate from `Applebot`, which handles Siri/Spotlight search) |

**Two things worth understanding before you paste that in:**

1. **Blocking training is not the same as blocking citation.** Some assistants fetch pages live to answer a question and cite the source. Blocking training crawlers can also cost you those referrals. If being cited as a source is valuable to you, blocking everything may be the wrong trade.
2. **`Google-Extended` does not affect Google Search.** Blocking it will not hurt your rankings. It only governs Gemini training. Many people avoid it out of an unfounded fear that it will.

WordPress serves a virtual `robots.txt`, so editing the file directly often doesn't stick. Either use the `robots_txt` filter in your theme's `functions.php`, or a plugin — Smart SEO Booster has a one-click **Ask AI crawlers not to train on this site** toggle in the same AI & GEO settings tab that adds all of the above.

## Verifying it worked

```bash
curl -s https://yourdomain.com/llms.txt | head
curl -s https://yourdomain.com/robots.txt
```

`llms.txt` should return Markdown with a `text/plain` content type. `robots.txt` should show your AI-crawler rules alongside the standard WordPress entries.

## The honest summary

Publishing `llms.txt` is a small, cheap bet on an unratified proposal — worth making, not worth overthinking. The AI-crawler directives are the part with real, immediate effect, and they deserve an actual decision: training opt-out protects your work, but may also cost you citations and the referral traffic that comes with them. Decide that deliberately rather than by default.
