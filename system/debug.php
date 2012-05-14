<?php

/**
 * Debug
 *
 * Easy debugging
 */
class Debug
{

    /**
     * Dump
     *
     * print the variable with print_r
     *
     * @param  anything $var
     * @return void
     */
    public static function dump($var)
    {
        print_r($var);
    }

    /**
     * Pretty
     *
     * print the variable with print_r wrapped in <pre> tags
     *
     * @param  anything $var
     * @return void
     */
    public static function pretty($var)
    {
        echo '<pre style="color:#333;background:#eee;padding:10px;">';
        self::dump($var);
        echo '</pre>';
    }

    /**
     * Dump
     *
     * print the variable with print_r then exit
     * defaults to pretty mode
     *
     * @param  anything $var
     * @param  bool     $pretty
     * @return void
     */
    public static function quit($var, $pretty=true)
    {
        $pretty ? self::pretty($var) : self::dump($var);
        exit;
    }

}