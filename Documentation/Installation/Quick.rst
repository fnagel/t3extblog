.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _quick_installation:


Quick Installation
==================

Target group: **Integrators**


Quick installation guide
------------------------

T3Extblog can be installed quickly by using the constant editor. You need to perform the following steps:

#. Install the extension via the Extension-Manager

#. Include static template:
	This can be done on your root-page or in an extension template places on a specific page.
	Minimum requirement: `T3Extblog: Default Setup (needed) (t3extblog)`.
	Do NOT include `T3Extblog: Rss setup (t3extblog)`! We will need this elsewhere.

	.. figure:: ../Images/Installation/includestatic.png
		:alt: Include static

#. Create a simple page structure for your blog
	At least we need a page for the blogsystem and a page for the Subscription Manager. We recommend to create an sysfolder for your blogposts to.

	.. figure:: ../Images/QuickInstallation/quickinstallation_folderstructur.png
		:alt: Recommended page structure for quick installation

#. add plugins to the pages:
	On our "main-page" we insert the plugin 'blogsystem' (see :ref:`Administration manual <admin-manual>` for the other plugins we can use).

	.. figure:: ../Images/Installation/plugin_blogsystem.jpg
		:alt: Insert the "blogsystem"

	On our "subscription"-page, we have to add the "Subscription Manager".

#. Make Settings in the Constant Editor:
	.. figure:: ../Images/QuickInstallation/constant_editor.png
		:alt: Settings Constant Editor


See :ref:`Configuration <configuration>` for all possible settings.

.. important::

	Except Twitter name and image sizes, all of these settings are mandatory!


