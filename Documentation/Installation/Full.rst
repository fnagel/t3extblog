.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. --- coding: utf-8 --- with BOM.

.. include:: ../Includes.txt

.. _installation:

Installation
============

Target group: --Administrators--

.. contents:: Within this page
   :local:
   :depth: 3


Installation process - step by step
-----------------------------------

.. important::

	This guide will use the "old" way of configuring an extension: including a static TypoScript template

In this installation guide, we will override some values and make our blog more individual.
Some steps are already described in the :ref:`Quick Installation <quick_installation>`.


#. Import and install the extension

   - via the extension manager
   - or using composer

#. Create the following pages

   - See :ref:`Quick Installation <quick_installation>`
   - Optional: add an RSS page (standard page) which will contain the RSS plugin
   - Optional: add a page (standard page) for subscribing to new blog posts

   .. figure:: ../Images/Installation/pagestructure.png
      :alt: Recommended page structure
      :class: with-shadow

#. Include static TypoScript template

   This can be done on your root-page or in an extension template for a specific page.

   Minimum requirement: `T3Extblog: Default Setup (needed)`.
   Do NOT include `T3Extblog: RSS setup`! We will need this elsewhere.

   .. figure:: ../Images/Installation/includestatic.png
      :alt: Include static
      :class: with-shadow

#. Add plugins to the pages

   - See :ref:`Quick Installation <quick_installation>`
   - Optional: add the RSS plugin to your RSS page
   - Optional: add the blog subscription form plugin to the previously created page

#. Configure settings in the constant editor module:

   .. figure:: ../Images/Installation/constant_editor.png
      :alt: Constant editor backend module
      :class: with-shadow

   Or use TypoScript:

#. Start to configure your Blog. This is an TypoScript example:

   .. code-block:: typoscript
      :linenos:

         plugin.tx_t3extblog {
            settings {
               blogsystem {
                  posts {
                     paginate {
                        insertAbove = 1
                        maximumNumberOfLinks = 1
                     }
                  }
                  comments {
                     allowedUntil = +5 years
                     approvedByDefault = 1
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
	This is overall more flexible than using the site sets settings approach.


Next Steps
----------

#. Setup the RSS page TypoScript: :ref:`RSS setup <administration-rss>`

#. Configure speaking URLs (optional): :ref:`Speaking URL configuration <configuration-speaking-url>`

#. Copy all templates, configure the paths in TS and start adjusting the HTML markup to your needs

#. Start blogging:  :ref:`Users manual <users-manual>`.
