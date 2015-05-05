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

If you need to customize the `sckenhancedselection_content_field.html.twig` template provided by this bundle you must override it within your own custom bundle.

There is general documentation on how to [import template override settings from a bundle](https://doc.ez.no/display/EZP/Import+settings+from+a+bundle)

#### Example of the content_field template override bundle changes required

An example of the custom bundle changes required is available within the [BCPageLayoutOverrideTestBundle](https://github.com/brookinsconsulting/BCPageLayoutOverrideTestBundle/commit/e48f57387a3b88c5869300d64e9ff3702eb37a67) which provides a `sckenhancedselection_content_field.html.twig` template override which you can use to see what is needed to be added to your own custom bundle.

#### General requirements of a content_field template override bundle

* Within your custom bundle create a DependencyInjection/YourCustomTemplateOverrideExtension.php class implementing the PrependExtensionInterface class. Remember to load the `Resources/config/ezpublish.yml` config file from in your own bundle. The [import template override settings from a bundle documentation](https://doc.ez.no/display/EZP/Import+settings+from+a+bundle#Importsettingsfromabundle-Theimplicitway) provides an example.

* Create a config file override in `Resources/config/ezpublish.yml` (within your own custom bundle) and populate it with the settings required in the field_templates settings block to override the template.

Here is an example of the bundle's template override config settings required:

```yaml
system:
    default:
        field_templates:
            - {template: "BCPageLayoutOverrideTestBundle::sckenhancedselection_content_field.html.twig", priority: 30}
```

* It is very important that within the `Resources/config/ezpublish.yml` template override settings you set the 'priority' variable value larger than within the NetgenEnhancedSelectionBundle default 'priority' value used, which is 10. Your 'priority' variable value should be larger than 10 like for example 20.

* Note: Instead of autoloading the config settings (by using PrependExtensionInterface class), you could also create the config settings within `ezpublish/config/ezpublish.yml` if you prefer or require global settings overrides or you can import the settings from your bundle within `ezpublish/config/config.yml` instead of autoloading the settings.

* Then create a copy of the default `sckenhancedselection_content_field.html.twig` template into your own custom bundle's `Resources/views` directory and customize the template to meet your own unique needs.

* Clear caches

