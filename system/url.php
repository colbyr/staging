<?php

/**
 * URL
 *
 * Wrapper class for generating urls
 */
class URL
{

    /**
     * Base
     *
     * Get the application's base URL
     *
     * @return string
     */
    public static function base()
    {
        return Config::get('application.url');
    }

    /**
     * To
     *
     * appends $path to the base url
     *
     * @param  string $path
     * @return string
     */
    public static function to($path='')
    {
        return self::base() . '/' . $path;
    }

}