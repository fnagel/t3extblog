.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

Target group: **Administrators**

.. _configuration:

Configuration
=============

.. contents:: Within this page
   :local:
   :depth: 3


Minimal configuration
---------------------

.. _configuration-minimal:

Make sure to setup at least the following settings (see :code:`/Configuration/TypoScript/constants.txt` or
:code:`/Configuration/Sets/Default/settings.definitions.yaml` for more details!):

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
                20 = EXT:t3extblog/Resources/Private/Templates/
                21 = {$plugin.tx_t3extblog.view.templateRootPath}
                30 = EXT:my_theme/Resources/Private/T3extblog/Templates/
            }
            partialRootPaths {
                20 = EXT:t3extblog/Resources/Private/Partials/
                21 = {$plugin.tx_t3extblog.view.partialRootPath}
                30 = EXT:my_theme/Resources/Private/T3extblog/Partials/
            }
            layoutRootPaths {
                20 = EXT:t3extblog/Resources/Private/Layouts/
                21 = {$plugin.tx_t3extblog.view.layoutRootPath}
                30 = EXT:my_theme/Resources/Private/T3extblog/Layouts/
            }
        }
	}
