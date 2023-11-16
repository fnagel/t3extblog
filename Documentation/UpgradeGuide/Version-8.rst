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


Upgrade to 8.0.0
^^^^^^^^^^^^^^^^

*"TYPO3 12 LTS support"*

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/7.x.x...8.0.0

- Support for TYPO3 12.4 LTS

- Improved configuration checks in BE module

- Make use of new datetime, email and number TCA types

- Lots of bugfixes and other improvements

- Code clean-up and quality improvements


**Breaking changes**

- Removed support for TYPO3 11.x

- Removed support for PHP 7.x

- Singal / Slot extension points have been removed, please migrate to PSR-14 events (be careful when extending EXT:t3extblog)


How to upgrade
""""""""""""""

#. "Change fields" in DB using "Analyze Database" in Install tool / Maintenance module

#. Adjust your TypoScript configuration (RSS configuration has changed)

#. Clear all caches
