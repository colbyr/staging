<?php

/**
 * Input
 *
 * library for easier user input retrieval
 */
class Input
{

    /**
     * All
     *
     * get an array of data post/getted to the server
     * optionally specify a type 'post' or 'get'
     *
     * @param  string $type
     * @return array
     */
    public static function all($type='')
    {
        switch ($type) {
            case 'get':
                return $_GET;
                break;

            case 'post':
                return $_POST;
                break;
            
            default:
                return array_merge($_POST, $_GET);
                break;
        }
    }

    /**
     * Has?
     *
     * returns true if key is set
     *
     * @param  string $key
     * @return bool
     */
    public static function has($key)
    {
        return array_key_exists($key, self::all());
    }

    /**
     * Get
     *
     * get value by key from all data
     * by default, returns null if not found
     *
     * @param  string $key
     * @param  mixed  $default
     * @return mixed
     */
    public function get($key, $default=null)
    {
        $params = self::all();
        return array_key_exists($key, $params) ? $params[$key] : $default;
    }

}