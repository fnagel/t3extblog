﻿.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _upgrade-guide:

Version 3.x
-----------

.. only:: html

	.. contents:: Within this page
		:local:
		:depth: 3



Upgrade from 2.x to 3.0.0
^^^^^^^^^^^^^^^^^^^^^^^^^

*"TYPO3 8.x support"*


Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/2.2.x...3.0.0

- Removed TYPO3 < 7.3 support - lots of legacy code has been removed!

- Added TYPO3 8.x support

- Added email layout and footer partial for easier customization

- Updated and added new screens in documentation

- Improved info boxes in backend modules (using default VH)

- Add proper create user field for post records

- Fixed some minor bugs

- Switch to PSR-2 CGL

- Added signal / slot hooks and fixed interface implementation (see :ref:`Extending T3extlog in docs <dev-guide-extending>`)

- Added `noindex, follow` meta tag in author, category and list view

- Fix category localization

- Fix issues with new `cHashIncludePageId` config and EXT:realurl 2.0.15

- Add prev / next meta tag for paginated views

- Add option to configure backend module date time format

- More minor bugfixes


**Breaking changes**

- ViewHelper changes
	- Introduced custom `paginate` VH
	- Removed custom `flashMessages` VH (use default one instead)
	- Add `flashMessagesClearCache` VH in `Comment/New.html` partial (before `flashMessages` VH!)

- Changed email template configuration
	- Old `plugin.tx_t3extblog.email` configuration has been removed
	- Use default `plugin.tx_t3extblog.view` configuration instead
	- `Email/` is appended to the final template path when rendering emails
	- No changes needed if default path structure has been used


How to upgrade
""""""""""""""

#. Make sure to fix all version 2.2.2 related security issues!

#. Create new DB fields by using "Compare current database with specification" in Install Tool

#. "Clear all cache" in Install Tool

#. Use "Add current post author to `cruser_id` field" update script in EM

#. You might need to reinstall the extension to rebuild class autoloading

#. Adjust and update TypoScript configuration and template overwrites