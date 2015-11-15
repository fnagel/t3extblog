.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

.. _developer-guide:

Developer Guide
===============

Target group: **Administrators**


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
