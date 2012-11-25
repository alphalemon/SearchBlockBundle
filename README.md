Add the Elastic Search App-Block to AlphaLemon CMS
==================================================

The Elastic Search App-Block adds a form to your website with a search field, to let 
the website users search through it.

.. note::

    The App-Block requires **elasticsearch** server installed.

Installation
------------

You can add the **Elastic Search App-Block** to the application managed by AlphaLemon 
CMS, adding it to the **composer.json** of your Symfony2 application:

.. code-block:: text

    {
        "name": "alphalemon/alphalemon-cms-sandbox",

        [...]

        "require": {

            [...]        

            "alphalemon/app-elastic-search-bundle": "dev-master",        
        }
    }

Be sure that there is declared the reference to **http://apps.alphalemon.com** repository,
under the **repositories** option:

.. code-block:: text

    "repositories": [

        [...]

        {
            "type": "composer",
            "url": "http://apps.alphalemon.com/"
        }
    ],

then run the composer's update command:

.. code-block:: text

    php composer.phar update alphalemon/app-elastic-search-bundle


Configuration
-------------
This App-Block cannot be added using the usual contextual menu and it must be injected 
at runtime. You should read the `how to change a content at runtime`_ tutorial to have 
more information about that.

To correctly display the block you have to implement two listeners, one to display the 
search form and another to display the results on a page. Adding these listeners is quite
easy because the Search Bundle provides two base classes that did that job.

.. note::

    A listener is a php object that waits for a specific event is dispatched and does 
    something in reaction of that.

    Listener should live under the **Listener** folder of your deploy bundle, so add that 
    folder if it does not exist.


The Search results page
-----------------------
The Search results listener must display the results of the search on a page of your website,
so, as first thing you must add a page with AlphaLemon CMS. Let's assume that your page name
is **search**.

.. note::

    The  name of this page is important and it will be used later in this tutorial


The Search results listener
~~~~~~~~~~~~~~~~~~~~~~~~~~~
Create a **AlSearchResultsRenderingListener.php** under the **Listener** folder and add the following code:

.. code-block:: php

    namespace Acme\WebSiteBundle\Listener;

    use AlphaLemon\Block\SearchBlockBundle\Core\Listener\AlSearchResultsRenderingListener as BaseSearchResultsRenderingListener;

    class AlSearchResultsRenderingListener extends BaseSearchResultsRenderingListener
    {
        protected function configure()
        {
            return array(
                'slot' => 'main_contents',
            );
        }
    }

The listener extends the abstract Search Bundle object **AlSearchResultsRenderingListener**
which requires to implements a protected method called configure. 

This method must return an array that defines a single option named **slot** which
value is the slot name where the search form will be displayed. 

In this example the search form will be displayed on the **main_contents** slot.

Enable the listener
~~~~~~~~~~~~~~~~~~~

To enable the listener you must configure it in the Dependency Injector Container, so open
the **Resources/config/service.xml** of your deploy bundle and add the following code:

.. code-block:: xml

     <parameters>

        [...]

        <parameter key="acme_web_site.search_listener.class">Acme\WebSiteBundle\Listener\AlSearchResultsRenderingListener</parameter>
    </parameters>

    <services>

        [...]

        <service id="acme_web_site.search_listener" class="%acme_web_site.search_listener.class%">
            <tag name="alpha_lemon_theme_engine.event_listener" event="page_renderer.before_search_rendering" method="onPageRendering" priority="0" />
            <argument type="service" id="service_container" />
        </service>
    </services>


.. note::

    The **acme_web_site.search_listener.class** must reflect the full namespace of your listener


The Search Form listener
~~~~~~~~~~~~~~~~~~~~~~~~
The Search Form listener must display the search form on the page, so create a **AlSearchFormRenderingListener.php** 
under the **Listener** folder and add the following code:

.. code-block:: php

    namespace Acme\WebSiteBundle\Listener;

    use AlphaLemon\Block\SearchBlockBundle\Core\Listener\AlSearchFormRenderingListener as BaseSearchFormRenderingListener;

    class AlSearchFormRenderingListener extends BaseSearchFormRenderingListener
    {
        protected function configure()
        {
            return array(
                'slot' => 'search_box',
                'page' => 'search',
            );
        }
    }

The listener extends the abstract Search Bundle object **AlSearchFormRenderingListener**
which requires to implements a protected method called configure. 

This method must return an array that defines a two options named **slot** and **page**.

The slot option is the slot name where the search form will be displayed, in this example 
it will be displayed on the **search_box** slot and the **page** option is the name of 
the search results page added at the beginning of this tutorial.

.. note::

    The page must be the AlphaLemon CMS Page Name option, not the permalink name


Enable the listener
~~~~~~~~~~~~~~~~~~~

To enable the listener you must configure it in the Dependency Injector Container, so open
the **Resources/config/service.xml** of your deploy bundle and add the following code:

.. code-block:: xml

     <parameters>

        [...]

        <parameter key="acme_web_site.page_listener.class">Acme\WebSiteBundle\Listener\AlSearchFormRenderingListener</parameter>
    </parameters>

    <services>

        [...]

        <service id="acme_web_site.page_listener" class="%acme_web_site.page_listener.class%">
            <tag name="alpha_lemon_theme_engine.event_listener" event="page_renderer.before_page_rendering" method="onPageRendering" priority="0" />
            <argument type="service" id="service_container" />
        </service>
    </services>

This configuration will display the Search form `each page of the website`_.


Configure elasticsearch
-----------------------
The Search Bundle configures the elasticsearch server as follows:

.. code-block:: text

    foq_elastica:
        clients:
            default: { host: "localhost", port: 9200 }
        indexes:
            website:
                client: default
                types:
                    search:
                        mappings:
                            url: { analyzer: snowball }
                            content: { analyzer: snowball }
                        persistence:
                            driver: propel
                            model: 
                            provider: 
                                service: alphalemon.search_provider.search

To have the elasticsearch server working for your website, that configuration must be
changed.

Open the **config.yml** file of your application and copy that configuration, then
change it to fit your needs.

For example, to indicize your website at the **example.com** host change that configuration
as follows:

.. code-block:: text

    foq_elastica:
        clients:
            default: { host: "example.com", port: 9200 }
        indexes:
            website:
                client: default
                types:
                    search:
                        mappings:
                            url: { analyzer: snowball }
                            content: { analyzer: snowball }
                        persistence:
                            driver: propel
                            model: 
                            provider: 
                                service: alphalemon.search_provider.search


Indicize your website
---------------------
To indicize the website, simpy run this command for your console:

.. code-block:: text

    app/console foq:elastica:populate

.. _`how to change a content at runtime`: http://www.alphalemon.com/how-to-change-a-content-at-runtime
.. _`each page of the website`: http://www.alphalemon.com/how-to-change-a-content-at-runtime#the-rendering-process