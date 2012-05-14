# Staging
no-nonsesne MVC for PHP 5.2... it's so simple there aren't even models

## Warning:

If you have access to PHP 5.3 don't bother with Staging! Use *[Laravel](http://laravel.com)* instead!!!

## Dig In

* `config/routes.php` - Map urls to behaviors (i.e. "controllers")
* `controllers.php` - Define behavior to associate with routes
* `libraries/` - Custom classes added to the libraries directory are automatically autoloaded
* `system/` - this folder contains the classes that make up Staging
* `views/` - staging uses the [smarty templating engine](http://www.smarty.net/)

## A Basic App

### controllers.php

```
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

```
/**
 * Routes
 */
return array(
    'GET /' => 'index',
    'GET /home' => 'index'
);
```

### views/index.tpl

```
<h1>{$title}</h1>
```