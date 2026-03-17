<?php
namespace Grav\Plugin;

use Grav\Common\Plugin;
use RocketTheme\Toolbox\Event\Event;


/**
 * Class SocialMetaTagsPlugin
 * @package Grav\Plugin
 */
class SocialMetaTagsPlugin extends Plugin
{
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }

    public function onPluginsInitialized()
    {
        if (!$this->isAdmin() and $this->config->get('plugins.social-meta-tags.enabled')) {
            $this->enable([
                'onPageInitialized' => ['onPageInitialized', 0],
                'onOutputGenerated' => ['onOutputGenerated', 0],
            ]);
        }
    }

    public function onPageInitialized(Event $e)
    {
        $page = $this->grav['page'];
        $meta = $page->metadata(null);
        $meta = $this->getTwitterCardMetatags($meta);
        $meta = $this->getFacebookMetatags($meta);
        $page->metadata($meta);
    }

    public function onOutputGenerated()
    {
        if (!$this->grav['config']->get('plugins.social-meta-tags.jsonld.enabled')) {
            return;
        }
        $page   = $this->grav['page'];
        $jsonLd = $this->buildJsonLd($page);
        if ($jsonLd) {
            $script = "\n" . '<script type="application/ld+json">' . $jsonLd . '</script>';
            $this->grav->output = str_replace('</head>', $script . "\n</head>", $this->grav->output);
        }
    }

    private function getTwitterCardMetatags($meta)
    {
        if (!$this->grav['config']->get('plugins.social-meta-tags.social_pages.pages.twitter.enabled')) {
            return $meta;
        }

        $page = $this->grav['page'];

        if (!isset($meta['twitter:card'])) {
            $meta['twitter:card']['name']     = 'twitter:card';
            $meta['twitter:card']['property'] = 'twitter:card';
            $meta['twitter:card']['content']  = $this->grav['config']->get('plugins.social-meta-tags.social_pages.pages.twitter.type');
        }

        if (!isset($meta['twitter:title'])) {
            $meta['twitter:title']['name']     = 'twitter:title';
            $meta['twitter:title']['property'] = 'twitter:title';
            $meta['twitter:title']['content']  = $this->sanitizeMarkdowns(
                $this->getHeaderValue($page, 'title') ?? $page->title()
            );
        }

        if (!isset($meta['twitter:description'])) {
            $desc = $this->getHeaderValue($page, 'description') ?? $this->getPageDescription($page);
            $meta['twitter:description']['name']     = 'twitter:description';
            $meta['twitter:description']['property'] = 'twitter:description';
            $meta['twitter:description']['content']  = $this->sanitizeMarkdowns(strip_tags($desc));
        }

        if (!isset($meta['twitter:image'])) {
            $image = $this->getPageImage($page);
            if ($image !== null) {
                $meta['twitter:image']['name']     = 'twitter:image';
                $meta['twitter:image']['property'] = 'twitter:image';
                $meta['twitter:image']['content']  = $this->grav['uri']->base() . $image->url();
            }
        }

        if (!isset($meta['twitter:site'])) {
            if ($this->grav['config']->get('plugins.social-meta-tags.social_pages.pages.twitter.aboutme')) {
                if ($this->grav['config']->get('plugins.aboutme.social_pages.enabled')
                    and $this->grav['config']->get('plugins.aboutme.social_pages.pages.twitter.url')) {
                    $user = preg_replace('((http|https)://twitter.com/)', '@', $this->grav['config']->get('plugins.aboutme.social_pages.pages.twitter.url'));
                } else {
                    $user = '';
                }
            } else {
                $user = '@' . $this->grav['config']->get('plugins.social-meta-tags.social_pages.pages.twitter.username');
            }
            $meta['twitter:site']['name']     = 'twitter:site';
            $meta['twitter:site']['property'] = 'twitter:site';
            $meta['twitter:site']['content']  = $user;
        }

        return $meta;
    }

    private function getFacebookMetatags($meta)
    {
        if (!$this->grav['config']->get('plugins.social-meta-tags.social_pages.pages.facebook.enabled')) {
            return $meta;
        }

        $page  = $this->grav['page'];
        $type  = $this->getPageType($page);
        $image = $this->getPageImage($page);

        $meta['og:site_name']['name']     = 'og:site_name';
        $meta['og:site_name']['property'] = 'og:site_name';
        $meta['og:site_name']['content']  = $this->grav['config']->get('site.title');

        $meta['og:title']['name']     = 'og:title';
        $meta['og:title']['property'] = 'og:title';
        $meta['og:title']['content']  = $this->sanitizeMarkdowns(
            $this->getHeaderValue($page, 'title') ?? $page->title()
        );

        $meta['og:description']['name']     = 'og:description';
        $meta['og:description']['property'] = 'og:description';
        $meta['og:description']['content']  = $this->sanitizeMarkdowns(strip_tags(
            $this->getHeaderValue($page, 'description') ?? $this->getPageDescription($page)
        ));

        $meta['og:type']['name']     = 'og:type';
        $meta['og:type']['property'] = 'og:type';
        $meta['og:type']['content']  = $type;

        $meta['og:url']['name']     = 'og:url';
        $meta['og:url']['property'] = 'og:url';
        $meta['og:url']['content']  = $this->grav['uri']->url(true);

        $locale = $this->getPageLocale();
        if ($locale) {
            $meta['og:locale']['name']     = 'og:locale';
            $meta['og:locale']['property'] = 'og:locale';
            $meta['og:locale']['content']  = $locale;
        }

        if ($image !== null) {
            $meta['og:image']['name']     = 'og:image';
            $meta['og:image']['property'] = 'og:image';
            $meta['og:image']['content']  = $this->grav['uri']->base() . $image->url();

            $width  = $image->get('width');
            $height = $image->get('height');

            if ($width) {
                $meta['og:image:width']['name']     = 'og:image:width';
                $meta['og:image:width']['property'] = 'og:image:width';
                $meta['og:image:width']['content']  = $width;
            }
            if ($height) {
                $meta['og:image:height']['name']     = 'og:image:height';
                $meta['og:image:height']['property'] = 'og:image:height';
                $meta['og:image:height']['content']  = $height;
            }

            $meta['og:image:alt']['name']     = 'og:image:alt';
            $meta['og:image:alt']['property'] = 'og:image:alt';
            $meta['og:image:alt']['content']  = $page->title();

            $ext     = strtolower(pathinfo($image->url(), PATHINFO_EXTENSION));
            $mimeMap = [
                'jpg'  => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png'  => 'image/png',
                'gif'  => 'image/gif',
                'webp' => 'image/webp',
            ];
            if (isset($mimeMap[$ext])) {
                $meta['og:image:type']['name']     = 'og:image:type';
                $meta['og:image:type']['property'] = 'og:image:type';
                $meta['og:image:type']['content']  = $mimeMap[$ext];
            }
        }

        if ($type === 'article') {
            if ($page->date()) {
                $meta['article:published_time']['name']     = 'article:published_time';
                $meta['article:published_time']['property'] = 'article:published_time';
                $meta['article:published_time']['content']  = date('c', $page->date());
            }
            if ($page->modified()) {
                $meta['article:modified_time']['name']     = 'article:modified_time';
                $meta['article:modified_time']['property'] = 'article:modified_time';
                $meta['article:modified_time']['content']  = date('c', $page->modified());
            }
        }

        $meta['fb:app_id']['name']     = 'fb:app_id';
        $meta['fb:app_id']['property'] = 'fb:app_id';
        $meta['fb:app_id']['content']  = $this->grav['config']->get('plugins.social-meta-tags.social_pages.pages.facebook.appid');

        return $meta;
    }

    private function buildJsonLd($page)
    {
        $type      = $this->getPageType($page);
        $title     = $this->getHeaderValue($page, 'title') ?? $page->title();
        $desc      = strip_tags($this->getHeaderValue($page, 'description') ?? $this->getPageDescription($page));
        $url       = $this->grav['uri']->url(true);
        $siteTitle = $this->grav['config']->get('site.title');
        $image     = $this->getPageImage($page);
        $imageUrl  = $image ? $this->grav['uri']->base() . $image->url() : null;

        if ($type === 'website') {
            $data = [
                '@context' => 'https://schema.org',
                '@type'    => 'WebSite',
                'name'     => $siteTitle,
                'url'      => $url,
            ];
        } elseif ($type === 'article') {
            $data = [
                '@context'  => 'https://schema.org',
                '@type'     => 'Article',
                'headline'  => $title,
                'url'       => $url,
                'publisher' => [
                    '@type' => 'Organization',
                    'name'  => $siteTitle,
                ],
            ];
            if ($desc)     $data['description']   = $desc;
            if ($imageUrl) $data['image']          = $imageUrl;
            if ($page->date())     $data['datePublished'] = date('c', $page->date());
            if ($page->modified()) $data['dateModified']  = date('c', $page->modified());
        } else {
            $data = [
                '@context' => 'https://schema.org',
                '@type'    => 'WebPage',
                'name'     => $title,
                'url'      => $url,
            ];
            if ($desc)     $data['description'] = $desc;
            if ($imageUrl) $data['image']        = $imageUrl;
        }

        return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private function getPageType($page)
    {
        $headerType = $this->getHeaderValue($page, 'type');
        if ($headerType) {
            return $headerType;
        }
        return $page->home() ? 'website' : 'article';
    }

    private function getPageLocale()
    {
        $lang = $this->grav['language']->getActive() ?? $this->grav['language']->getDefault();
        if (!$lang) {
            return null;
        }
        $localeMap = [
            'en' => 'en_US', 'es' => 'es_ES', 'fr' => 'fr_FR', 'de' => 'de_DE',
            'it' => 'it_IT', 'pt' => 'pt_PT', 'nl' => 'nl_NL', 'ru' => 'ru_RU',
            'ja' => 'ja_JP', 'zh' => 'zh_CN', 'ar' => 'ar_AE', 'ko' => 'ko_KR',
            'sv' => 'sv_SE', 'pl' => 'pl_PL', 'tr' => 'tr_TR',
        ];
        return $localeMap[$lang] ?? strtolower($lang) . '_' . strtoupper($lang);
    }

    private function getHeaderValue($page, $key)
    {
        $header     = $page->header();
        $socialMeta = $header->social_meta ?? null;
        if ($socialMeta === null) {
            return null;
        }
        if (is_array($socialMeta) && isset($socialMeta[$key])) {
            return $socialMeta[$key];
        }
        if (is_object($socialMeta) && isset($socialMeta->$key)) {
            return $socialMeta->$key;
        }
        return null;
    }

    private function getPageImage($page)
    {
        if (!empty($page->value('media.image'))) {
            $images = $page->media()->images();
            return array_shift($images);
        }
        foreach ($page->children() as $child) {
            $image = $this->getPageImage($child);
            if ($image !== null) {
                return $image;
            }
        }
        return null;
    }

    private function getPageDescription($page)
    {
        $summary = $page->summary();
        if (!$this->stringIsEmpty($summary)) {
            return $summary;
        }
        foreach ($page->children() as $child) {
            $description = $this->getPageDescription($child);
            if (!$this->stringIsEmpty($description)) {
                return $description;
            }
        }
        return '';
    }

    private function stringIsEmpty($string)
    {
        return strlen(trim($string)) === 0;
    }

    private function sanitizeMarkdowns($text)
    {
        $rules = [
            '/(#+)(.*)/'                             => '\2',  // headers
            '/(&lt;|<)!--\n((.*|\n)*)\n--(&gt;|\>)/' => '',    // comments
            '/(\*|-|_){3}/'                          => '',    // hr
            '/!\[([^\[]+)\]\(([^\)]+)\)/'            => '',    // images
            '/\[([^\[]+)\]\(([^\)]+)\)/'             => '\1',  // links
            '/(\*\*|__)(.*?)\1/'                     => '\2',  // bold
            '/(\*|_)(.*?)\1/'                        => '\2',  // emphasis
            '/\~\~(.*?)\~\~/'                        => '\1',  // del
            '/\:\"(.*?)\"\:/'                        => '\1',  // quote
            '/```(.*)\n((.*|\n)+)\n```/'             => '\2',  // fence code
            '/`(.*?)`/'                              => '\1',  // inline code
            '/(\*|\+|-)(.*)/'                        => '\2',  // ul lists
            '/\n[0-9]+\.(.*)/'                       => '\2',  // ol lists
            '/(&gt;|\>)+(.*)/'                       => '\2',  // blockquotes
            '/\s+/'                                  => ' ',   // collapse whitespace
        ];

        foreach ($rules as $regex => $replacement) {
            if (is_callable($replacement)) {
                $text = preg_replace_callback($regex, $replacement, $text);
            } else {
                $text = preg_replace($regex, $replacement, $text);
            }
        }

        return substr(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'), 0, 280);
    }

}
