.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

.. _faq:

FAQ
====================

.. only:: html

	.. contents:: Within this page
		:local:
		:depth: 3



Some links are broken, i.e. categories, archive or rss
------------------------------------------------------

Check if :code:`plugin.tx_t3extblog.settings.blogsystem.pid` is set right (see :ref:`Installation <installation>`, 4.).


RSS Output instead of page
--------------------------

Remove static template `T3Extblog: Rss setup (t3extblog)`. It should only be included on a seperate rss-page.


Link RSS in page header
-----------------------

Use following TS config where `123` is the UID of yours RSS page:

.. code-block:: typoscript

	page.headerData {
		100 =  TEXT
		100.wrap = <link rel="alternate" type="application/rss+xml" title="Blog RSS Feed" href="|" />
		100.typolink {
			parameter = 123
			returnLast = url
			forceAbsoluteUrl = 1
		}
	}


Add canonical tag to pae header
-------------------------------

Use following TS config:

.. code-block:: typoscript

	[globalVar = GP:tx_t3extblog_blogsystem|post > 0]
		page.headerData.123 {
		   wrap = <link rel="canonical" href="|">
         htmlSpecialChars = 1

         typolink {
            parameter.data = TSFE:id
            forceAbsoluteUrl = 1
            returnLast = url
            additionalParams.cObject = COA
            additionalParams.cObject {
               10 = TEXT
               10.dataWrap = &tx_t3extblog_blogsystem[controller]=Post&tx_t3extblog_blogsystem[action]=show&tx_t3extblog_blogsystem[post]={GP:tx_t3extblog_blogsystem|post}&tx_t3extblog_blogsystem[year]={GP:tx_t3extblog_blogsystem|year}&tx_t3extblog_blogsystem[month]={GP:tx_t3extblog_blogsystem|month}&tx_t3extblog_blogsystem[day]={GP:tx_t3extblog_blogsystem|day}
               10.if.isTrue.data = GP:tx_t3extblog_blogsystem|post
            }
         }
		}
	[global]


Does it work together with "indexed search engine"?
---------------------------------------------------

Yes! T3extblog works together with "indexed search engine". Once your posts are indexed, your visitors can search through all your posts.
Your sould restrict indexing to the main content (blogposts) and exclude latest posts, the categorie module etc. to get usefull results.


Can I override the basic templates?
-----------------------------------

Copy the template files (:code:`t3extblog\Resources\PrivateSet\Layouts` +Partials +Templates)
in your fileadmin or custom extension and set the path via TS or constant editor.

See :ref:`Configuration <configuration-overwrite-templates>` for code snippets!

Does it work with dd_googlesitemap?
-----------------------------------

Yes! Use this syntax: :code:`?eID=dd_googlesitemap&sitemap=t3extblog&pidList=123` (where 123 is your storage folder page id).
Add an optional 'limit' parameter for very large blogs. Example: :code:`&limit=100`.

.. tip::

	Since google supports RSS-Feeds, we recommend to use this solution!


Filtering tags doesn't work?
----------------------------

To avoid filling the cache with not existing pages, the filter only works with tags => 3 letters.


Set default post category
-------------------------

Add this to the page or user TS config:

.. code-block:: typoscript

	TCAdefaults {
		tx_t3blog_post {
			# CSV of category UIDs
			cat = 1,2,3

			# Works for other fields too!
			hidden = 0
		}
	}


Disable post fields
-------------------

Add something similar to the page or user TS config:

.. code-block:: typoscript

	TCEFORM.tx_t3blog_post {
		# Just some examples...
		hidden.disabled = 1
		starttime.disabled = 1
		endtime.disabled = 1

		preview_mode.disabled = 1
		preview_text.disabled = 1
	}


.. Hint::

	Do not hide the `date` field as it's needed for sorting!


Translation / Localization
--------------------------

Localization works like for any other TYPO3 extension:

.. code-block:: typoscript

	plugin.tx_t3extblog._LOCAL_LANG {
		en {
			comment.human = Please confirm!
		}
	}


**Adding localizations**

Feel free to add new localization!

TYPO3 Pootle project: http://translation.typo3.org/projects/TYPO3.TYPO3.ext.t3extblog/

More information about TYPO3 translation server: https://wiki.typo3.org/Translations


Clear frontend cache
--------------------
.. _faq-clear-cache:

Since version 2.2.0 this extension makes use of cache tags. The following cache tags are available:

* tx_t3extblog
* tx_t3extblog_PID
* tx_t3blog_post_uid_UID
* tx_t3blog_post_pid_PID
* tx_t3blog_com_pid_PID
* tx_t3blog_cat_pid_PID

*PID = page uid, UID = record uid*


Cache tags are cleared by a built-in functionality when a record is modified or created in backend and frontend.

This works by adding cache tags for each page rendered with a t3extblog plugin. Each time a blog record is edited,
deleted or created, this cache entry is flushed. No additional cache configuration is needed.

It's still possible to use TS config for adjusting the cache behaviour. This could be basic configuration (see below) or
using a more advanced tag approach:

.. code-block:: typoscript

	# Flushes all blog related caches
	TCEMAIN.clearCacheCmd = cacheTag:tx_t3extblog

	# Flushes cache for all records in page with UID 123
	TCEMAIN.clearCacheCmd = cacheTag:tx_t3extblog_123


Before version 2.2.0
^^^^^^^^^^^^^^^^^^^^

When a **frontend user adds a new comment** the blogsystem plugin page cache is cleared using default TYPO3 extbase
functionality.


When **editing records in backend** (for example posts or comments) as a non admin user the cache needs to be cleared
manually or by using page TS config:

.. code-block:: typoscript

	# PIDs of page which need to be cleared
	TCEMAIN.clearCacheCmd = 123,456,789


The code needs to be added to the sys folder where the blog records are edited.


.. Hint::

	The mentioned TCEMAIN settings are part of the TYPO3 core and can be used therefore not only for the t3extblog extension.


Some output tweaks
------------------


Category-Module
^^^^^^^^^^^^^^^

Add number of articles per category
"""""""""""""""""""""""""""""""""""

Just add :code:`<f:count>{category.posts}</f:count>` to :code:`Templates/Category/List.html`.
This function is expensive, so we don't add it to the standard template files.


Detail-View (Show post)
^^^^^^^^^^^^^^^^^^^^^^^

Add prev/next-function
""""""""""""""""""""""

Link the previous / next article in the detais-view of a post. The following lines will do the job, add them in Templates/Post/Show.html


.. code-block:: xml

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



TYPO3 CMS legacy support
------------------------

Version 6.0 - 6.2
^^^^^^^^^^^^^^^^^

Version 3.0.0 of this extension will no longer support TYPO3 6.2 LTS. Some bugfixes and small features might be
backported to this branch: https://github.com/fnagel/t3extblog/tree/2.x


Version 4.5 - 4.7
^^^^^^^^^^^^^^^^^

There is a legacy branch for TYPO3 4.5-4.7. It's functional (tested in TYPO3 4.7) but needs some fixes backported.
Please open an issue on GitHub if you are interested in a bugfixed 4.5 branch.

Version 0.9.1 of this extension is NOT compatible with TYPO3 4.5 or 4.7 even if TER says otherwise.
You will need to use this branch: https://github.com/fnagel/t3extblog/tree/legacy
