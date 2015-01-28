.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _installation:

Installation
====================

The installation is quite simple. Just follow the instructions below.


1. Import and install the extension via the extension manager
2. Include static: At least you have to include "T3Extblog: Default Setup (needed) (t3extblog)". When using RealURL you should include "T3Extblog: additional RealUrl config (t3extblog)". It works fine with RealURL-autoconfiguration, see see typo3conf/ext/t3extblog/Classes/Hooks/RealUrl.php for details.


.. figure:: ../Images/Installation/includestatic.png
	:width: 669px
	:alt: include static

	Include static

3. Now set the storage PID via TS: **plugin.tx_t3extblog.persistence.storagePid = 123** (123 is the pageid where you will store your blogposts)

4. Now add this line to your template-setup: **plugin.tx_t3extblog.settings.blogsystem.pid = 456** (456 is the pageid where the modul „blogsystem“ will be.
5. Check, if you want to do some more settings, i.e. blogName or handling of comments. See /Configuration/TypoScript/setup.txt for details.