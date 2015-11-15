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

In this installation guide, we will override some values and make our Blog more individual.
Some steps are allready described in the :ref:`Quick Installation <quick_installation>`.


#. Import and install the extension
	via the extension manager

#. Create the following pages: :ref:`Quick Installation <quick_installation>`
	Additional add an "rss-page" (standard page, optional): on this page we place the rss-feed

	.. figure:: ../Images/Installation/pagestructure.png
		:width: 219px
		:alt: typical page structure

		recommended page structure

#. Include static template: see :ref:`Quick Installation <quick_installation>`

#. add plugins to the pages: see :ref:`Quick Installation <quick_installation>`
	Additional add the RSS-Plugin to your RSS-page

#. Start to configure your Blog. This is an typoscript-example:

::

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

                		paginate {
                    		itemsPerPage = 50
                    		insertAbove = 0
                    		insertBelow = 1
                    		maximumNumberOfLinks = 10
                		}
            		}
        		}
    		}
		}
	module.tx_t3extblog < plugin.tx_t3extblog

For more configuration-options and the associated explanations see :code:`/Configuration/TypoScript/setup.txt`.


If you do NOT made the basic setting via the contant editor, you need to add the following settings in your typoscript:

::

	plugin.tx_t3extblog.settings.blogsystem.pid = 456 (456 is the page id where the plugin "blogsystem" has been added)
	plugin.tx_t3extblog.settings.subscriptionManager.pid = 789 (789 is the page id where the plugin "Subscription Manager" has been added).
	plugin.tx_t3extblog.settings.subscriptionManager.admin.mailTo.email = mailadress@of-the-admin.tld
	plugin.tx_t3extblog.persistence.storagePid = 123 (123 is the page id where you will store your blogposts, we recommend to use a storage folder)


.. important::

	The setting "module.tx_t3extblog < plugin.tx_t3extblog" is essential when overriding values. Don´t forget it!


Usage
-----

#. You want to configure your RSS-Feed and want to have some nice URLs (RealURL)? See :ref:`Administration manual <admin-manual>`

#. You want to start blogging? See :ref:`Users manual <users-manual>`.