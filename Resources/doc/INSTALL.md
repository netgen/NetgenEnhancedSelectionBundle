Netgen Enhanced Selection Bundle installation instructions
==========================================================

Requirements
------------

* Recent version of eZ Publish 5

Installation steps
------------------

### Use Composer

Add the following to your composer.json and run `php composer.phar update netgen/enhanced-selection-bundle` to refresh dependencies:

```json
"require": {
    "netgen/enhanced-selection-bundle": "~1.0",
    "netgen/enhancedselection2": "*"
}
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
