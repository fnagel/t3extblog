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

- Support for PHP 8.0

- Implement custom pagination (as core VH is no longer supported)

- Code clean-up (removed and replaced deprecated code usage)

- Migrated EM update class to upgrade wizards

- Some minor bugfixes and improvements



**Breaking changes**

- Removed support for TYPO3 10.x

- Routing changes

- Template changes

- Some classes and methods have been renamed (be careful when extending EXT:t3extblog)


How to upgrade
""""""""""""""

#. "Change fields" in DB using "Analyze Database" in Install tool / Maintenance module

#. Adjust your routing configuration (pagination has changed)

#. Adjust your templates (pagination and a few other changes)

#. Clear all caches
