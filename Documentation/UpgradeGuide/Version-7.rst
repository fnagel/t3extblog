.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _upgrade-guide-v6:

Version 7.x
-----------

.. contents:: Within this page
   :local:
   :depth: 3


Upgrade from 6.2.0 to 7.0.0
^^^^^^^^^^^^^^^^^^^^^^^^^^^

*"TYPO3 11 LTS support"*

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/6.2.0...7.0.0

- Support for TYPO3 11.5 LTS

- Support for PHP 8.0 and 8.1

- Implement custom pagination (as core VH widget is no longer available)

- Migrated EM update class to upgrade wizards

- Lots of bugfixes and other improvements

- Massive code clean-up and quality improvements


**Breaking changes**

- Removed support for TYPO3 10.x

- Routing and TCA has changed

- Page URL structure has changed (`my-blog/page-123` instead of `my-blog/page/123`)

- Template changes

- Some classes and methods have been renamed (be careful when extending EXT:t3extblog)


How to upgrade
""""""""""""""

#. "Change fields" in DB using "Analyze Database" in Install tool / Maintenance module

#. Adjust your routing configuration (pagination has changed)

#. Add a htaccess rule for redirecting old page URLs (see "URL redirect" below)

#. Adjust your templates (pagination and a few other changes)

#. Run upgrade wizards in admin tools

#. Clear all caches


**URL redirect**

This will work for the pagination URL change when using the default routing:

.. code-block::

   RewriteRule ^blog/page/(\d+)/$ /blog/page-$1 [R=301,NC,L]
   RewriteRule ^blog/(.*)/page/(\d+)/$ /blog/$1/page-$2 [R=301,NC,L]
