Netgen Enhanced Selection Bundle changelog
==========================================

2.3.3 (01.02.2017)
------------------

* Fix broken installation on eZ Publish 5.4.x (thanks @ernestob)

2.3.2 (17.01.2017)
------------------

* Add dummy `sckenhancedselection` field definition settings template in order not to crash Platform UI

2.3.1 (16.12.2016)
------------------

* Add a conflict with eZ Platform Solr Search Bundle 1.2.0 due to breaking change in API (thanks @MarioBlazek)

2.3 (07.12.2016)
----------------

* Add Solr visitor for `EnhancedSelection` criterion (thanks @MarioBlazek)

2.2 (06.09.2016)
----------------

* Add `Indexable` implementation for Solr support (thanks @jxn)
* Full test coverage (thanks @MarioBlazek)
* Added Twig function for fetching name of selection identifier (thanks @MarioBlazek)
* Bug fixes

2.1 (17.12.2015)
----------------

* Implemented enhanced selection field type handler for NetgenEzFormsBundle (thanks @MarioBlazek)
* Switch coding standards to PSR-2

2.0 (29.06.2015)
----------------

* Migrate `sckenhancedselection` field type to database storage

1.1.2 (24.05.2015)
------------------

* Specifically require `enhancedselection2` 1.x versions

1.1.1 (07.05.2015)
------------------

* Mark the field type as unindexable (for now) in order not to crash elasticsearch/solr indexing scripts

1.1 (07.05.2015)
----------------

* Load field type view template automatically by implementing `PrependExtensionInterface` in DI extension
* Implemented loading field type settings (thanks Brookings Consulting)
* Implemented creating field type with the ability to specify field type settings (thanks Brookings Consulting)
* Implemented field type settings validation
* Fixed block name in field type view template (thanks Brookings Consulting)
* Various documentation fixes (thanks Brookings Consulting)

1.0 (19.09.2014)
----------------

* Initial release
