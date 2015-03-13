TYPO3 t3extblog
===============

A flexible blog extension for TYPO3 CMS powered by Extbase / Fluid which aims to replace t3blog.


Current state
-------------
Beta - in production use but missing some features from the original extension.


Idea & goals
------------
This extension aims to be an improved replacement for EXT:t3blog. It's based upon Extbase and Fluid.
Very easy data migration as a slightly modified SQL and TCA is used.

This extension could (but should probably not) be used for starting a new blog with TYPO3.
Keep in mind: TYPO3 is not a dedicated blogging platform.


Features
--------

Currently implemented features:

* Supports migration to and is tested in TYPO3 6.2.
* Show list, latest and detail view of posts
* Archive and category view
* Add new comments
* Allow some HTML tags in comment message
* Wordpress like subscription manager (manage all subscriptions from a dashboard)
* Configurable spam check: "I am human" checkbox, honeypot, cookie and useragent
* Opt-In mails for comment subscription with expiration date
* Auto close comments functionality
* RSS feed for posts
* BE Module for posts and comments
* Default HTML markup matches Twitter Bootstrap 2.3
* Reasonable email sending, even when you accept comments in BE (extension module AND default list module, uses TCEMAIN)
* Preview of hidden posts (add `tx_t3extblog.singlePid` to your page TSconfig)
* A few unit tests


Documentation
-------------

See `/Documentation` directory or online: http://docs.typo3.org/typo3cms/extensions/t3extblog/


Installation
------------

NOTE: This guide may be outdated. Please give feedback!

* (Deinstall EXT:t3blog)
* Install EXT:t3extblog
* Update DB in EM
* Use update script in EM
* Add static TS template
* Create pages and add plugins
* Set pid's in TypoScript
* Configure templates


Todo & Known bugs
-----------------

Please see Github Issues: https://github.com/fnagel/t3extblog/issues


Contribution
------------

Any help is appreciated. Please feel free to drop me a line, open issues or send pull requests.

A lot of testing needs to be done. Help wanted!

It is possible to sponsor features and bugfixes!


Donation
--------

Please consider a donation: http://www.felixnagel.com/donate/