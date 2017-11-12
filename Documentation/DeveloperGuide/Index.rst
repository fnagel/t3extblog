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



Extending T3extlog
------------------
.. _dev-guide-extending:

Using interfaces
^^^^^^^^^^^^^^^^

Some internal services are implemented as interfaces. The actual class to use could be configured by TypoScript.
This way it is possible to use custom implementations.

See `config.tx_extbase.objects` configuration in [ext_typoscript_setup.txt](../../ext_typoscript_setup.txt).


Signal / Slot
^^^^^^^^^^^^^

T3extblog provides a couple of hooks to extend or change the default behavior.
This is done by using the signal / slot functionality provided by TYPO3.


**Available signals**

* `sendEmail` in `TYPO3\T3extblog\Service\EmailService`

* `subscriberConfirmAction` in `TYPO3\T3extblog\Controller\CommentSubscriberController`
* `subscriberDeleteAction` in `TYPO3\T3extblog\Controller\CommentSubscriberController`

* `subscriberConfirmAction` in `TYPO3\T3extblog\Controller\PostSubscriberController`
* `subscriberDeleteAction` in `TYPO3\T3extblog\Controller\PostSubscriberController`

* `processNewComment` in `TYPO3\T3extblog\Service\CommentNotificationService`
* `processChangedComment` in `TYPO3\T3extblog\Service\CommentNotificationService`
* `notifySubscribers` in `TYPO3\T3extblog\Service\CommentNotificationService`

* `processNewSubscriber` in `TYPO3\T3extblog\Service\BlogNotificationService`
* `processChangedSubscriber` in `TYPO3\T3extblog\Service\BlogNotificationService`
* `notifySubscribers` in `TYPO3\T3extblog\Service\BlogNotificationService`

* `spamCheck` in `TYPO3\T3extblog\Service\SpamCheckService`

* `prePersist` in `TYPO3\T3extblog\Controller\CommentController`


**Example code**

for using the EmailService sendEmail signal:

.. code-block:: php

	// typo3conf/ext/my_extension/ext_localconf.php
	$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
	    \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
	);
	$signalSlotDispatcher->connect(
	    TYPO3\T3extblog\Service\EmailService::class,
	    'sendEmail',
	    MyVendor\MyExtension\Slot\MyEmailServiceSlot::class,
	    'mySendEmailMethod'
	);


**More info**

on signal / slots in TYPO3:

* https://somethingphp.com/extending-classes-typo3/
* https://usetypo3.com/signals-and-hooks-in-typo3.html


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

Please consider opening a GitHub ticket so we can review new translations as soon as possible!

.. tip::

	German localization has been handled by local files before version `2.2.0`.
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

This needs to be done for latest supported TYPO3 versions in a multi-language site setup.
It is recommenced to install T3extblog in TYPO3'S default Bootstrap Package.


**What needs to be tested:**

* Creating blog posts (with and without preview image and text)
* Posting comments and subscribe for new comments
	* With and without SPAM check triggered
	* Test field validation
* Using the subscription manager (confirm and delete for new comment and post subscription)
* New comment subscription
	* admin, subscriber opt-in and notification emails
	* triggered by: frontend & backend (confirm and un-spam a comment)
	* make sure mails are sent with localized links for multi language setups
* New post subscription
	* subscriber opt-in (frontend) and notification emails (button in BE module)
* Run unit tests (see below)


**Quick test procedure**

* Create a new post
* Add comment with subscription (saved but marked as SPAM) -> admin email sent
* Mark as non SPAM using the dashboard (T3extblog BE module) -> subscription mail sent
* Confirm subscription by email link
* When in subscription manager, add new article subscription
* Add another comment (saved but marked as SPAM) -> admin email sent
* Edit the comment record you just created in "list" module
* Un-check the SPAM checkbox, save record -> new comment mail sent
* Click envelope icon in "all post" view (T3extblog BE module) -> New post subscription mail sent



Run unit tests
^^^^^^^^^^^^^^

This extension uses the `nimut/testing-framework` testing framework, see https://github.com/Nimut/testing-framework

.. code-block:: bash

	cd typo3conf/ext/t3extblog

	composer install

   ./vendor/bin/phpunit -c vendor/nimut/testing-framework/res/Configuration/UnitTests.xml ./Tests/Unit


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


**After a release**

* Make sure the release has been pushed to the TER
* Add t3x file rendered by TER to the GitHub release
* Update maintained translations (German) on http://translation.typo3.org


.. important::
	Please be careful when pushing tags.
	Do not push "non release" tags without changing the version number in :code:`/ext_emconf.php` to a dev version number
	(see above)
