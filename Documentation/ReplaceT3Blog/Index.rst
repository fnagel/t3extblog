﻿.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _replace-t3blog:

Replace T3Blog
==============

Target group: **Developers**

.. important::

	Please note this is only a rough outline on how to update TYPO3 project from 4.x to 6.x.


You have a TYPO3 4.5. with T3Blog (EXT:t3blog) installed. Goal is to get your blog running with TYPO3 6.2.

#.	Recommended: clone your system and make it ready for the update (update extensions, remove unused extensions...)
#.	Update to TYPO3 6.2, run the update wizard. If needed, deinstall t3blog. Important: When running the database analyser, DON´T remove any t3blog tables!
#.	When used deinstall dam/dam index. Otherwise go on to 4.
#.	Deinstall t3blog if not done before (Don't remove any t3blog tables!)
#.	Install t3extblog! Make sure to create / modify database fields.
#.	Run the "Find existing comments with invalid author or email" update-script (in Extensions-Manager) and update invalid comments (empty author or email)
#.	Run the "Set mails_sent flag for existing comments" update-script (in Extensions-Manager)
#.	Run the Install Tool Wizard (in Install Tool)
#.	Clean up (deleting deinstalled extensions, run the database analyser)

You will need to adjust your templates and TS configuration, but all your blogposts, comments etc. should be already available.


.. tip::
	Make sure to read the :ref:`Upgrade Guide <upgrade-guide>`.
