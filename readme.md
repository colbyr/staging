# Staging
barebones MVC for PHP 5.2... it's so simple there aren't even models

## Motivation
I love [Laravel](http://laravel.com). I was also stuck working in PHP 5.2. So to make my life easier (and to have some fun) I came up with Staging. I borrow heavily from Laravel's syntax and naming conventions. That being said, if you're developing in PHP 5.3+ I wouldn't bother with Staging... jump straight into [Laravel](http://laravel.com).

## Configuration

1. apache

Staging uses mod_rewrite to make pretty URLs. Make sure mod_rewrite is install, enabled in Apache and that overides are allowed in the app's directory.

2. PHP

Staging was written for PHP 5.2. If you're running 5.3+, I recommend using [Laravel](http://laravel.com). Trust me... it's awesome!

3. set the base application URL in `config/application.php`

```php
<?php

return array(

    /**
     * URL
     *
     * be sure to set the base URL of your application (no trailing slash)
     */
    'url' => 'http://localhost'

);
```

## Dig In

* `config/routes.php` - Map urls to behaviors (i.e. "controllers")
* `controllers.php` - Define behavior to associate with routes
* `libraries/` - Custom classes added to the libraries directory are automatically autoloaded
* `system/` - this folder contains the classes that make up Staging
* `views/` - staging uses the [smarty templating engine](http://www.smarty.net/)

## A Basic App

### controllers.php

```php
<?php

class Controllers
{

    public static function before() {}

    public static function after() {}

    /**
     * Index
     *
     * controller actions are prepended with "action_"
     *
     * @return void
     */
    public static function action_index()
    {
        return View::make('index')
                    ->assign('title', 'Staging')
                    ->render();
    }

}
```

### config/routes.php

```php
<?php

/**
 * Routes
 */
return array(
    'GET /' => 'index',
    'GET /home' => 'index'
);
```

### views/index.tpl

```html
<h1>{$title}</h1>
```