.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _installation:

Installation
============

The installation is quite simple. Just follow the instructions below.


#. Import and install the extension
	via the extension manager

#. Include static template:
	At least you have to include "T3Extblog: Default Setup (needed) (t3extblog)". When using RealURL you
	should include "T3Extblog: additional RealUrl config (t3extblog)". It works fine with RealURL-autoconfiguration,
	see :code:`typo3conf/ext/t3extblog/Classes/Hooks/RealUrl.php` for details.


	.. figure:: ../Images/Installation/includestatic.png
		:width: 669px
		:alt: include static

		Include static

#. Create pages and add plugins
	Add a page (for example 'blog') and add plugin 'blogsystem' (see :ref:`Administration manual <admin-manual>`).

#. Set the storage PID via TypoScript setup
	:code:`plugin.tx_t3extblog.persistence.storagePid = 123` (123 is the page id where you will store your blogposts, we
	recommend to use a storage folder).

#. Add this line to your TypoScript setup
	:code:`plugin.tx_t3extblog.settings.blogsystem.pid = 456` (456 is the page id where the plugin 'blogsystem' has been added).

#. Check settings
	i.e. blogName or handling of comments. See :code:`/Configuration/TypoScript/setup.txt` for details.