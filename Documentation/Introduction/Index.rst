.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. contents:: Within this page
   :local:
   :depth: 3


What does it do?
================

A record based blog extension for TYPO3 CMS powered by Extbase / Fluid. Flexible and powerful!


Currently implemented features:


**General**

* Blog system with posts, categories, tags, comments and subscriptions
* Use all TYPO3 content elements within your blog posts
* BE Module with dashboard and lists for posts, comments and subscriptions
* Multiple core dashboard widgets
* Views: list, detail, latest, categories, archive, latest posts and comments (each is a FE plugin)
* Filter posts by category, tag or author
* Fields for preview text and image in list view
* RSS feed for posts
* Preview of hidden posts (drafts)
* Multi language support
* Link validator support
* Speaking URLs support
* Sitemap support


**Comments and subscriptions**

* Subscriptions for new comments and new blog posts
* Wordpress like subscription manager (manage all subscriptions from a dashboard)
* Opt-In mails for subscriptions with configurable expiration date
* Configurable spam check: "I am human" checkbox, honeypot, cookie and user agent
* Email address for comments is optional (but will be enforced if subscription checkbox is enabled)
* Auto close comments functionality
* Allow some HTML tags in comment message
* GDPR / DSGVO checkboxes
* Reasonable email sending, even when you accept comments in BE (extension module AND default list module, using TCEMAIN hooks)
* Send HTML and / or text emails


**Developer related**

* Reasonable cache handling (using cache tags)
* Default HTML markup matches Twitter Bootstrap 3.x or 4.x
* Using Pootle translation server (translation.typo3.org)
* Using interfaces and signal / slot for easy extending
* Supports migration (of EXT:t3blog) and is tested in TYPO3 6-10
* A few unit tests


.. tip::

	Visit & contribute: https://github.com/fnagel/t3extblog


Screenshots
-----------

**Frontend**

Blogsystem

.. figure:: ../Images/AdministratorManual/blogsystem.png
	:alt: Blogsystem


Comment form

.. figure:: ../Images/Screenshots/comment.png
	:alt: Comment form


Archive

.. figure:: ../Images/AdministratorManual/archive.png
	:alt: Archive


Subscription Manager

.. figure:: ../Images/AdministratorManual/subscription-manager.png
	:alt: Subscription Manager


**Backend**

Dashboard

.. figure:: ../Images/Screenshots/dashboard.png
	:alt: Dashboard backend module


Post records list

.. figure:: ../Images/UserManual/module.png
	:alt: Post records backend module
