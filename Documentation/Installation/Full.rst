﻿.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt

.. _installation:


Installation
============

Target group: **Administrators**


Installation process - step by step
-----------------------------------

In this installation guide, we will override some values and make our blog more individual.
Some steps are already described in the :ref:`Quick Installation <quick_installation>`.


#. Import and install the extension
	- via the extension manager

#. Create the following pages
	- See :ref:`Quick Installation <quick_installation>`
	- Optional: add an RSS page (standard page) which will contain the RSS plugin
	- Optional: add a page (standard page) for subscribing to new blog posts

	.. figure:: ../Images/Installation/pagestructure.png
		:alt: Recommended page structure

#. Include static template
	- See :ref:`Quick Installation <quick_installation>`

#. Add plugins to the pages
	- See :ref:`Quick Installation <quick_installation>`
	- Optional: add the RSS plugin to your RSS page
	- Optional: add the Blog Subscription Form plugin to the previously created page

#. Start to configure your Blog. This is an TypoScript example:

.. code-block:: typoscript
	:linenos:

		plugin.tx_t3extblog {
			settings {
				blogsystem {
					posts {
						paginate {
							itemsPerPage = 99
							insertAbove = 1
							insertBelow = 1
							maximumNumberOfLinks = 50
						}
					}
					comments {
						allowed = 1
						allowedUntil = +6 months
						approvedByDefault = 0

						subscribeForComments = 0
					}
				}
			}
		}
		module.tx_t3extblog < plugin.tx_t3extblog

See :ref:`Configuration <configuration>` for all possible settings.


.. important::

	Assigning the TypoScript settings to backend context by using :code:`module.tx_t3extblog < plugin.tx_t3extblog` is essential
	when overriding values. Don't forget it!

.. important::

	Make sure to configure all mandatory settings, see the :ref:`minimal configuration <configuration-minimal>`!

.. tip::

	It is recommended to use the constants in conjunction with custom TypoScript. Copy both configuration files
	(`constants.typoscript` and `setup.typoscript` located in `EXT:t3extblog/Configuration/TypoScript`) and include
	(using :code:`<INCLUDE_TYPOSCRIPT: source="FILE:EXT:my_extension/.../setup.typoscript">` them in your root template.


Next Steps
----------

#. Setup the RSS page TypoScript: :ref:`RSS setup <administration-rss>`

#. Configure speaking URLs (optional): :ref:`Speaking URL configuration <configuration-speaking-url>`

#. Copy all templates, configure the paths in TS and start adjusting the HTML markup to your needs

#. Start blogging:  :ref:`Users manual <users-manual>`.
