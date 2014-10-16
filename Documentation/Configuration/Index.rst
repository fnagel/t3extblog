.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _configuration:

Configuration
====================

A lot of settings can be done via TypoScript, have a look at /Configuration/TypoScript/setup.txt .
You can override the values by copying the whole content or parts of the setup.txt into your template-setup and modify the values.

Example:
This part would be change some pagination values:


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

RSS-Settings: have a look at /Configuration/TypoScript/RSS/setup.txt
You can override the values by using an extension template on the page where your have insert the Rss-modul.
