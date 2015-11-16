﻿.. ==================================================
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


Translation / Localization
--------------------------

Localization works like for any other TYPO3 extension:

.. code-block:: typoscript

	plugin.tx_t3extblog._LOCAL_LANG {
		en {
			comment.human = Please confirm!
		}
	}


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



TYPO3 CMS version 4.5 - 4.7 support
-----------------------------------

There is a legacy branch for TYPO3 4.5-4.7. It's functional (tested in TYPO3 4.7) but needs some fixes backported.
Please open an issue on GitHub if you are interested in a bugfixed 4.5 branch.

Version 0.9.1 of this extension is NOT compatible with TYPO3 4.5 or 4.7 even if TER says otherwise.
You will need to use this branch: https://github.com/fnagel/t3extblog/tree/legacy