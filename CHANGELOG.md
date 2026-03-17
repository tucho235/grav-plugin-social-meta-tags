# v0.3.0
## 03/16/2026

1. [](#new)
    * JSON-LD / Schema.org structured data support (`WebSite`, `Article`, `WebPage`) injected automatically before `</head>`
    * Per-page overrides via `social_meta` frontmatter block (title, description, type)
    * `og:locale` support — detects active Grav language and maps to locale format (e.g. `en_US`)
    * `og:image:width`, `og:image:height`, `og:image:alt`, `og:image:type` for richer image previews
    * `article:published_time` and `article:modified_time` tags for article pages
    * `og:type` is now dynamic: `website` for home page, `article` for all other pages
2. [](#improved)
    * Fixed `og:sitename` typo — corrected to `og:site_name` per Open Graph spec
    * `og:site_name` now uses `site.title` from Grav config instead of `$page->value('name')`
    * Description and image resolution now traverses modular page children when parent has no content
    * Added whitespace collapsing to `sanitizeMarkdowns()`
    * Increased description truncation limit from 140 to 280 characters

# v0.2.1
## (unreleased)

1. [](#improved)
    * Minor fixes

# v0.2.0
## 03/06/2017

1. [](#improved)
    * Remove mandatory dependencies to AboutMe plugin for twitter:site integration
    * Use summary for meta-tag description instead content for description

# v0.1.0
## 09/28/2016

1. [](#new)
    * ChangeLog started...
