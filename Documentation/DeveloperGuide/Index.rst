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

_tbd_



TER deployment
--------------

TYPO3 TER deployment is done automated via GitHub hooks. Just add a version tag and push to GitHub.
See https://github.com/FluidTYPO3/fluidtypo3-gizzle for more information on the topic.

**How to release**

* Change version information in :code:`/ext_emconf.php`
* Change version information in :code:`/Documentation/Settings.yml`
* Commit changes: No [XYZ] prefix, this commit message will be the TYPO3 TER release notice
* Add tag to release commit (format: "1.2.3")
* Change version information in :code:`/ext_emconf.php` to next bugfix version + "dev" (example: "1.2.4dev")
* Push changes to GitHub (:code:`git push --tags`)


.. important::
	Please be careful when pushing tags.
	Do not push "non release" tags without changing the version number in :code:`/ext_emconf.php` to a dev version number
	(see above)
