.. ==================================================
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
	via the extension manager

#. Create the following pages
	:ref:`Quick Installation <quick_installation>`
	Optional: add an RSS page (standard page) which will contain the RSS plugin

	.. figure:: ../Images/Installation/pagestructure.png
		:width: 286px
		:alt: typical page structure

		recommended page structure

	#. Include static template
		See :ref:`Quick Installation <quick_installation>`

#. Add plugins to the pages
	See :ref:`Quick Installation <quick_installation>`
	Optional: add the RSS plugin to your RSS page

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
						metadata {
							enable = 1

							twitterCards {
								enable = 0
							}
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

	For more configuration-options and the associated explanations see :code:`/Configuration/TypoScript/setup.txt`.


.. important::

	Assigning the TypoScript settings to backend context by using :code:`module.tx_t3extblog < plugin.tx_t3extblog` is essential
	when overriding values. Don't forget it!

.. important::

	Make sure to configure all mandatory settings, see the :ref:`minimal configuration <configuration-minimal>`!

.. tip::

	It is recommended to use the constants in conjunction with custom TypoScript. Copy both configuration files
	(constants.txt and setup.txt located in `typo3conf/ext/t3extblog/Configuration/TypoScript`) and include
	(using :code:`<INCLUDE_TYPOSCRIPT: source="FILE:EXT:my-extension/.../setup.txt">` them in your root template.


Next Steps
----------

#. Setup the RSS page TypoScript: :ref:`RSS setup <configuration>`

#. Configure RealURL: :ref:`RealUrl configuration <configuration-realurl>`

#. Copy all templates, configure the paths in TS and start adjusting the HTML markup to your needs

#. Start blogging:  :ref:`Users manual <users-manual>`.