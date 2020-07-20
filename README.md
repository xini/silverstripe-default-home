# Silverstripe Default Home

[![Version](http://img.shields.io/packagist/v/innoweb/silverstripe-default-home.svg?style=flat-square)](https://packagist.org/packages/innoweb/silverstripe-default-home)
[![License](http://img.shields.io/packagist/l/innoweb/silverstripe-default-home.svg?style=flat-square)](license.md)

## Overview

This modules makes sure that the homepage for a site is always accessible through the URL segment 
configured in `SilverStripe\CMS\Controllers\RootURLController::default_homepage_link` (usually 'home').
The module also makes sure that the homepage exists and can't be unpublished or deleted.
It also hides the homepage from menus (`ShowInMenus=false`).

This module supports single site as well as [multisites](https://github.com/symbiote/silverstripe-multisites) setups.

## Requirements

* SilverStripe CMS 4.x

## Installation

Install the module using composer:
```
composer require innoweb/silverstripe-default-home dev-master
```

Then run dev/build.

## Configuration

For this module to work you need to create a homepage class in your project and this module's HomePageExtension to it:

```
<?php

namespace Your\NameSpace;

use Innoweb\DefaultHome\Extensions\HomePageExtension;
use Page;

class HomePage extends Page
{
    private static $table_name = 'HomePage';

    private static $singular_name = "Home Page";
    private static $plural_name = "Home Pages";
    private static $description = 'Site home page';

    private static $hide_ancestor = HomePage::class;

    private static $extensions = [
        HomePageExtension::class
    ];

    ...
	
}
```

Then, the following configuration value needs to be set:

```
SilverStripe\CMS\Controllers\RootURLController:
  default_homepage_class: Your\NameSpace\Homepage
```

## License

BSD 3-Clause License, see [License](license.md)
