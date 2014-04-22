TYPO3 t3extblog
===============

This is a TYPO3 Extbase / Fluid extension which aims to replace t3blog.


Current state
------------
Not for production use - alpha! Broken commits ahead!
Needs testing!



Idea & goals
------------
I'm tired of hardly maintained extensions and hard to maintain code with unclean logic.
Tried to built a clean Extbase / Fluid alternative around the old codebase.
Data migration not needed as (slightly modified) t3blog SQL and TCA is used.

This extension could (but should probably not) be used for starting a new blog with TYPO3.

This extension will support migration to TYPO3 6.x.


Features
------------

Currently implemented features:

* Show list, latest and detail view of posts
* Archive and category view
* Add new comments
* Allow some HTML tags in comment message
* Wordpress like subscription manager
* Configurable spam check: "I am human" checkbox, honeypot, cookie, useragent, and EXT:sfpantispam support
* Opt-In mails for comment subscription with expiration date
* Auto close comments functionality
* RSS feed for posts
* BE Module for posts and comments
* Default HTML markup matches Twitter Boostrap 2.3
* Reasonable email sending, even when you accept comments in BE (extension module AND default list module)
* Preview of hidden posts (add tx_t3extblog.singlePid to your page TSconfig)


Installation
------------

* (Deinstall EXT:t3blog)
* Install EXT:t3extblog
* Update DB in EM
* Use update script in EM
* Add static TS template
* Create pages and add plugins
* Set pid's in TypoScript


Todo
------------

A lot of testing needs to be done. Help wanted!

* Add own caching to prevent blogsystem page (aka all post pages) from being generated all over again
* Or split list and show action into different plugins
* Add this is an old post functionality
* Partial for flashMessage VH to be more flexible
* More unit tests and functional tests
* Better flexform configuration for plugins
* Trackback / Pingback support
* Blogroll?
* Documentation
* Rework notifications to be based upon scheduler (cronjob)
* Captcha implementation
* Use EXT:vidi for BE modules (needs TYPO3 6.1)


Known bugs
* delete button deletes but without prompt in post view (BE module)



Contribution
------------

Any help is appreciated. Please feel free to drop me a line, open issues or send pull requests.


Donation
------------

Please consider a donation: http://www.felixnagel.com/donate/