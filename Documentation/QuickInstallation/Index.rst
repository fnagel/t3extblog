.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _quick_installation:


Quick Installation
============

Target group: **Administrators**



Quick installation guide
--------------------

T3Extblog can be installed quickly by using the constant editor. You need to perform the following steps

#. Install the extension via the Extensionmanager

#. Include static template:
	This can be done on your root-page or in an extension template places on a specific page.
	Minimum requirement: "T3Extblog: Default Setup (needed) (t3extblog)".
	Do NOT include "T3Extblog: Rss setup (t3extblog)"! We will need this elsewhere.

	.. figure:: ../Images/Installation/includestatic.png
		:width: 674px
		:alt: include static

		Include static

#. Create a simple page structure for your blog
	At least we need a page for the blogsystem and a page for the Subscription Manager. We recommend to create an sysfolder for your blogposts to.

	.. figure:: ../Images/QuickInstallation/quickinstallation_folderstructur.png
		:width: 219px
		:alt: typical page structure

		recommended page structure for quick installation

#. add plugins to the pages:
	On our "main-page" we insert the plugin 'blogsystem' (see :ref:`Administration manual <admin-manual>` for the other plugins we can use).

	.. figure:: ../Images/Installation/plugin_blogsystem.jpg
		:width: 627px
		:alt: insert the "blogsystem"

		insert the "blogsysten"

	On our "subscription"-page, we have to add the "Subscription Manager".

#. Make Settings in the Constant Editor:
	.. figure:: ../Images/QuickInstallation/constant_editor.png
		:width: 627px
		:alt: constant editor

		Settings Constant Editor

You need to do the following settings:

- Admin notification mails: Mailadress where notification mails were send to
- Blogsystem Pid: PageId where the "blogsystem" is
- Default storage PID: PageId of your blog-sysfolder (where your blogposts are stored)
- Subscription Manager Pid: PageId of your Subscription Manager

