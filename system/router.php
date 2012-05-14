<?php

/**
 * Router
 *
 * Handles controller calls
 */
class Router
{

    /**
     * Execute
     *
     * performs the actions associated with the current route
     */
    public static function execute()
    {
        $route = Request::route();
        $available = self::routes();
        if (array_key_exists($route, $available))
        {
            $method = 'action_' . $available[$route];
			Controllers::before();
			echo Controllers::$method();
			Controllers::after();
        }
        else
        {
            Debug::quit('404 - page not found');
        }
    }

    /**
     * Routes
     *
     * returns the route array
     */
    public static function routes()
    {
        return Config::get('routes');
    }

}