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
    /**
     * @return array
     *
     * The getSubscribedEvents() gives the core a list of events
     *     that the plugin wants to listen to. The key of each
     *     array section is the event that the plugin listens to
     *     and the value (in the form of an array) contains the
     *     callable (or function) as well as the priority. The
     *     higher the number the higher the priority.
     */
    public static function getSubscribedEvents()
    {

        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0]
        ];
    }


    /**
     * Initialize the plugin
     */
    public function onPluginsInitialized()
    {

        if (    !$this->isAdmin()
            and $this->config->get('plugins.social-meta-tags.enabled')
        ) {
            $this->enable([
                'onPageInitialized'     => ['onPageInitialized', 0]
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

    private function getTwitterCardMetatags($meta){

        if($this->grav['config']->get('plugins.social-meta-tags.social_pages.pages.twitter.enabled')) {

            if (!isset($meta['twitter:card'])) {
                $meta['twitter:card']['name']      = 'twitter:card';
                $meta['twitter:card']['property']  = 'twitter:card';
                $meta['twitter:card']['content']   = $this->grav['config']->get('plugins.social-meta-tags.social_pages.pages.twitter.type');
            }

            if (!isset($meta['twitter:title'])) {
                $meta['twitter:title']['name']     = 'twitter:title';
                $meta['twitter:title']['property'] = 'twitter:title';
                $meta['twitter:title']['content']  = $this->grav['page']->title();
            }

            if (!isset($meta['twitter:description'])) {
                $meta['twitter:description']['name']     = 'twitter:description';
                $meta['twitter:description']['property'] = 'twitter:description';
                $meta['twitter:description']['content']  = substr($this->grav['page']->value('content'), 0, 140);
            }

            if (!isset($meta['twitter:image'])) {
                if (!empty($this->grav['page']->value('media.image'))) {
                    $images = $this->grav['page']->media()->images();
                    $image  = array_shift($images);

                    $meta['twitter:image']['name']     = 'twitter:image';
                    $meta['twitter:image']['property'] = 'twitter:image';
                    $meta['twitter:image']['content']  = $this->grav['uri']->base() . $image->url();;
                }
            }

            if (!isset($meta['twitter:site'])) {
                if ($this->grav['config']->get('plugins.aboutme.social_pages.enabled')
                    and $this->grav['config']->get('plugins.aboutme.social_pages.pages.twitter.url')
                ) {
                    $user = preg_replace('((http|https)://twitter.com/)', '@', $this->grav['config']->get('plugins.aboutme.social_pages.pages.twitter.url'));
                    $meta['twitter:site']['name']     = 'twitter:site';
                    $meta['twitter:site']['property'] = 'twitter:site';
                    $meta['twitter:site']['content']  = $user;
                }
            }
        }
        return $meta;
    }

    private function getFacebookMetatags($meta){

        if($this->grav['config']->get('plugins.social-meta-tags.social_pages.pages.facebook.enabled')){

            $meta['og:sitename']['name']        = 'og:sitename';
            $meta['og:sitename']['property']    = 'og:sitename';
            $meta['og:sitename']['content']     = $this->grav['page']->value('name');

            $meta['og:title']['name']           = 'og:title';
            $meta['og:title']['property']       = 'og:title';
            $meta['og:title']['content']        = $this->grav['page']->title();

            $meta['og:description']['name']     = 'og:description';
            $meta['og:description']['property'] = 'og:description';
            $meta['og:description']['content']  = substr($this->grav['page']->value('content'),0,140);

            $meta['og:type']['name']            = 'og:type';
            $meta['og:type']['property']        = 'og:type';
            $meta['og:type']['content']         = 'article';

            $meta['og:url']['name']             = 'og:url';
            $meta['og:url']['property']         = 'og:url';
            $meta['og:url']['content']          = $this->grav['uri']->url(true);

            if (!empty($this->grav['page']->value('media.image'))) {
                $images = $this->grav['page']->media()->images();
                $image  = array_shift($images);

                $meta['og:image']['name']      = 'og:image';
                $meta['og:image']['property']  = 'og:image';
                $meta['og:image']['content']   = $this->grav['uri']->base() . $image->url();
            }

            $meta['fb:app_id']['name']         = 'fb:app_id';
            $meta['fb:app_id']['property']     = 'fb:app_id';
            $meta['fb:app_id']['content']      = $this->grav['config']->get('plugins.social-meta-tags.social_pages.pages.facebook.appid');

        }
        return $meta;
    }

}
