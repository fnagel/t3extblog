.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

.. _developer-guide:

Developer Guide
===============

Target group: **Developers**


.. only:: html

	.. contents:: Within this page
		:local:
		:depth: 3



Code insights
-------------

*This section is incomplete*


**Table mapping**

This extension uses TYPO3 Extbase table mapping to make use of existing EXT:t3blog tables.
Take a look at :code:`ext_typoscript_setup.txt` to see how this is done.


Translations
------------

Translations could be added here:
http://translation.typo3.org/projects/TYPO3.TYPO3.ext.t3extblog/

*Please note:*
German localization is configured in `Resources/Private/Language/de.*.xlf`, not within the TYPO3 Pootle instance.
Please see this issue for more information: https://github.com/fnagel/t3extblog/issues/99



Documentation
-------------

Anyone is very welcome to help improving our documentation!
Just send a pull request or add an issue at GitHub.

To view your changes before submitting them you will need to install following extensions:

* EXT:sphinx
* EXT:restdoc

See this link for more information: https://docs.typo3.org/typo3cms/extensions/sphinx/WritersManual/SphinxRest/Index.html


Testing
-------

**What needs to be tested:**

* Creating blog posts (with and without preview image and text)
* Posting comments and subscribe for new comments
* Using the subscription manager (confirm and delete for new comment and post subscription)
* New comment subscription
	* admin, subscriber opt-in and notification emails
	* triggered by: frontend & backend (confirm and un-spam a comment)
* New post subscription
	* subscriber opt-in (frontend) and notification emails (button in BE module)


TER deployment
--------------

TYPO3 TER deployment is done automated via GitHub hooks. Just add a version tag and push to GitHub.
See https://github.com/FluidTYPO3/fluidtypo3-gizzle for more information on the topic.

**How to release**

* Change version information in :code:`/ext_emconf.php`
* Change version information in :code:`/Documentation/Settings.yml`
* Change version information in :code:`/composer.json`
* Commit changes: No [XYZ] prefix, this commit message will be the TYPO3 TER release notice
* Add tag to release commit (format: "1.2.3")
* Change version information in :code:`/ext_emconf.php` to next bugfix version + "dev" (example: "1.2.4dev")
* Push changes to GitHub (:code:`git push --tags`)


.. important::
	Please be careful when pushing tags.
	Do not push "non release" tags without changing the version number in :code:`/ext_emconf.php` to a dev version number
	(see above)
