# Social Meta Tags Plugin

The **Social Meta Tags** Plugin is for [Grav CMS](http://github.com/getgrav/grav). 

## Description

Add all Meta Tags that need Facebook Open Graph, and Twitter Cards.


# Features

* [Open Graph](http://ogp.me/) support.
* [Twitter Cards](https://dev.twitter.com/cards/overview) support. You can select between Summary and Large cards.
* [AboutMe plugin](https://github.com/Birssan/grav-plugin-about-me) integration.


# Installation

As this plugin is not yet in the Grav repository, you need to install it manually. From your plugins folder:
```
git clone https://github.com/tucho235/grav-plugin-social-meta-tags social-meta-tags
```

This will clone this repository into the social-meta-tags folder.


# Usage

Just enable plugin, no need edit any template. :)


# Configuration

## Associate Twitter account

Social-Meta-Tags need [AboutMe plugin](https://github.com/Birssan/grav-plugin-about-me). To add/change the Twitter defined in `twitter:site`, edit your profile in the AboutMe plugin.

## Facebook App Id

Social-Meta-Tags is able to use [Facebook Open Graph](https://developers.facebook.com/docs/opengraph/getting-started). You need generate an app_id. Without this property you'll loose admin right on the Open Graph Facebook Page.


# Contributing

If you think any implementation are just not the best, feel free to submit ideas and pull requests. All your comments and suggestion are welcome.

