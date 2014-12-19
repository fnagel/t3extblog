.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _Replace T3Blog:

Replace T3Blog
====================

You have a TYPO3 4.5. with T3Blog installed. Goal is to get your blog running with TYPO3 6.2.

1.	Recommended: clone your system and make it ready for the update (update extensions, remove unused extensions...)
2.	Update to TYPO3 6.2, run the update wizard. If needed, deinstall t3blog. Important: When running the database analyser, DON´T remove any t3blog tables!
3.	Deinstall dam/dam index and t3blog (if not done before, DON´T remove any t3blog tables yet)
4.	Install t3extblog!
5.	Clean up (deleting deinstalled extensions, run the database analyser)

Now some templating must be done, but all your blogposts, comments etc. should be already available.




