﻿.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _upgrade-guide-v4:

Version 4.x
-----------

.. contents:: Within this page
   :local:
   :depth: 3


Upgrade from 4.1.0 to 4.2.0
^^^^^^^^^^^^^^^^^^^^^^^^^^^

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/4.1.0...4.2.0

- Pre-fill comment author and email field (sponsored feature, thanks to *WebundWerbeWerk* https://www.webundwerbe.de)

- A few bugfixes


**Breaking changes**

- none


How to upgrade
""""""""""""""

#. Adjust templates and configuration if needed

#. Clear all caches



Upgrade from 4.0.x to 4.1.0
^^^^^^^^^^^^^^^^^^^^^^^^^^^

*"GDPR"*

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/4.0.1...4.1.0

- GDPR checkboxes for comment and blog post subscription form (disabled by default, see `privacyPolicy` constants and TS setup options) - THANKS to Kevin Ditscheid

- Improve documentation content and configuration - THANKS to Sybille Peters and @christophbee

- Some minor bugfixes - THANKS to Franz Kugelmann

- Apply PHP CS Fixer


**Breaking changes**

- none


How to upgrade
""""""""""""""

#. Use "Compare database" in install tool to add new DB fields

#. Adjust configuration and template for GDPR checkboxes

#. Clear all caches



Upgrade from 3.x to 4.0.0
^^^^^^^^^^^^^^^^^^^^^^^^^

*"Packagist support"*

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/3.0.2...4.0.0

- Extension is now available on Packagist

- Fixed all "TCA Migrations" warnings in install tool

- Code quality improvements

- Use default 404 handler for hidden or deleted records

- Add update wizard check for invalid comments (EXT:t3blog migration helper)

- Fix SQL for using workspace and versioning

- Some minor bugfixes


**Breaking changes**

- Changed PHP class namespace

- Changed composer package name

- Removed support for TYPO3 7.x

- PHP 5.6 is no longer supported


How to upgrade
""""""""""""""

#. Adjust your class auto loading or class overwrites to new namespace / composer package name (if needed for your setup)

#. Use "Clear all caches including PHP opcode cache" and "Dump Autoload Information" in the install tool (if needed for your setup)

#. Use "Compare database" in install tool to adjust changed DB fields

#. Adjust VH namespace in your overwrite templates

#. Clear all caches
