.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _upgrade-guide:

Upgrade Guide
-------------

.. only:: html

	.. contents:: Within this page
		:local:
		:depth: 3



Upgrade from 1.0.x to 1.1.0
^^^^^^^^^^^^^^^^^^^^^^^^^^^

**Changelog**

- Improved FlashMessage ViewHelper

- Better localization in backend

- Improved backend module

- Bugfixes



**How to upgrade**

FlashMessage VH now extends the the Fluid default one.

No more :code:`h5` and :code:`p` tags, just some additional CSS classes for Bootstrap and the Fluid default
:code:`ul` or :code:`div` mode. Note that the error partial has changed accordingly.

Make sure styling still matches your needs as the HTML is slightly different now.


