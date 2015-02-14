.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _configuration:

Configuration
====================

Like most other extensions, settings can be overwritten via TypoScript.

Have a look at :code:`/Configuration/TypoScript/setup.txt`.


Minimal configuration
---------------------

Two configs are needed in any case:

.. code-block:: typoscript

	# PID where your blogsystem is included
	plugin.tx_t3extblog.settings.blogsystem.pid = 123

	# PID where you will store your blogposts
	plugin.tx_t3extblog.persistence.storagePid = 456


RealURL
-------

When using EXT:realurl or similar extension you will need to add additional staticTS template
"T3Extblog: additional RealUrl config (t3extblog)" (:code:`/Configuration/TypoScript/RealUrl/setup.txt`).

.. important::
	Add this static TS to the blogsystem plugin page only to preserve cache!

