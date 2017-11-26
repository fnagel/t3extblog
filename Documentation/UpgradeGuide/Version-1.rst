.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _upgrade-guide:

Version 1.x
-----------

.. only:: html

	.. contents:: Within this page
		:local:
		:depth: 3


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
