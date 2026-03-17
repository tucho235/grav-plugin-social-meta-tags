# Social Meta Tags Plugin

The **Social Meta Tags** Plugin is for [Grav CMS](http://github.com/getgrav/grav).

## Description

Automatically injects social meta tags and structured data into every page — no template changes required. Supports Open Graph (used by Facebook, WhatsApp, Telegram, Discord, Slack, LinkedIn and others), X/Twitter Cards, and JSON-LD structured data for Google rich results.


## Features

- **[Open Graph](http://ogp.me/)** — full support including `og:image` dimensions, `og:locale`, `og:type`, and `article:published_time` / `article:modified_time`.
- **[X/Twitter Cards](https://developer.twitter.com/en/docs/twitter-for-websites/cards/overview/abouts-cards)** — Summary or Summary with Large Image.
- **[JSON-LD / Schema.org](https://schema.org/)** — structured data injected as `<script type="application/ld+json">` for Google rich results. Generates `WebSite`, `Article`, or `WebPage` depending on the page.
- **Modular pages** — automatically traverses child modules to find a description and image when the parent page has none.
- **Per-page overrides** — any page can override title, description, and type via frontmatter.
- **[AboutMe plugin](https://github.com/Birssan/grav-plugin-about-me)** integration for Twitter username (min version 1.1.4).


## Installation

### Via Grav Admin

Search for **Social Meta Tags** in the Admin panel under Plugins and install it directly.

### Via GPM

```
bin/gpm install social-meta-tags
```

### Manual

```
git clone https://github.com/tucho235/grav-plugin-social-meta-tags user/plugins/social-meta-tags
```


## Usage

Enable the plugin from the Admin panel or in `user/config/plugins/social-meta-tags.yaml`. No template changes are needed.


## Configuration

### Per-page overrides

Any page can override the values used for social tags by adding a `social_meta` block to its frontmatter:

```yaml
---
title: My Page
social_meta:
  title: "Custom title for social sharing"
  description: "Custom description for social sharing"
  type: "website"
---
```

Valid values for `type`: `article`, `website`, `webPage` (controls both `og:type` and the JSON-LD `@type`). The default is `website` for the home page and `article` for all other pages.

---

### Twitter / X account

Social Meta Tags can pull the Twitter username from the [AboutMe plugin](https://github.com/Birssan/grav-plugin-about-me) (min version 1.1.4). Enable the option in the plugin settings and configure your profile in AboutMe.

Alternatively, set your username directly in the plugin settings without using AboutMe.

---

### Facebook / Open Graph

To claim admin rights on your Open Graph Facebook page you need a Facebook App ID. Generate one at the [Facebook Developers page](https://developers.facebook.com/apps) and set it in the plugin configuration. Without it, Open Graph will still work but you will lose admin rights on the Facebook page.

---

### JSON-LD

Enabled by default. Injects a `<script type="application/ld+json">` block before `</head>` on every page. The structured data type is chosen automatically:

| Page | JSON-LD `@type` |
|---|---|
| Home page | `WebSite` |
| Regular pages | `Article` (includes `datePublished`, `dateModified`, `publisher`) |
| Custom via frontmatter `social_meta.type` | As specified |


## Contributing

If you think any implementation is not the best, feel free to submit ideas and pull requests. All comments and suggestions are welcome.
