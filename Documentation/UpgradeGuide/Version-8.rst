.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _upgrade-guide-v8:

Version 8.x
-----------

.. contents:: Within this page
   :local:
   :depth: 3


Upgrade to 8.0.2
^^^^^^^^^^^^^^^^

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/8.0.0...8.0.2

- Fix RSS links

- Fixes for workspaces and permissions

- Replace deprecated core functionality

- Some more bugfixes


How to upgrade
""""""""""""""

#. Clear all caches



Upgrade to 8.0.0
^^^^^^^^^^^^^^^^

*"TYPO3 12 LTS support"*

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/7.0.0...8.0.0

- Support for TYPO3 12.4 LTS and PHP 8.2

- Improved configuration checks in BE module

- Make use of new file, datetime, email and number TCA types

- Use new registration API for upgrade wizards, backend modules and dashboard widgets

- Introduce PSR-14 events

- Lots of small bugfixes and other improvements

- Code clean-up and lots of code quality improvements


**Breaking changes**

- Removed support for TYPO3 11

- Removed support for PHP 7

- Replaced removed core BE user model with custom one

- Signal / Slot functionality has been removed


How to upgrade
""""""""""""""

#. Migrate your signal / slot extension points to :ref:`PSR-14 events <dev-guide-extending-events>` (optional)

#. "Change fields" in DB using "Analyze Database" in Install tool / Maintenance module

#. Adjust your TypoScript configuration (RSS configuration has changed)

#. Adjust your templates if needed (`FormErrors` and `FlashMessages` partials have changed)

#. Clear all caches
