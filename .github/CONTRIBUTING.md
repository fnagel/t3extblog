# How to contribute

Any help is appreciated. Please feel free to open issues or send pull requests.

It is possible to sponsor features and bugfixes!


## Bug reports / questions

Always use the [search](https://github.com/fnagel/t3extblog/issues) before creating new tickets.

When submitting a ticket, the following information should be included:

* Include **TYPO3 CMS and extension version**
* Include a **step by step guide how to reproduce the issue**
* (optional) Include your TypoScript settings (incl. information on how and where it's added)


## Making Changes / Send PR's

* Make sure to follow the commit message style:
    * Example: `[TASK|BUGFIX|FIX|FEATURE|DOC|CLEANUP|RELEASE] What has been fixed`
    * 72 chars max line length
* Make sure to follow our CGL, see:
    * [.editorconfig](.editorconfig)
    * [.php_cs.dist (PSR-2)](.php_cs.dist)
    * [typoscript-lint.yml](typoscript-lint.yml)
* Run `composer run test`
* Make sure to test with all supported TYPO3 CMS versions (according to `composer.json`)
* Test your changes following the [testing guide](Documentation/DeveloperGuide/Index.rst)


## Additional Resources

* See [developer guide](Documentation/DeveloperGuide) in documentation
