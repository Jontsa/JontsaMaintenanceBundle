# Using with deployer

If you are using [Deployer](https://deployer.org) in your application, this package
provides a simple recipe to enable or disable maintenance break on your target host.

## Usage

Include the recipe in your deploy.php file

```php
require 'vendor/jontsa/maintenance-bundle/recipe/maintenance.php';
```

This adds two new tasks `maintenance:enable` and `maintenance:disable`. These tasks
execute `bin/console jontsa:maintenance` command on the target host.