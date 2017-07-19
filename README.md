# Social Meta Tags Plugin

The **Social Meta Tags** Plugin is for [Grav CMS](http://github.com/getgrav/grav).

## Description

This Plugin adds all Meta Tags that are needed for Facebook Open Graph, and Twitter Cards.


# Features

* [Open Graph](http://ogp.me/) support.
* [Twitter Cards](https://dev.twitter.com/cards/overview) support. You can select between Summary and Large cards.
* [AboutMe plugin](https://github.com/Birssan/grav-plugin-about-me) integration capabilities.


# Installation

As this plugin is not yet in the Grav repository, you will need to install it manually. From your plugins folder (`user/plugins`):

```
git clone https://github.com/tucho235/grav-plugin-social-meta-tags social-meta-tags
```

This will clone this repository into the social-meta-tags folder.


# Usage

Simply enable the plugin, there is no need to edit any templates. :)

# Configuration
If you want to customize the configuration, you can add or modify your `user/config/plugins.yml`
with a settings from `user\plugins\social-meta-tags\social-meta-tags.yaml`.

## Associating a Twitter account

Social-Meta-Tags can use [AboutMe plugin](https://github.com/Birssan/grav-plugin-about-me)
min version 1.1.4. To add/change the Twitter defined in `twitter:site`, edit
your profile in the AboutMe plugin. However, this isn't required. You can
manually define `twitter:site` without using the AboutMe plugin.

## Facebook App Id

Social-Meta-Tags is able to use [Facebook Open Graph](https://developers.facebook.com/docs/opengraph/getting-started).
You need to generate an app_id before using this. Without this property you will lose admin rights on the Open Graph Facebook Page.

# Contributing

If you think there are any implementations that are not the best, please feel
free to submit ideas and pull requests. All comments and suggestions are welcome.
