Netgen Enhanced Selection Bundle installation instructions
==========================================================

Requirements
------------

* Ibexa Platform 4.0+

Installation steps
------------------

### Use Composer

Run the following command from your project root to install the bundle:

```bash
$ composer require netgen/enhanced-selection-bundle
```

### Activate the bundle

Activate the bundle in `config/bundles.php` file.

```php
<?php

return [
    ...,

    Netgen\Bundle\EnhancedSelectionBundle\NetgenEnhancedSelectionBundle::class => ['all' => true],

    ...
];
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

Clear Ibexa Platform caches.

```bash
php bin/console cache:clear
```

### Use the bundle

You can now load and create content with `sckenhancedselection` field type.
