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
