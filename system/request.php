<?php

/**
 * Request
 *
 * handles requests to the server
 */
class Request
{

    /**
     * Full
     *
     * returns the complete request URL
     *
     * @return string
     */
    public static function full()
    {
        return "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    }

    /**
     * Method
     *
     * returns the request method
     *
     * @return string
     */
    public static function method()
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Query
     *
     * returns the request query string
     *
     * @return string
     */
    public static function query()
    {
        return $_SERVER['QUERY_STRING'];
    }

    /**
     * Route
     *
     * returns the complete request route
     * in the form "METHOD /to/location"
     *
     * @return string
     */
    public static function route()
    {
        $method = self::method();
        $location = self::location();
        if ($location === '') {
            $location = '/';
        }
        return "$method $location";
    }

    /**
     * Location
     *
     * returns the reuqested location
     *
     * @return string
     */
    public static function location()
    {
        $params = str_replace(URL::base(), '', self::full());
        $parts = explode('?', $params);
        return $parts[0];
    }

}