.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _upgrade-guide-v6:

Version 6.x
-----------

.. contents:: Within this page
   :local:
   :depth: 3


Upgrade from 5.1.0 to 6.0.0
^^^^^^^^^^^^^^^^^^^^^^^^^^^

*"TYPO3 10 LTS support"*

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/5.1.0...6.0.0

- Support for TYPO3 10.4 LTS

- Support of new TYPO3 core dashboard module

- Update and improve BE module templates and configuration

- Make use of new table mapping configuration

- Remove auto generated core DB fields

- Code clean-up (removed and replaced deprecated code usage)

- Some minor bugfixes and improvements



**Breaking changes**

- Removed support for TYPO3 9.x

- Backend module template changes

- Reverted blog post structure change of version 5.0.0 (see upgrade guide for version 5.1)


How to upgrade
""""""""""""""

#. "Change fields" in DB using "Analyze Database" in Install tool / Maintenance module

#. Adjust your BE templates to structure change if needed

#. Clear all caches
