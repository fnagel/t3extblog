.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _faq:

FAQ
====================

1. Some links are broken, i.e. categories, archive or rss

**Solution:** Check if plugin.tx_t3extblog.settings.blogsystem.pid is set right (see installation, 4.)

2.) RSS Output instead of page

**Solution** Remove static template "T3Extblog: Rss setup (t3extblog)". It should only be included on a seperate rss-page

3.) Does it work together with "indexed search engine"?

**Answer** Yes! T3extblog works together with "indexed search engine". Once your posts are indexed, your visitors can search through all your posts.
Your sould restrict indexing to the main content (blogposts) and exclude latest posts, the categorie module etc. to get usefull results.

4.) Can I override the basic templates?

**Answer** Of course! Copy the templetefiles (t3extblog\Resources\PrivateSet\Layouts +Partials +Templates) in your fileadmin and set the path via the constant editor.

5.) Does it work with dd_googlesitemap?

**Answer** Yes! User this syntax: ?eID=dd_googlesitemap&sitemap=t3extblog&pidList=123 (123 = pid where the blogposts are stored). Your posts should all listet. If you got a lot of posts, you need to add the parameter &limit=123456 to get all posts listed in the sitemap.
