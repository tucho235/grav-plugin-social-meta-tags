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
                $meta['twitter:title']['content']  = $this->sanitizeMarkdowns($this->grav['page']->title());
            }

            if (!isset($meta['twitter:description'])) {
                $meta['twitter:description']['name']     = 'twitter:description';
                $meta['twitter:description']['property'] = 'twitter:description';
                $meta['twitter:description']['content']  = $this->getPageDescription($this->grav['page']);
            }

            if (!isset($meta['twitter:image'])) {
                $image = $this->getPageImage($this->grav['page']);
                if (!empty($image)) {
                    $meta['twitter:image']['name']     = 'twitter:image';
                    $meta['twitter:image']['property'] = 'twitter:image';
                    $meta['twitter:image']['content']  = $this->grav['uri']->base() . $image->url();
                }
            }

            if (!isset($meta['twitter:site'])) {
                //Use AboutMe plugin configuration
                if ($this->grav['config']->get('plugins.social-meta-tags.social_pages.pages.twitter.aboutme'))
                {
                    if ($this->grav['config']->get('plugins.aboutme.social_pages.enabled')
                         and $this->grav['config']->get('plugins.aboutme.social_pages.pages.twitter.url'))
                    {
                        $user = preg_replace('((http|https)://twitter.com/)', '@', $this->grav['config']->get('plugins.aboutme.social_pages.pages.twitter.url'));
                    }
                    else
                    {
                        $user = "";
                    }
                }
                //Use plugin self-configuration
                else
                {
                    $user = "@".$this->grav['config']->get('plugins.social-meta-tags.social_pages.pages.twitter.username');
                }
                //Update data
                $meta['twitter:site']['name']     = 'twitter:site';
                $meta['twitter:site']['property'] = 'twitter:site';
                $meta['twitter:site']['content']  = $user;
            }
        }
        return $meta;
    }

    private function getFacebookMetatags($meta){

        if($this->grav['config']->get('plugins.social-meta-tags.social_pages.pages.facebook.enabled')){

            $meta['og:sitename']['name']        = 'og:site_name';
            $meta['og:sitename']['property']    = 'og:site_name';
            $meta['og:sitename']['content']     = $this->grav['config']->get('site.title');

            $meta['og:title']['name']           = 'og:title';
            $meta['og:title']['property']       = 'og:title';
            $meta['og:title']['content']        = $this->sanitizeMarkdowns($this->grav['page']->title());

            $meta['og:description']['name']     = 'og:description';
            $meta['og:description']['property'] = 'og:description';
            $meta['og:description']['content']  =  $this->getPageDescription($this->grav['page']);

            $meta['og:type']['name']            = 'og:type';
            $meta['og:type']['property']        = 'og:type';
            $meta['og:type']['content']         = 'article';

            $meta['og:url']['name']             = 'og:url';
            $meta['og:url']['property']         = 'og:url';
            $meta['og:url']['content']          = $this->grav['uri']->url(true);

            $image = $this->getPageImage($this->grav['page']);
            if (!empty($image)) {
                $meta['og:image']['name']     = 'og:image';
                $meta['og:image']['property'] = 'og:image';
                $meta['og:image']['content']  = $this->grav['uri']->base() . $image->url();
            }

            $meta['fb:app_id']['name']         = 'fb:app_id';
            $meta['fb:app_id']['property']     = 'fb:app_id';
            $meta['fb:app_id']['content']      = $this->grav['config']->get('plugins.social-meta-tags.social_pages.pages.facebook.appid');

        }
        return $meta;
    }

    /**
     * Get the image for the page.
     * @param  Grav\Common\Page\Page
     * @return Image|null
     */
    private function getPageImage($page) {
      if(count($page->collection()->modular()) && empty($page->value('media.image'))) {
        foreach($page->collection()->modular() as $child){
          if($child->value('media.image')){
            return array_shift($child->media()->images());
          }
        }
      }
      else if(empty($page->value('media.image'))) {
        return null;
      }
      else {
        return array_shift($page->media()->images());
      }
    }

    /**
     * Gets the description of a page
     * @return string
     */
    private function getPageDescription($page) {
      if(count($page->collection()->modular()) && $this->stringIsEmpty($this->sanitizeMarkdowns(strip_tags($page->summary())))) {
        foreach($page->collection()->modular() as $child){
          if($this->getPageDescription($child)){
            return $this->getPageDescription($child);
          }
          else {
            continue;
          }
        }
      }
      else {
        return trim($this->sanitizeMarkdowns(strip_tags($page->summary())));
      }
    }

    /**
     * Cleans a string and determines if it's actually empty
     * @return boolean
     */
    private function stringIsEmpty($string) {
      return empty(trim($string));
    }

    private function sanitizeMarkdowns($text){
        $rules = array (
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
            '/\s+/'                                  => ' ',
        );

        foreach ($rules as $regex => $replacement) {
            if (is_callable ( $replacement)) {
                $text = preg_replace_callback ($regex, $replacement, $text);
            } else {
                $text = preg_replace ($regex, $replacement, $text);
            }
        }

        return substr(htmlspecialchars($text, ENT_QUOTES, 'UTF-8'),0,140);
    }

}
