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


RealURL
^^^^

When using RealURL include the static template "T3Extblog: additional RealURL config (t3extblog)".

Now let the work do by the auto-configuration-function of RealURL. Take the output (configure RealURL to php-output (slow), so you can read it) and maybe put it to your own RealURL configuration and adapt it to your needs.


Some Output enhancements
----------

Category-Module
^^^^

Add number of articles per category
""""""""""

Just add <f:count>{category.posts}</f:count> to Templates/Category/List.html. This function is expensive, so we don´t add it to the standard template files.

Detail-View (Show post)
^^^^

Add prev/next-function
""""""""""

Link the previous / next article in the detais-view of a post. The following lines will do the job, add them in Templates/Post/Show.html


::

<f:if condition="{nextPost.linkParameter}">
<f:then>
<f:link.action controller="Post" action="show" pageUid="{settings.blogsystem.pid}" arguments="{nextPost.linkParameter}"> &lt; {nextPost.title} </f:link.action>
</f:then>
</f:if>
<f:if condition="{previousPost.linkParameter}">
<f:then>
<f:link.action controller="Post" action="show" pageUid="{settings.blogsystem.pid}" arguments="{previousPost.linkParameter}"> {previousPost.title} &gt; </f:link.action>
</f:then>
</f:if>
