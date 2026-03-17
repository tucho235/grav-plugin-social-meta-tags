# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A Grav CMS plugin that automatically injects Open Graph (Facebook) and Twitter Card meta tags into every page. No template changes are required — the plugin hooks into `onPageInitialized` and adds metadata programmatically.

## How the plugin works

The entry point is `social-meta-tags.php`. The plugin subscribes to `onPageInitialized` and calls two private methods:

- `getTwitterCardMetatags($meta)` — builds `twitter:*` tags; respects existing tags via `!isset()` checks, so page-level metadata overrides the plugin.
- `getFacebookMetatags($meta)` — builds `og:*` tags; always overwrites (no `!isset()` guard).

Both methods delegate to helpers for content resolution:

- `getPageDescription($page)` — returns `$page->summary()`, falling back recursively through `$page->children()` if empty (handles modular pages).
- `getPageImage($page)` — returns the first image from `$page->media()->images()`, falling back recursively through children.
- `sanitizeMarkdowns($text)` — strips Markdown syntax and truncates to 280 characters.

## Configuration files

- `social-meta-tags.yaml` — default config (Twitter enabled, Facebook disabled, empty `appid`).
- `blueprints.yaml` — Admin panel UI form definition. Field keys map directly to config paths (e.g. `social_pages.pages.twitter.enabled`).
- `languages.yaml` — i18n strings for the Admin panel UI (en, es, fr, de).

## Installation in a Grav site

Clone into the Grav plugins folder:
```
git clone https://github.com/tucho235/grav-plugin-social-meta-tags user/plugins/social-meta-tags
```

There is no build step, no composer, no npm. PHP files are loaded directly by Grav.

## Optional integrations

- **AboutMe plugin** (`grav-plugin-about-me` ≥ 1.1.4): when `twitter.aboutme: true`, the Twitter username is pulled from the AboutMe plugin config instead of `twitter.username`.
- **Facebook App ID**: required to claim admin rights on the Open Graph Facebook Page; configured via `social_pages.pages.facebook.appid`.

## Key config paths

| Config key | Purpose |
|---|---|
| `plugins.social-meta-tags.enabled` | Master on/off |
| `plugins.social-meta-tags.social_pages.pages.twitter.enabled` | Twitter Cards on/off |
| `plugins.social-meta-tags.social_pages.pages.twitter.type` | `summary` or `summary_large_image` |
| `plugins.social-meta-tags.social_pages.pages.twitter.aboutme` | Use AboutMe plugin for username |
| `plugins.social-meta-tags.social_pages.pages.twitter.username` | Fallback Twitter handle |
| `plugins.social-meta-tags.social_pages.pages.facebook.enabled` | Open Graph on/off |
| `plugins.social-meta-tags.social_pages.pages.facebook.appid` | Facebook App ID |
| `site.title` | Used as `og:site_name` value |
