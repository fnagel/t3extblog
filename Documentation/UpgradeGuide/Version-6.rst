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


Upgrade from 6.2.0 to 6.2.1
^^^^^^^^^^^^^^^^^^^^^^^^^^^

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/6.2.0...6.2.1

- Minor improvements and fixes

- Clean up code base and fix extension scanner issues

- Improve code quality using rector, see https://github.com/rectorphp/rector


How to upgrade
""""""""""""""

#. Adjust templates and routing configuration if needed

#. Clear caches



Upgrade from 6.1.0 to 6.2.0
^^^^^^^^^^^^^^^^^^^^^^^^^^^

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/6.1.1...6.2.0

- Improved post URL generation (more flexible date segment), thanks to @mueller-sebastian!

- Extension now uses Crowdin for managing localization, see https://crowdin.com/project/typo3-extension-t3extblog

- Improve code quality using rector, see https://github.com/rectorphp/rector


How to upgrade
""""""""""""""

#. Make sure the new `routeEnhancers.T3extblogBlogsystemPlugin.aspects.post_title.datePrefixRegex` routing option
   is available (either by adding it you your config or by importing the default)

#. Pull latest localization data

#. Clear caches



Upgrade from 6.0.3 to 6.1.0
^^^^^^^^^^^^^^^^^^^^^^^^^^^

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/6.0.3...6.1.0

- Pre-fill comment author and email field (see `settings.blogsystem.comments.prefillFields`,
   sponsored feature, thanks to *WebundWerbeWerk* https://www.webundwerbe.de )

- Allow comment email address to be an optional property (see `settings.blogsystem.comments.requireEmail`)

- Add items amount configuration to latest records plugins

- Minor refactoring and improvements


How to upgrade
""""""""""""""

#. Adjust templates and configuration if needed

#. Clear caches



Upgrade from 6.0.2 to 6.0.3
^^^^^^^^^^^^^^^^^^^^^^^^^^^

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/6.0.2...6.0.3

- Support for PHP 7.4

- Fix some issues with MySQL 5.7 and DB mode (thanks to Philipp Kuhlmay)

- Fix post preview functionality

- Improve TS constants editor categories

- Add Github action based commit message check


How to upgrade
""""""""""""""

#. Clear caches



Upgrade from 6.0.1 to 6.0.2
^^^^^^^^^^^^^^^^^^^^^^^^^^^

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/6.0.1...6.0.2

- Fix issues with routing in latest TYPO3 versions

- Add Github action based code quality tests

- Some minor bugfixes and improvements


How to upgrade
""""""""""""""

#. Clear caches



Upgrade from 6.0.0 to 6.0.1
^^^^^^^^^^^^^^^^^^^^^^^^^^^

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/6.0.0...6.0.1

- RSS template improvements (CGL, added item categories and image closure tags, introduce item partial)

- Improve routing error logging

- Some SQL and TCA clean-up

- Some minor bugfixes and improvements


How to upgrade
""""""""""""""

#. Remove obsolete table in DB using "Analyze Database" in Install tool / Maintenance module

#. Adjust your RSS templates if needed

#. Clear caches



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
