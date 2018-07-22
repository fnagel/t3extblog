.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _upgrade-guide-v3:

Version 4.x
-----------

.. only:: html

	.. contents:: Within this page
		:local:
		:depth: 3



Upgrade from 3.x to 4.0.0
^^^^^^^^^^^^^^^^^^^^^^^^^

*"Packagist support"*

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/3.0.0...4.0.0

- Extension is now available on Packagist

- Fixed all "TCA Migrations" warnings in install tool

- Code quality improvements

- Use default 404 handler for hidden or deleted records

- Add update wizard check for invalid comments (EXT:t3blog migration helper)

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

#. Adjust VH namespace in your overwrite templates

#. Clear all caches
