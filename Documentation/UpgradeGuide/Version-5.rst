.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _upgrade-guide-v5:

Version 5.x
-----------

.. contents:: Within this page
   :local:
   :depth: 3


Upgrade from 4.x to 5.0.0
^^^^^^^^^^^^^^^^^^^^^^^^^

*"TYPO3 9.5 support"*

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/4.0.0...5.0.0

- Support TYPO3 9.x

- Support new TYPO3 core SEO sitemap functionality

- Support new TYPO3 core routing functionality

- Fixed all "strong" issues in the "Scan Extension Files"

- Code clean-up (removed quite a lot legacy code and workarounds)

- Some minor bugfixes and improvements


**Breaking changes**

- Removed support for TYPO3 8.x

- Post URL structure has changed (yyyy-mm-dd-my-post-title instead of yyyy/mm/dd/my-post-title)

- Removed support for EXT:realurl

- Removed support for EXT:dd_googlesitemap

- Removed `blogsystem.comments.allowSomeTagAttributes` setting


How to upgrade
""""""""""""""

#. Add extension :ref:`routing configuration <configuration-speaking-url>` to your site configuration

#. Make sure your site configuration has an absolute URL as "Entry Point" (or base) configured (so no `/` but
   something like `https://domain.com/`. Otherwise TYPO3 will NOT RENDER ABSOLUTE URLs in email templates!

#. Add a htaccess rule for redirecting old post URLs to the new one

   This will work for the default routing:

   .. code-block::

      RewriteRule ^blog/article/(\d{4})/(\d{2})/(\d{2})/(.+)$ /blog/article/$1-$2-$3-$4 [R=301,NC,L]

#. Add "Sitemap setup" static TS to your template in order to enable :ref:`SEO sitemap support <faq-sitemap>`

#. Use "Compare database" in install tool to adjust changed DB fields

#. Run "Create missing post URL slugs" update wizard in extension manager

#. Run "Create missing category URL slugs" update wizard in extension manager

#. Clear all caches
