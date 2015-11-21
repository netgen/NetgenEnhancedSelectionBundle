Netgen Enhanced Selection Bundle changelog
==========================================

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
