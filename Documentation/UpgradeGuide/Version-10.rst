.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _upgrade-guide-v9:

Version 10.x
-----------

.. contents:: Within this page
   :local:
   :depth: 3



Upgrade to 10.0.0
^^^^^^^^^^^^^^^^^

*"TYPO3 14 support"*

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/9.2.0...10.0.0

- Support for TYPO3 14.0

- Support for TYPO3 FluidMail email standard

- Support for fully localized emails

- Lots of small bugfixes and replacements for deprecated core functionality

- Code clean-up and lots of code quality improvements


**Breaking changes**

- Removed support for TYPO3 13

- Email templates have changed


How to upgrade
""""""""""""""

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
