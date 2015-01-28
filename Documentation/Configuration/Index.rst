.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _configuration:

Configuration
====================

Settings
----------

Like most other extensions, settings can be overwritten via TypoScript. Have a look at /Configuration/TypoScript/setup.txt.

Needed configs
^^^^

Two configs are needed in any case:

::

	# PID where your blogsystem is included
	plugin.tx_t3extblog.settings.blogsystem.pid = 111
	# PID where you will store your blogposts
	plugin.tx_t3extblog.persistence.storagePid = 222


RealURL
^^^^

When using EXT:realurl or similar extension you will need to add additional staticTS template "T3Extblog: additional RealUrl config (t3extblog)" (/Configuration/TypoScript/RealUrl/setup.txt). Add this static TS to the blogsystem plugin page only to preserve cache!


Example
^^^^

Example code for changing the pagination values:


::

	plugin.tx_t3extblog {
		settings {
			blogsystem {
				posts {
					paginate {
					# items (=blogposts) per page
					itemsPerPage = 2
				# pagination above the blogposts, 1 = yes, 0 = no
					insertAbove = 1
				# pagination below the blogposts, 1 = yes, 0 = no
					insertBelow = 1
					# maximum number of pagination links
					maximumNumberOfLinks = 50
					}
				}
			}
		}
	}