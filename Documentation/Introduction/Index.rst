.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


What does it do?
================

A flexible blog extension for TYPO3 CMS powered by Extbase / Fluid which aims to replace t3blog.


Currently implemented features:

* Blog systems with comments, categories and tags
* Use all TYPO3 content elements within your blog posts
* Views: list, detail, latest, categories, archive, latest comments (each is a FE plugin)
* Filter posts by category, tag or author
* Fields for preview text and image
* Subscriptions for new comments and new blog posts
* Wordpress like subscription manager (manage all subscriptions from a dashboard)
* Opt-In mails for subscriptions with configurable expiration date
* Configurable spam check: "I am human" checkbox, honeypot, cookie and user agent
* Auto close comments functionality
* Allow some HTML tags in comment message
* RSS feed for posts
* BE Module with dashboard and lists for posts, comments and subscriptions
* Default HTML markup matches Twitter Bootstrap 3.x
* Reasonable email sending, even when you accept comments in BE (extension module AND default list module, uses TCEMAIN)
* Send HTML and / or text emails
* Preview of hidden posts
* Supports migration (of EXT:t3blog) to and is tested in TYPO3 6.2 and 7.x
* Multi language support
* Link validator support
* Using Pootle translation server (translation.typo3.org)
* A few unit tests

.. tip::

	Visit & contribute: https://github.com/fnagel/t3extblog