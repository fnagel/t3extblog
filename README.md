TYPO3 t3extblog
===============

This is a TYPO3 Extbase / Fluid extension which aims to replace t3blog.


Current state
------------
Not for production use - alpha! Broken commits ahead!



Idea & goals
------------
I'm tired of hardly maintained extensions and hard to maintain code with unclean logic.
Tried to built a clean Extbase / Fluid alternative around the old codebase.
Migration not needed as old SQL and TCA is used.

This extension should not be used for starting a new blog with TYPO3.

More to come...


Features
------------

* Show list and detail view of posts
* Post new comments
* Wordpress like subscription manager
* Configurable spam check for comments and subscription requests
* Spam check includes: simple checkbox, honeypot, cookie, useragent, and sfpantispam
* Auto close comments functionality 
* Posts RSS feed
* more to come...


Installation
------------

* You need a working installation of t3blog.
* Install t3extblog
* Add static template
* Set storagePid to blog post storage page


Todo
------------

TESTING!

* ViewHelper for more marker
* Cleanup extension from extension builder files
* Add documentation
* Use translation instead of hardcoded messages

IN WORK
* BE Module for posts

FEATURES
* BE Module for comments
* Trackback / Pingback
* Blogroll?
* Hooks for list module when changing comments?
* Captcha implementation


To discuss
------------



Contribution
------------

Any help is appreciated. Please feel free to drop me a line, open issues or send pull request.