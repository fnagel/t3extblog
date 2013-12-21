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

This extension will support migration to TYPO3 6.x.


Features
------------

Implemented features.

* Show list and detail view of posts
* Post new comments
* Wordpress like subscription manager
* Configurable spam check for comments and subscription requests
* Spam check includes: simple checkbox, honeypot, cookie, useragent, and sfpantispam
* Auto close comments functionality 
* Posts RSS feed
* BE Module for posts and comments


Installation
------------

* You need a working installation of t3blog.
* Install t3extblog
* Add static template
* Set storagePid to blog post storage page


Todo
------------

A lot of testing is missing. Help wanted!

* Add documentation


IN WORK
* Templating (Twitter Boostrap)
* Use translation instead of hardcoded messages
* Hook for comment records to be changed -> send mails

FEATURES
* Trackback / Pingback
* Captcha implementation



Contribution
------------

Any help is appreciated. Please feel free to drop me a line, open issues or send pull request.