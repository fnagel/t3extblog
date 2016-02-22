﻿.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _upgrade-guide:

Upgrade Guide
-------------

.. only:: html

	.. contents:: Within this page
		:local:
		:depth: 3



Upgrade from 2.1.x to 2.2.0
^^^^^^^^^^^^^^^^^^^^^^^^^^^

*"Author improvements"*


Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/2.1.0...2.2.0

- Use HTML files as email templates

- Author filter for post list action

- Show backend user (post author) avatar image (available since TYPO3 7.5)

- Backend module: Add a dashboard view

- Backend module: Add list views for post and blog subscriptions

- Multiple fixes and improvements

- Auto cache clearing when editing posts in backend (no more page TS config needed!)

- Greatly improved cache handling when creating new comments


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

https://github.com/fnagel/t3extblog/compare/1.2.1...2.0.0

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
See :ref:`Responsive image configuration <configuration-rwd-images>`


How to upgrade
""""""""""""""

#. "Clear all cache" in Install Tool (including Opcode caches!)

#. Reload the TYPO3 backend

#. Adjust and update all templates!



Upgrade from 1.1.x to 1.2.x
^^^^^^^^^^^^^^^^^^^^^^^^^^^

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/1.1.0...1.2.x

- Multi language support incl. improved BE module

- New image and text preview fields

- Removed ###MORE### marker support (migration update wizard is available)

- PHP Namespaces (TYPO3 CMS 7.x compatibility preparation)

- Support for meta tags (description, keywords, TwitterCards)

- A lot of bugfixes

- Removed quite a lot t3blog legacy code


.. important::
	Make sure to follow "How to upgrade" steps after updating!


**###MORE### marker**

This legacy functionality from t3blog was always an issue as it never worked as expected.
With introducing fields for preview image and text we finally can get rid of it!

The Install Tool Wizard provided will remove the marker where needed.
It will copy all text before the marker and paste it into our new *previewText* field.
When a *textpic* or *image* content element has been found before the marker,
the first of its images will be used as *previewImage* property.


**Templating**

Some post related templates and partials have been changed.
Please make sure to adapt these changes in your templates.

Some major changes:

* Now using namespaces for ViewHelpers, make sure to adjust your template references

* ViewHelper namespace in default templates has changed. Be careful!

* RenderPreview ViewHelper has been removed as no longer needed.



How to upgrade
""""""""""""""

#. "Clear all cache" in Install Tool (Important actions)

#. Clean up DB fields by using "Compare current database with specification" in Install Tool (Important actions)

#. Use the Install Tool Wizard if you are currently using the ###MORE### marker functionality

#. Adjust your templates

#. Adjust your blog post records if needed (depending on your setup)


.. tip::
	Some old, unused database fields have been removed. Don't be scared but make sure to have a back-up available.



Upgrade from 1.0.x to 1.1.0
^^^^^^^^^^^^^^^^^^^^^^^^^^^

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/1.0.1...1.1.0

- Improved FlashMessage ViewHelper

- Better localization in backend

- Improved backend module

- Bugfixes


How to upgrade
""""""""""""""

**Templating**

Some comment related partials and code parts have been changed.
Please make sure to adapt these changes in your templates.

Some backend module templates have been changed too.

**FlashMessage**

FlashMessage VH now extends the the Fluid default one. No more :code:`h5` and :code:`p` tags,
just some additional CSS classes for Bootstrap and the Fluid default :code:`ul` or :code:`div` mode.
Note that the error partial has changed accordingly.

Make sure styling still matches your needs as the HTML is slightly different now.

**Backend localization**

Some backend localization keys might have changed. Please check your overriding configuration.



Upgrade from 1.0.0 to 1.0.1
^^^^^^^^^^^^^^^^^^^^^^^^^^^

Changelog
"""""""""

https://github.com/fnagel/t3extblog/compare/1.0.0...1.0.1

- Removed EXT:sfantispam

- Improved documentation

- Fix integration for EXT:dd_googlesitemap

- Better extension and record icons

- Bugfixes


How to upgrade
""""""""""""""

*no changes required*


Upgrade from EXT:t3blog
^^^^^^^^^^^^^^^^^^^^^^^

Please see :ref:`Replace T3blog <replace-t3blog>`.