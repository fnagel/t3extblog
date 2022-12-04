.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

Target group: **Administrators**

.. contents:: Within this page
   :local:
   :depth: 3


.. _configuration:

Configuration
=============


Minimal configuration
---------------------

.. _configuration-minimal:

Make sure to setup at least the following settings (see :code:`/Configuration/TypoScript/constants.txt` for more details!):

.. code-block:: typoscript

	# PID of the blog sysfolder containing all blog related records
	plugin.tx_t3extblog.persistence.storagePid = 456

	# PID where the "Blogsystem" plugin is located
	plugin.tx_t3extblog.settings.blogsystem.pid = 123

	# PID where the "Subscription Manager" plugin is located
	plugin.tx_t3extblog.settings.subscriptionManager.pid = 789

	# E-mail address where notification mails were send to
	plugin.tx_t3extblog.settings.subscriptionManager.admin.mailTo.email = mailadress@of-the-admin.tld


General configuration
---------------------

Key prefix is *plugin.tx_t3extblog.*

=========================================   =========================================================================================================
Property                                    Description
=========================================   =========================================================================================================
settings.blogName                           Name of your blog
settings.blogsystem                         All settings of your blog system, i.e. pagination, comment handling...
settings.subscriptionManager                Configure the subscription process, i.e. admin email, notification email...
settings.blogSubscription                   Configure blog (new post) subscription form
settings.categories                         Some settings for the categorie-module
settings.latestPosts                        Some settings for the latest posts module
settings.latestComments                     Some settings for the latest comments module
settings.rss                                Basis rss-settings (see :code:`/Configuration/TypoScript/RSS/setup.typoscript` for page config)
settings.backend                            Configure the backend output
settings.debug                              Some debug configs
settings.*.privacyPolicy.enabled            Enable the privacy policy checkbox in the subscription forms
settings.*.privacyPolicy.typolink           Set a typolink as the privacy policy link
lib.tx_t3extblog.date                       Localize date format, used within the fluid templates
lib.tx_t3extblog.month                      Localize month format, used within the fluid templates
=========================================   =========================================================================================================


Like most other extensions, settings can be overwritten via TypoScript.

Have a look at :code:`/Configuration/TypoScript/setup.typoscript` and its includes (see `/Configuration/TypoScript/Includes/Settings`).


SPAM checks
-----------
.. _configuration-spam:

A comment has two additional visibility flags (besides TYPO3 default `hidden` and `deleted` flags):
*is approved* and *is spam*. Comments not approved or marked as SPAM are not displayed.

Admins configure if a comment should be approved by default or always needs manual approval.
In addition multiple basic SPAM checks flag every new comment as SPAM or not SPAM:

* Simple "I am human" checkbox (no real check for bots!)
* Honeypot fields
* Cookie support check
* User agent check
* Search for links in comment text

Each of these checks has a configurable spam point value. The sum of all spam points is compared to multiple
configurable threshold values in order to trigger specific actions:

* mark comment as spam but save it anyway
* block comment and allow user to try again
* redirect user to a configurable page


See TypoScript for full configuration:

* :code:`plugin.tx_t3extblog.settings.blogsystem.comments`
* :code:`plugin.tx_t3extblog.settings.blogSubscription`


.. important::

	Built in SPAM protection may be insufficient to protect your blog from serious attacks and does not prevent brute force attacks.


Speaking URLs
-------------
.. _configuration-speaking-url:

This extension include a predefined setup for the TYPO3 CMS core feature speaking URL,
see :code:`t3extblog/Configuration/Routes/Default.yaml` for details.

Default will render URLs for blog posts like this: `domain.com/page/article/2020/12/30/post-title/` but it's easy to
change this by either:

* Remove the `datePrefix` option completely, resulting in no date prefix in the URL:
   `domain.com/page/article/post-title/`
* Use any date format string with according prefix. For example with speaking month names:
   `datePrefix: 'Y/F/'` and `datePrefixRegex: '#^[^\/]*\/[^\/]*\/#'` resulting in URLs like
   `domain.com/page/article/2020/December/post-title/` (please note: you will need to adjust `requirements.post_title`
   to `^\d{4}\/\p{L}{3,10}\/[\p{Ll}\d\-\_]+$` in order to make this work; you might want to enable `datePrefixLowercase`
   too)

.. tip::
	Option `datePrefix` uses the `date()` method, see here for possible options:
   https://www.php.net/manual/en/function.date
   https://www.php.net/manual/en/datetime.format.php

.. tip::
	See here for more info on site configuration and speaking URLs:
   https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ApiOverview/SiteHandling/Basics.html
   https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ApiOverview/Routing/Examples.html#usage-with-imports


RealURL
-------
.. _configuration-realurl:

This extension include a predefined setup for RealURL auto configuration, see :code:`typo3conf/ext/t3extblog/Classes/Hooks/RealUrl.php` for details.

When using EXT:realurl or similar extension you will need to add additional StaticTS template
`T3Extblog: additional RealUrl config (t3extblog)` (:code:`/Configuration/TypoScript/RealUrl/setup.txt`).

When using RSS, add a :code:`config.tx_realurl_enable = 1` to your TS to get RealUrl running in the RSS-Feed.

.. important::
	Since version 5.x (and therefor TYPO3 9.x) it's recommended to use the core speaking URL feature (see above).

.. important::
	Add this static TS to the blogsystem plugin page only to preserve cache!

.. important::
	When not using the realurl autoconfig feature, you need to add the configuration by yourself!


Responsive image rendering
--------------------------
.. _configuration-rwd-images:

T3extblog is able to make use of TYPO3's RWD image rendering (sourceCollection feature).

This works by using a cObject ViewHelper within the templates which processes a TypoScript Lib:
:code:`lib.tx_t3extblog.responsiveImage` (defined in :code:`/Configuration/TypoScript/setup.txt`)

This feature is configured via TypoScript as described here:
https://docs.typo3.org/typo3cms/TyposcriptReference/ContentObjects/Image/Index.html

By default, T3extblog makes uses of the tt_content image rendering configuration. It's possible to adjust the
rendering for t3extblog by modifying this TypoScript lib or by using a custom (i.e. Fluid) rendering.

.. important::
	Since version 5.x (and therefor TYPO3 9.x) this feature has been removed as no longer provided by the TYPO3 core.


Overwrite templates
-------------------
.. _configuration-overwrite-templates:

It's possible to copy only the needed files and have a fallback.

Use the constants or change the TS setup:

.. code-block:: typoscript

	plugin.tx_t3extblog {
        view {
			   # "Email/" is appended to the final template path when rendering emails
            templateRootPaths {
                0 = EXT:t3extblog/Resources/Private/Templates/
                10 = {$plugin.tx_t3extblog.view.templateRootPath}
                20 = EXT:my_theme/Resources/Private/T3extblog/Templates/
                30 = fileadmin/templates/ext/t3extblog/Templates/
            }
            partialRootPaths {
                0 = EXT:t3extblog/Resources/Private/Partials/
                10 = {$plugin.tx_t3extblog.view.partialRootPath}
                20 = EXT:my_theme/Resources/Private/T3extblog/Partials/
            }
            layoutRootPaths {
                0 = EXT:t3extblog/Resources/Private/Layouts/
                10 = {$plugin.tx_t3extblog.view.layoutRootPath}
                20 = EXT:my_theme/Resources/Private/T3extblog/Layouts/
            }
        }
	}
