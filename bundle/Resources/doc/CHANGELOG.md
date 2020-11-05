Netgen Enhanced Selection Bundle changelog
==========================================

3.4.0 (05.11.2020)
------------------

* Implement expanded checkbox value in field type and expose controls in eZ Platform Admin UI

3.3.1 (16.08.2019)
------------------

* Fix showing iteration delimiter when condition for options is applied in field template

3.3.0 (22.07.2019)
------------------

* Added Symfony version of database migration command from legacy extension (thanks @SerheyDolgushev)
* Removed eZ kernel 8 support, it doesn't work in v3.x of the bundle

3.2.1 (06.07.2019)
------------------

* Allow eZ kernel 8

3.2.0 (07.06.2019)
------------------

* Add parameter to render the form via an expandend Symfony choice type

3.1.0 (22.12.2017)
------------------

* Add support for eZ Platform Admin UI v2
* Usage of Twig classes switched to namespaces
* Separated Twig extension definition from its runtime

3.0.1 (21.11.2017)
------------------

* Allow installing on eZ kernel 7.0

3.0.0 (27.10.2017)
------------------

* Replaced the field type legacy storage gateway with Doctrine storage gateway
* Removed usage of deprecated gateway based storage API
* Removed usage of deprecated base field criterion visitor from eZ Solr bundle
* Use Twig paths to reference Twig templates
* Removed support for eZ Publish 5 and eZ Platform <= 1.10
* Minimum supported eZ Solr Search Engine version is now 1.4
* Changed bundle structure to use PSR-4
* Renamed all Symfony services to have `netgen.enhanced_selection` prefix
* Removed all Symfony DI `*.class` parameters
* Removed support for PHP 5.5 and PHP 7.0
* Marked all services as public/private as needed
* Improvements to tests

2.3.5 (21.04.2017)
------------------

* Compatibility with Symfony 3.x

2.3.4 (21.04.2017)
------------------

* Priority of the field template set to 0 to ease overriding
* Marked the bundle as `ezplatform-bundle`
* Migrated to PHP CS Fixer 2.0
* Unit tests improvements

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
