.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _faq:

FAQ
====================

Some links are broken, i.e. categories, archive or rss
----------

**Solution:** Check if plugin.tx_t3extblog.settings.blogsystem.pid is set right (see :ref:`Installation <installation>`, 4.)

RSS Output instead of page
----------

**Solution** Remove static template "T3Extblog: Rss setup (t3extblog)". It should only be included on a seperate rss-page

Does it work together with "indexed search engine"?
----------

**Answer** Yes! T3extblog works together with "indexed search engine". Once your posts are indexed, your visitors can search through all your posts.
Your sould restrict indexing to the main content (blogposts) and exclude latest posts, the categorie module etc. to get usefull results.

Can I override the basic templates?
----------

**Answer** Of course! Copy the template files (t3extblog\Resources\PrivateSet\Layouts +Partials +Templates) in your fileadmin and set the path via TS or constant editor.

Does it work with dd_googlesitemap?
----------

**Answer** Yes! User this syntax: ?eID=dd_googlesitemap&sitemap=t3extblog&pidList=123 (123 = pid where the blogposts are stored). Add an optional 'limit' parameter for very large blogs. Example: '&limit=100'".
Since google supports RSS-Feeds, we recommend to use this solution!

Filtering tags doesn´t work?
----------

**Answer** To avoid filling the cache with not existing pages, the filter only works with tags => 3 letters.

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
