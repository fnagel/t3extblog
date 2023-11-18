.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

.. _developer-guide:

Developer Guide
===============

Target group: **Developers**


.. contents:: Within this page
   :local:
   :depth: 3



Extending T3extlog
------------------
.. _dev-guide-extending:

Using interfaces
^^^^^^^^^^^^^^^^

Some internal services are implemented as interfaces. The actual class to use could be configured.
This way it is possible to use custom implementations.

**Before TYPO3 10**
See `config.tx_extbase.objects` configuration in [ext_typoscript_setup.txt](../../ext_typoscript_setup.txt).

**TYPO3 10 and later**
See interface registration configuration in [Services.yml](../../Configuration/Services.yml).

More info on the topic:
https://docs.typo3.org/m/typo3/reference-coreapi/master/en-us/ApiOverview/DependencyInjection/Index.html


Events
^^^^^^
.. _dev-guide-extending-events:

T3extblog provides a couple of events to extend or change the default behavior.
This is done by using PSR-14 events functionality provided by TYPO3.

**Available events**

* `Comment\CreatePrePersistEvent` (former `prePersist` signal)
   in `FelixNagel\T3extblog\Controller\CommentController:createAction`

* `Comment\SubscriberConfirmEvent` (former `subscriberConfirmAction` signal)
   in `FelixNagel\T3extblog\Controller\PostSubscriberController:confirmAction`
* `Comment\SubscriberDeleteEvent` (former `subscriberDeleteAction` signal)
   in `FelixNagel\T3extblog\Controller\PostSubscriberController:deleteAction`

* `Post\SubscriberConfirmEvent` (former `subscriberConfirmAction` signal)
   in `FelixNagel\T3extblog\Controller\BlogSubscriberController:confirmAction`
* `Post\SubscriberDeleteEvent` (former `subscriberDeleteAction` signal)
   in `FelixNagel\T3extblog\Controller\BlogSubscriberController:deleteAction`

* `Comment\Notification\CreateEvent` (former `processNewComment` signal)
   in `FelixNagel\T3extblog\Service\CommentNotificationService`
* `Comment\Notification\ChangedEvent` (former `processChangedComment` signal)
   in `FelixNagel\T3extblog\Service\CommentNotificationService`
* `Comment\Notification\SubscribersEvent` (former `notifySubscribers` signal)
   in `FelixNagel\T3extblog\Service\CommentNotificationService`

* `Post\Notification\CreateEvent` (former `processNewSubscriber` signal)
   in `FelixNagel\T3extblog\Service\BlogNotificationService`
* `Post\Notification\ChangedEvent` (former `processChangedSubscriber` signal)
   in `FelixNagel\T3extblog\Service\BlogNotificationService`
* `Post\Notification\SubscribersEvent` (former `notifySubscribers` signal)
   in `FelixNagel\T3extblog\Service\BlogNotificationService`

* `SpamCheckEvent` (former `spamCheck` signal)
   in `FelixNagel\T3extblog\Service\SpamCheckService`

* `SendEmailEvent` (former `sendEmail` signal) in `FelixNagel\T3extblog\Service\EmailService`


**More info**

on PSR-14 events  in TYPO3:

* https://docs.typo3.org/m/typo3/reference-coreapi/main/en-us/ApiOverview/Events/EventDispatcher/Index.html
* https://usetypo3.com/psr-14-events.html


Code insights
-------------

*This section is incomplete*


**Table mapping**

This extension uses TYPO3 Extbase table mapping to make use of existing EXT:t3blog tables.
Take a look at :code:`ext_typoscript_setup.txt` to see how this is done.


Translations
------------

Translations could be added here:
https://crowdin.com/project/typo3-extension-t3extblog

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
   * Test prefilling fields
* Using the subscription manager (confirm and delete for new comment and post subscription)
* New comment subscription
	* Admin, subscriber opt-in and notification emails
	* Triggered by:
      * Frontend & backend (confirm and un-spam a comment)
      * Edit button (BE module and core dashboard) and direct spam / confirmed toggle buttons
	* Make sure mails are sent with localized links for multi language setups
* New post subscription
	* Subscriber opt-in (frontend) and notification emails (button in BE module)
* Dashboard widgets work as expected
* Test request throttling
* PSR-14 events


**Quick test procedure**

Make sure to have all related pages, folders and blog categories localized!

* Create a new post and translate it
* Add comment with subscription (saved but marked as SPAM)
   * admin email sent
* Mark as non SPAM using the dashboard (in blog BE module)
   * subscription mail sent
* Confirm subscription by email link (check localization and link)
* When in subscription manager -> add new article subscription
* Add another comment (saved but marked as SPAM)
   * admin email sent
* Edit the comment record you just created (in list BE module)
* Un-check the SPAM checkbox, save record
   * new comment mail sent (check localization and link)
* Add a valid comment for the translated post
   * admin mail sent (check localization and link)
* Make sure there
   * are two comments displayed for the default language post and only one for the localized post
   * is no "new comment" email for the default language subscription
* Click envelope icon in "all post" view (T3extblog BE module) -> New post subscription mail sent
* Check dashboard blog widgets functionality


TER deployment
--------------

TYPO3 TER deployment is done automated via GitHub action workflow. Just add a version tag and push to GitHub.
See `/.github/workflows/release.yml` for more information.


**How to release**

* Create upgrade guide!
* Change version information in :code:`/ext_emconf.php`
* Change year in :code:`/Documentation/Settings.cfg` if needed
* Commit changes: Use [RELEASE] prefix, this commit message will be the TYPO3 TER release notice
* Add tag to release commit (format: "1.2.3")
* Change version information in :code:`/ext_emconf.php` to next bugfix version + "dev" (example: "1.2.4dev")
* Push GIT changes incl. tag


**After a release**

* Make sure the release has been pushed to the TER
* Add t3x file rendered by TER to the GitHub release
* Update maintained translations (German) on https://crowdin.com/project/typo3-extension-t3extblog


.. important::
	Please be careful when pushing tags.
	Do not push "non release" tags without changing the version number in :code:`/ext_emconf.php` to a dev version number
	(see above)
