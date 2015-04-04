Netgen Enhanced Selection Bundle installation instructions
==========================================================

Requirements
------------

* Recent version of eZ Publish 5

Installation steps
------------------

### Use Composer

Run the following command from your project root to install the bundle:

```bash
$ composer require netgen/enhanced-selection-bundle:~1.0
```

### Activate the bundle

Activate the bundle in `ezpublish/EzPublishKernel.php` file.

```php
use Netgen\Bundle\EnhancedSelectionBundle\NetgenEnhancedSelectionBundle;

...

public function registerBundles()
{
   $bundles = array(
       new FrameworkBundle(),
       ...
       new NetgenEnhancedSelectionBundle()
   );

   ...
}
```

### Clear the caches

Clear eZ Publish 5 caches.

```bash
php ezpublish/console cache:clear
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

Replace the text: "selection" with your own content type field identifier as needed.

Save these additions to your custom template and clear caches as required.

Custom usage
------------------

### Custom content_field template override

PrependExtensionInterface should be used instead

Then create a copy of the default `sckenhancedselection_content_field.html.twig` template into your activated custom, and customize as needed and then clear cache.

Edit your `ezpublish/config/config.yml` file, add and customize the following yaml configuration:

```yaml
parameters:
    ezsettings.YOUR_SITEACCESS_NAME.field_templates:
          -  {template: EzPublishCoreBundle::content_fields.html.twig, priority: 0}
          -  {template: YourCustomDesignTemplateBundle::sckenhancedselection_content_field.html.twig, priority: 0}
```

Then create a copy of the default `sckenhancedselection_content_field.html.twig` template into your activated custom bundle and customize as needed.

### Controling the selection options display order

* The default sorting of enhanced selection options requires unique numeric selection option priority values otherwise the order they are created is used

### Sort selection options by any other selection option field besides priority

The default display order of enhanced selection options is by priority. Other sort orders supported by default are: id, identifier and name.

You can sort your selection options display order by any other selection option field besides priority by creating a `sckenhancedselection_content_field.html.twig` template override (described above).

Then edit the `sckenhancedselection_content_field.html.twig` template in your custom bundle and changing the twig filter 'sort_by_selection_field' first parameter (sortByField), a string, 'priority' to any one of the following values supported by default.

The twig filter 'sort_by_selection_field' provided by this extension uses the php function 'usort' and a custom 'usort value_compare_func' to sort selection options.

The twig filter 'sort_by_selection_field' will not sort selection options by 'priority' if the FieldType options 'priority' field values contain duplicate values.

By default each time you create an enhanced selection option the default priority is set to 1. This is historically due to a bug in the legacy datatype where each time a new option is created the priority is set to 1 even if another option with priority of 1 already exists.

If you do not manually edit your enhanced selection options priority values to be unique then the default order, the order they were created is the order they will be displayed.
