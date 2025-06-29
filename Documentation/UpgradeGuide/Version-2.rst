﻿.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

.. _upgrade-guide-v2:

Version 2.x
-----------

.. contents:: Within this page
   :local:
   :depth: 3


Upgrade from 2.2.x to 2.2.2
^^^^^^^^^^^^^^^^^^^^^^^^^^^

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/2.2.1...2.2.2

- Important security fix!

- Strengthen security

- Improve default configuration

- Document how :ref:`SPAM protection <configuration-spam>` works


**Breaking changes**

- Comment website url scheme is now limited to http and https only

- Attributes for allowed tags in comments are now removed by default


How to upgrade
""""""""""""""

#. Run update wizard in Extension Manager to search for now invalid comment website links

#. Fix or remove all invalid, existing comment website links (!)

#. Please check your TypoScript configuration (SPAM protection, allowed tags, ...)

#. Clear all caches



Upgrade from 2.2.0 to 2.2.1
^^^^^^^^^^^^^^^^^^^^^^^^^^^

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/2.2.0...2.2.1

- Tested with EXT:realurl 2.x

- Minor bugfixes for backend modules

- Fix post preview for non-admin BE users in newer TYPO3 versions

- Adjust TCA to match latest TYPO3 API


How to upgrade
""""""""""""""

#. "Clear all cache" in Install Tool

#. Uninstall and install extension if needed (TCA issues)



Upgrade from 2.1.x to 2.2.0
^^^^^^^^^^^^^^^^^^^^^^^^^^^

*"Author improvements"*


Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/2.1.0...2.2.0

- Use HTML or text files as email templates

- Author filter for post list action

- Show backend user (post author) avatar image (available since TYPO3 7.5)

- New backend modules: dashboard view, list view for post and blog subscriptions

- Use built-in image cropping (since TYPO3 7.2) for preview image

- :ref:`Greatly improved cache handling <faq-clear-cache>` when creating new comments or editing posts in backend (no more page TS config needed!)

- Multiple minor fixes and improvements

- PHP 7.0 compatibility


**Breaking changes**

- New TypoScript configurations for BE modules

- Some localization keys have changed

- Some templates have changed (mostly backend module related)

- RealUrl configuration has been extended


.. tip::
	German localizations are now managed by the TYPO3 Pootle (translation.typo3.org) server (just like every other localization).


How to upgrade
""""""""""""""

#. "Clear all cache" in Install Tool

#. Adjust and update TypoScript configuration and template overwrites

#. Download localizations (using the "Languages" BE module)



Upgrade from 2.0.x to 2.1.0
^^^^^^^^^^^^^^^^^^^^^^^^^^^

This release has been sponsored by *Elementare Teilchen* (http://www.elementare-teilchen.de).


Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/2.0.1...2.1.0

- New major feature: Subscribe for new posts

- Configure email template files with TypoScript

- Some minor bugfixes and improvements

- Documentation improvements


**Subscribe for new posts**

- New plugin with simple subscription form (SPAM protected)

- Opt-in email for new subscriber

- Subscription management within the existing subscription manager plugin

- Send notification emails for single posts form the BE module


See :ref:`Users Manual <users-manual-notifications>` and
:ref:`Administration manual <administration-subscription-manager>` for more information.


**Breaking changes**

- Subscription manager TypoScript has changed:
	-  `subscriptionManager.admin` and `subscriptionManager.subscriber` moved to `subscriptionManager.comment.*`
	-  `subscriptionManager.admin.enable` changed to `subscriptionManager.admin.enableNotifications`
	-  `subscriptionManager.subscriber.enableNewCommentNotifications` changed to `subscriptionManager.subscriber.enableNotifications`

- Quite some localization keys have changed (mostly `subscriber` and `flashMessage.subscriber` related)

- Some templates have changed (e.g. changed link parameter in email templates, SPAM check partial, ...)

- RealUrl configuration has been extended

- Massive code refactoring (so in case you extended t3extblog, make sure to adjust your changes if needed)



How to upgrade
""""""""""""""

#. "Clear all cache" in Install Tool

#. Create new DB fields by using "Compare current database with specification" in Install Tool

#. Adjust and update TypoScript configuration, templates and localization overwrites

#. You probably want to run the upgrade wizard in EM to mark all old posts as "notification has been sent"



Upgrade from 2.0.0 to 2.0.1
^^^^^^^^^^^^^^^^^^^^^^^^^^^

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/2.0.0...2.0.1

- Bugfix for broken flash message caching in TYPO3 >= 7.3, see https://github.com/fnagel/t3extblog/issues/112

- Bugfix for hidden or deleted BE users (author field)

- Respect current post filter when using paginator

- TYPO3 Link validator support


How to upgrade
""""""""""""""

Make sure to add `addQueryStringMethod = GET` to all `paginate` TypoScript config arrays and to
adopt the changes in `Resources/Private/Templates/ViewHelpers/Widget/Paginate/Index.html` if needed.
Your RealUrl configuration needs to be updated if you're not using the auto configuration feature.


#. "Clear all cache" in Install Tool (including Opcode caches!)

#. Make sure to adopt TypoScript and Template changes!



Upgrade from 1.2.x to 2.0.0
^^^^^^^^^^^^^^^^^^^^^^^^^^^

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/1.0.0...2.0.0

- A bunch of bugfixes

- TYPO3 CMS 7.x support (tested up to TYPO3 7.6)

- Removed support for TYPO3 < 6.2

- Twitter Bootstrap 3 theme

- New backend icons for records and module

- New flash message proposing pages with blog records helps to find correct storage folder in backend module

- New settings validation check in frontend and backend help to ensure proper configuration

- Added FormError ViewHelper for easy Twitter Bootstrap form errors

- Introduce constants for easier setup

- Preview image now makes use of TYPO3's responsive image feature

- Improved documentation

- Cleaner code base and CGL improvements


**Templating**

Quite a few templates and partials have been changed to match Twitter Bootstrap 3.
Please make sure to adapt these changes in your templates.


**Responsive image rendering**

T3extblog is now able to make use of TYPO3's RWD image rendering.


How to upgrade
""""""""""""""

#. "Clear all cache" in Install Tool (including Opcode caches!)

#. Reload the TYPO3 backend

#. Adjust and update all templates!
