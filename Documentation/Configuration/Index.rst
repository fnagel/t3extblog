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

Like most other extensions, settings can be overwritten via TypoScript.

Have a look at :code:`/Configuration/TypoScript/setup.txt`.


Minimal configuration
---------------------

Two configs are needed in any case:

.. code-block:: typoscript

	# PID where your blogsystem is included
	plugin.tx_t3extblog.settings.blogsystem.pid = 123

	# PID where you will store your blogposts
	plugin.tx_t3extblog.persistence.storagePid = 456


General configuration
---------------------

plugin.tx_t3extblog.

=========================================   =========================================================================================================
Property                                    Description
=========================================   =========================================================================================================
view.templateRootPath                       Path to the template root
view.partialRootPath                        Path to the partials
view.layoutRootPath                         Path to the layout
email.templateRootPath                      Path to the email template root
email.partialRootPath                       Path to the email partials
email.layoutRootPath                        Path to the email layout
persistence.storagePid                      See above
persistence.enableAutomaticCacheClearing    Enable automatic clearing of the cache
settings.blogName                           Name of your blog
settings.previewHiddenRecords               Enable preview of hidden records (see usermanual)
settings.blogsystem                         All settings of your blogsystem, i.e. pagination, commenthandling...
settings.subscriptionManager                Configure the subscriptionprocess, i.e. adminmail, notification...
settings.categories                         Some settings for the categorie-module
settings.latestPosts                        Some settings for the latest posts module
settings.latestComments                     Some settings for the latest comments module
settings.rss                                Basis rss-settings (see :code:`/Configuration/TypoScript/RSS/setup.txt` for page config)
settings.backend                            Configure the backend output
settings.debug                              Some debug configs
lib.tx_t3extblog.date                       Localize date format, used within the fluid templates
=========================================   =========================================================================================================

See :code:`/Configuration/TypoScript/RealUrl/setup.txt` for details!


RealURL
-------

When using EXT:realurl or similar extension you will need to add additional staticTS template
"T3Extblog: additional RealUrl config (t3extblog)" (:code:`/Configuration/TypoScript/RealUrl/setup.txt`).

.. important::
	Add this static TS to the blogsystem plugin page only to preserve cache!
