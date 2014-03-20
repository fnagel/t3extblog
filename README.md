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
Data migration not needed as old SQL and TCA is used.

This extension could (but should probably not) be used for starting a new blog with TYPO3.

This extension will support migration to TYPO3 6.x. as soon as 6.2 LTS is released.


Features
------------

Currently implemented features:

* Show list, latest and detail view of posts
* Archive and category view
* Add new comments
* Wordpress like subscription manager
* Configurable spam check for comments and subscription requests
* Spam check: "I am human" checkbox, honeypot, cookie, useragent, and EXT:sfpantispam support
* Auto close comments functionality
* Posts RSS feed
* BE Module for posts and comments
* Default HTML markup matches Twitter Boostrap 2.3


Installation
------------

* (Deinstall EXT:t3blog)
* Install EXT:t3extblog
* Add static template
* Set pid's in TypoScript


Todo
------------

A lot of testing needs to be done. Help wanted!


IN WORK
* Make use of the Tce hook for comments
* rework notifications to be scheduler based
* Test comment and subscription process again
* * if subscription emails are sent
* * if subscription process works
* * if un-subscribe process works

BUGS
* delete button deletes but without prompt in post view

NEXT
* Partial for flashMessage VH
* More unit tests and functional tests
* Documentation
* better flexform configuration for plugins

FEATURES
* Trackback / Pingback
* Captcha implementation
* Use EXT:vidi for BE modules (needs TYPO3 6.1)



Contribution
------------

Any help is appreciated. Please feel free to drop me a line, open issues or send pull requests.