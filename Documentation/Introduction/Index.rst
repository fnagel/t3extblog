.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


What does it do?
================

This is a TYPO3 Extbase / Fluid extension which aims to replace t3blog. It is easy to implement and use.


Currently implemented features:

* Blog systems with comments, categories and tags
* Use all TYPO3 content elements within your blog posts
* Views: list, detail, latest, categories, archive, latest comments (each is a FE plugin)
* Allow some HTML tags in comment message
* Wordpress like subscription manager (manage all subscriptions from a dashboard)
* Configurable spam check: "I am human" checkbox, honeypot, cookie, useragent, and EXT:sfpantispam support
* Opt-In mails for comment subscription with expiration date
* Auto close comments functionality
* RSS feed for posts
* BE Module for posts and comments
* Default HTML markup matches Twitter Bootstrap 2.3
* Reasonable email sending, even when you accept comments in BE (extension module AND default list module, uses TCEMAIN)
* Preview of hidden posts
* Supports migration to and is tested in TYPO3 6.2.
* A few unit tests


.. tip::

	Visit & contribute: https://github.com/fnagel/t3extblog