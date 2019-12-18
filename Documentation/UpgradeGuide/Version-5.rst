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

- Support for TYPO3 9.x

- Support of new TYPO3 core routing functionality

- Support of new TYPO3 core SEO sitemap functionality

- Fixed all "strong" issues in the "Scan Extension Files"

- Code clean-up (removed quite a lot legacy code and workarounds)

- Lots of minor bugfixes and improvements


**Breaking changes**

- Removed support for TYPO3 8.x

- Post URL structure has changed (yyyy-mm-dd-my-post-title instead of yyyy/mm/dd/my-post-title)

- Removed support for EXT:realurl

- Removed support for EXT:dd_googlesitemap

- Removed `blogsystem.comments.allowSomeTagAttributes` setting

- Replaced RWD image TypoScript lib (whose functionality is no longer available) with default image VH

- Rename TS and TSconfig files to newer file extensions

- Split TS configuration into multiple files


How to upgrade
""""""""""""""

#. Use "Compare database" in install tool to adjust changed DB fields

#. Run "Create missing post URL slugs" update wizard in extension manager

#. Run "Create missing category URL slugs" update wizard in extension manager

#. Add extension :ref:`routing configuration <configuration-speaking-url>` to your site configuration

#. Make sure your site configuration has an absolute URL as "Entry Point" (or base) configured (so no `/` but
   something like `https://domain.com/`. Otherwise TYPO3 will NOT RENDER ABSOLUTE URLs in email templates!

#. Add a htaccess rule for redirecting old post URLs to the new one (see "URL redirect" below)

#. Update your TS and TSconfig includes if needed

#. Update your TS linkhandler configuration if needed

#. Add "Sitemap setup" static TS to your template in order to enable :ref:`SEO sitemap support <faq-sitemap>`

#. Clear all caches


**URL redirect**

This will work for the default routing:

.. code-block::

   RewriteRule ^blog/article/(\d{4})/(\d{2})/(\d{2})/(.+)$ /blog/article/$1-$2-$3-$4 [R=301,NC,L]

Please note that it is now possible to configure the URLs as you wish, even without a date!
