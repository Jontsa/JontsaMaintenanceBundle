# Symfony maintenance mode bundle

A small bundle for Symfony 4/5 which provides commands to put your application in maintenance break during which
all requests receive HTTP 503 response. This is done by throwing `ServiceUnavailableHttpException` and clients
will receive either default Symfony error page or JSON message with correct HTTP status code.

![Tests](https://github.com/Jontsa/JontsaMaintenanceBundle/workflows/Tests/badge.svg)

## Features

- Put your site to maintenance mode with single command
- Responds with HTTP 503 to requests during maintenance
- Optional IP-address whitelist. For example to allow access for load balancer health checks during maintenance.
- Lightweight bundle

## Requirements

- Symfony 4 or 5
- PHP 7.1+
- composer

## Installation

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Open a command console, enter your project directory and execute:

```console
$ composer require jontsa/maintenance-bundle
```

### Applications not using Symfony Flex

When not using Symfony Flex, you need to enable the bundle by adding it
to the list of registered bundles in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    Jontsa\Bundle\MaintenanceBundle\JontsaMaintenanceBundle::class => ['all' => true],
];
```

## Usage

To put your site under maintenance mode

```console
$ bin/console jontsa:maintenance enable
```

To disable maintenance mode

```console
$ bin/console jontsa:maintenance disable
```

## Configuration

To change default settings, create a configuration file.

```yaml
# config/packages/jontsa_maintenance.yaml
jontsa_maintenance:
  whitelist:
    ip: [127.0.0.1, 192.168.0.0/24]
  lock_path: '%kernel.project_dir%/var/cache/maintenance'
```

- `ip` is an array of IP-addresses or networks which are allowed to access applications even during maintenance
- `lock_path` is the file path which is created during maintenance

### Custom error page

If you want to customize the error page, check out [Symfony documentation](https://symfony.com/doc/current/controller/error_pages.html).
