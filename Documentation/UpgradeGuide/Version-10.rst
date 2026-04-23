.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _upgrade-guide-v10:

Version 10.x
-----------

.. contents:: Within this page
   :local:
   :depth: 3



Upgrade to 10.2.0
^^^^^^^^^^^^^^^^^

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/10.1.0...10.2.0

- Support for TYPO3 14.3 LTS

- Important email bugfix


**Breaking changes**

- Removed support for Twitter Boostrap version 3


How to upgrade
""""""""""""""

#. Adjust templates if you need the old Twitter Boostrap templating

#. Clear all caches



Upgrade to 10.1.0
^^^^^^^^^^^^^^^^^

*"Features & Tests"*

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/10.0.0...10.1.0

- Support for TYPO3 14.2

- Added new "related posts" plugin

- Added related posts to post detail view

- Added reading time calculation to list and detail view (new `ReadingTimeViewHelper`)

- Added "this is an old post" notice for detail view

- Added year filter to archive plugin

- Added tag cloud plugin

- Option to configure recursive level in category list view

- Added menu processor for breadcrumbs and EXT:schema support

- Switched from `SimplePagination` to `SlidingWindowPagination` pagination

- Added unit, functional and acceptance tests incl. CI

- Added YAML lint test in CI

- Some small bugfixes and improvements


**Breaking changes**

- Removed support for TYPO3 < 14.2

- Some templates have changed
   - Post show
   - Post list
   - Post archive
   - Category list


How to upgrade
""""""""""""""

#. Adjust templates if needed

#. Add new archive plugin routing configuration (`Configuration/Routes/Archive.yaml`) if needed

#. Clear all caches



Upgrade to 10.0.0
^^^^^^^^^^^^^^^^^

*"TYPO3 14 support"*

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/9.2.0...10.0.0

- Support for TYPO3 14

- Support for TYPO3 FluidMail email standard

- Support for fully localized emails

- Make use of new meta tag view helpers

- Lots of small bugfixes and replacements for deprecated core functionality

- Code clean-up and lots of code quality improvements


**Breaking changes**

- Removed support for TYPO3 13

- Email templates have changed

- Removed some custom view helpers (`MetaTag` and `HeaderData` VH)


How to upgrade
""""""""""""""

#. Replace MetaTag view helper, e.g. `<t3b:metaTag property="keywords" content="xyz" />` with core VH
   - Check `Resources/Private/Partials/Post/Meta.html` template if overridden

#. Replace HeaderData view helper, e.g. `<t3b:headerData>...</t3b:headerData>` with core VH
   - Check `Resources/Private/Partials/PaginationMeta.html` template if overridden

#. Configure email type in site settings (`plugin.tx_t3extblog.settings.emailType`) or TypoScript:
   - `mailMessage`: MailMessage: standalone Fluid template rendering (legacy)
   - `fluidEmail`: FluidEmail is the newer TYPO3 standard for themed emails

#. Adjust your email templates if overridden
   - Update layout from `<f:layout name="Email" />` to `<f:layout name="{layout}" />`
   - Update section from `<f:section name="main">` to `<f:section name="Main">`
   - Adjust TypoScript `template` configurations to use plain, HTMl or both (default) email templates
     (see e.g. `plugin.tx_t3extblog.settings.subscriptionManager.comment.admin.template`)
   - Add HTML email templates if not existing yet

#. Clear all caches
