.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _upgrade-guide-v9:

Version 9.x
-----------

.. contents:: Within this page
   :local:
   :depth: 3


Upgrade to 9.3.0
^^^^^^^^^^^^^^^^

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/9.2.0...9.3.0

- Added new "related posts" plugin

- Added related posts to post detail view

- Added tag cloud plugin

- Added menu processor for breadcrumbs and EXT:schema support

- Added "this is an old post" notice for detail view

- Option to configure recursive level in category list view

- Switched from `SimplePagination` to `SlidingWindowPagination` pagination

- Some small bugfixes and improvements


**Breaking changes**

- Some templates have changed
   - Post show
   - Category list


How to upgrade
""""""""""""""

#. Adjust post show template if needed

#. Clear all caches



Upgrade to 9.2.0
^^^^^^^^^^^^^^^^

*"Deprecation free"*

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/9.1.0...9.2.0

- Added PHP 8.4 support

- Deprecation free (in TYPO3 v13)

- Migrated docs to PHP rendering

- Clean-up documentation

- Clean-up TCA configuration

- Some small bugfixes and improvements


**Breaking changes**

- Migrated the plugin to a custom content element type


How to upgrade
""""""""""""""

#. Use provided upgrade wizard in "Upgrade" BE module to migrate existing plugins to content elements

#. Use "Compare database" in install tool to adjust changed DB fields

#. Clear all caches



Upgrade to 9.1.0
^^^^^^^^^^^^^^^^

*"Site sets support"*

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/9.0.0...9.1.0

- Support for site sets

- Support for Twitter Bootstrap 5

- Bugfix Fluid namespace in RSS templates

- Fix comment and post subscription delete

- Some small bugfixes and improvements

- Clean-up documentation


**Breaking changes**

- Removed support for twitter cards


How to upgrade
""""""""""""""

#. Adjust Fluid namespace of RSS template and partial if overridden

#. Adjust your templates if needed (post meta partial)

#. Clear all caches



Upgrade to 9.0.0
^^^^^^^^^^^^^^^^

*"TYPO3 13 LTS support"*

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/8.0.2...9.0.0

- Support for TYPO3 13.4 LTS and PHP 8.3

- Replace `BackendConfigurationManager` override with improvements in settings service class

- Updated images of the CMS backend in documentation

- Categories collapsible functionality is now configurable

- Lots of small bugfixes and replacements for deprecated core functionality

- Code clean-up and lots of code quality improvements


**Breaking changes**

- Removed support for TYPO3 12

- Removed support for PHP 8.1


How to upgrade
""""""""""""""

#. "Change fields" in DB using "Analyze Database" in Install tool / Maintenance module

#. Adjust your templates if needed (some BE templates and partials, category list template)

#. Clear all caches
