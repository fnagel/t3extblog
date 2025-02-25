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


Upgrade to 9.0.0
^^^^^^^^^^^^^^^^

*"TYPO3 13 LTS support"*

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/8.0.2...9.0.0

- Support for TYPO3 13.4 LTS and PHP 8.2

- Replace `BackendConfigurationManager` override with improvements in settings service class

- Updated images of the CMS backend in documentation

- Categories collapsable functionality is now configurable

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
