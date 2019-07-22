Netgen Enhanced Selection Bundle installation instructions
==========================================================

Requirements
------------

* eZ Platform 1.11+

Installation steps
------------------

### Use Composer

Run the following command from your project root to install the bundle:

```bash
$ composer require netgen/enhanced-selection-bundle
```

### Activate the bundle

Activate the bundle in `app/AppKernel.php` file.

```php
public function registerBundles()
{
   $bundles = array(
       new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
       ...
       new Netgen\Bundle\EnhancedSelectionBundle\NetgenEnhancedSelectionBundle()
   );

   ...
}
```

### Import SQL tables to your database

Import the following database table to your MySQL database:

```sql
CREATE TABLE `sckenhancedselection` (
  `contentobject_attribute_id` int(11) NOT NULL default '0',
  `contentobject_attribute_version` int(11) NOT NULL default '0',
  `identifier` varchar(255) NOT NULL default '',
  KEY `sckenhancedselection_coaid_coav` ( `contentobject_attribute_id`, `contentobject_attribute_version` ),
  KEY `sckenhancedselection_coaid_coav_iden` ( `contentobject_attribute_id`, `contentobject_attribute_version`, `identifier` )
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

### Clear the caches

Clear eZ Publish caches.

```bash
php bin/console cache:clear
```

### Use the bundle

You can now load and create content with `sckenhancedselection` field type.

## Using the content field within your own custom template

Add the following template code into your template:

```twig
{% if not ez_is_field_empty( content, "selection" ) %}
      {{ ez_render_field( content, "selection" ) }}
{% else %}
       Empty selection
{% endif %}
```

There is also Twig function available for fetching selection name based on selection identifier:

```twig
{% if not ez_is_field_empty( content, "selection" ) %}
    Selection name: {{ netgen_enhanced_selection_name( content, "selection", "selection_identifier") }}
{% else %}
       Empty selection
{% endif %}
```

Or you can fetch all selected selection names (in case of multiple selection):

```twig
{% if not ez_is_field_empty( content, "selection" ) %}
    {% set selection_data = netgen_enhanced_selection_name( content, "bio" ) %}
    {% for identifier, name in selection_data %}
        Selection identifier : {{ identifier }}
        Selection name : {{ name }}
    {% endfor %}
{% else %}
       Empty selection
{% endif %}
```

Replace the text: "selection" with your own content type field identifier and "selection_identifier" with some of your own selection identifiers as needed.

Save these additions to your custom template and clear caches as required.


Custom usage
------------

### Custom content field template override

If you need to customize the `sckenhancedselection_content_field.html.twig` template provided by this bundle you must override it within your own custom bundle.

There is general documentation on how to [import template override settings from a bundle](https://doc.ez.no/display/EZP/Import+settings+from+a+bundle)

#### Example of content field template override

An example of changes required to override the content field template is available within the [BCPageLayoutOverrideTestBundle](https://github.com/brookinsconsulting/BCPageLayoutOverrideTestBundle/commit/e48f57387a3b88c5869300d64e9ff3702eb37a67) which provides an overriden `sckenhancedselection_content_field.html.twig` template.

#### Step by step instructions to override the content field template

* Create `Resources/config/ezpublish.yml` file within your own custom bundle and populate it with the following example to override the template, changing the bundle name as required:

```yaml
system:
    default:
        field_templates:
            - {template: "BCPageLayoutOverrideTestBundle::sckenhancedselection_content_field.html.twig", priority: 30}
```

* It is very important that value of `priority` configuration in the example above is larger than the default priority defined in this bundle, which is 10.

* Within your custom bundle create a `DependencyInjection/MyBundleExtension.php` class implementing the `PrependExtensionInterface` interface. The [import template override settings from a bundle documentation](https://doc.ez.no/display/EZP/Import+settings+from+a+bundle#Importsettingsfromabundle-Theimplicitway) provides an example. `prepend` method should load your `Resources/config/ezpublish.yml` config file.

* Create a copy of the default `sckenhancedselection_content_field.html.twig` template in your own custom bundle's `Resources/views` directory and customize the template to meet your own unique needs.

* Clear caches

* NOTE: Instead of autoloading the configuration by implementing `PrependExtensionInterface` as noted above, you could also place the configuration in `ezpublish/config/ezpublish.yml` or import it from your bundle within `ezpublish/config/config.yml` file.
