.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

Target group: **Administrators**


.. only:: html

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

*plugin.tx_t3extblog.*

=========================================   =========================================================================================================
Property                                    Description
=========================================   =========================================================================================================
settings.previewHiddenRecords               Enable preview of hidden records (see usermanual)
settings.blogsystem                         All settings of your blog system, i.e. pagination, comment handling...
settings.subscriptionManager                Configure the subscription process, i.e. admin email, notification email...
settings.blogSubscription                   Configure blog (new post) subscription form
settings.categories                         Some settings for the categorie-module
settings.latestPosts                        Some settings for the latest posts module
settings.latestComments                     Some settings for the latest comments module
settings.rss                                Basis rss-settings (see :code:`/Configuration/TypoScript/RSS/setup.txt` for page config)
settings.backend                            Configure the backend output
settings.debug                              Some debug configs
lib.tx_t3extblog.date                       Localize date format, used within the fluid templates
lib.tx_t3extblog.month                      Localize month format, used within the fluid templates
lib.tx_t3extblog.responsiveImage            TypoScript lib for rendering responsive images
=========================================   =========================================================================================================


Like most other extensions, settings can be overwritten via TypoScript.

Have a look at :code:`/Configuration/TypoScript/setup.txt`.


RealURL
-------
.. _configuration-realurl:

This extension include a predefined setup for RealURL auto configuration, see :code:`typo3conf/ext/t3extblog/Classes/Hooks/RealUrl.php` for details.

When using EXT:realurl or similar extension you will need to add additional staticTS template
`T3Extblog: additional RealUrl config (t3extblog)` (:code:`/Configuration/TypoScript/RealUrl/setup.txt`).

.. important::
	Add this static TS to the blogsystem plugin page only to preserve cache!

.. important::
	When not using the realurl autoconfig feature, you need to add the configuration by yourself!


Responsive image rendering
--------------------------
.. _configuration-rwd-images:

T3extblog is able to make use of TYPO3's RWD image rendering (sourceCollection feature).

This works by using a cObject ViewHelper within the templates which processes a TypoScript Lib:
:code:`lib.tx_t3extblog.responsiveImage` (defined in :code:`/Configuration/TypoScript/RealUrl/setup.txt`)

This feature is configured via TypoScript as described here:
https://docs.typo3.org/typo3cms/TyposcriptReference/ContentObjects/Image/Index.html

By default, T3extblog makes uses of the tt_content image rendering configuration. It's possible to adjust the
rendering for t3extblog by modifying this TypoScript lib or by using a custom (i.e. Fluid) rendering.


Overwrite templates
-------------------
.. _configuration-overwrite-templates:

It's possible to copy only the needed files and have a fallback.
"Email/" is appended to all paths when rendering emails.

Use the constants or change the TS setup:

.. code-block:: typoscript

	plugin.tx_t3extblog {
        view {
            templateRootPaths {
                0 = {$plugin.tx_t3extblog.view.templateRootPath}
                1 = EXT:my_theme/Resources/Private/T3extblog/Templates/
                2 = fileadmin/templates/ext/t3extblog/Templates/
            }
            partialRootPaths {
                0 = {$plugin.tx_t3extblog.view.partialRootPath}
                1 = EXT:my_theme/Resources/Private/T3extblog/Layouts/
            }
            layoutRootPaths {
                0 = {$plugin.tx_t3extblog.view.layoutRootPath}
                1 = EXT:my_theme/Resources/Private/T3extblog/Partials/
            }
        }
	}


**TYPO3 6.2**

Use this config for for email templates path:

.. code-block:: typoscript

	plugin.tx_t3extblog.email.templateRootPath = ...

