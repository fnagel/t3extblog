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

.. important::

	This guide will use the "new" way of configuring an extension: site sets settings

T3Extblog can be installed quickly by using the site settings. You need to perform the following steps:

#. Import and install the extension
	- via the extension manager
	- or using composer

#. Include configuration (site set)
	Include the extension site set using the Sites module:

	.. figure:: ../Images/Installation/site-set.png
		:alt: Include site set

#. Create a simple page structure for your blog
	At least we need a page for the actual blog and a page for the subscription manager.
   We recommend to create a sysfolder for your blogposts too.

	.. figure:: ../Images/QuickInstallation/quickinstallation_folderstructur.png
		:alt: Recommended page structure for quick installation

#. Add plugins to the pages:
	On our main blog page we insert the plugin 'Blogsystem'
   (see :ref:`Administration manual <admin-manual>` for the other plugins we can use).

	.. figure:: ../Images/Installation/plugin_blogsystem.png
		:alt: Insert the "Blogsystem"

	On our subscription manager page, we need to add the "Subscription Manager" plugin.

#. Configure settings in the settings module:
	.. figure:: ../Images/Installation/site-set-configuration.png
		:alt: Extension site set settings

See :ref:`Configuration <configuration>` for all possible settings.
