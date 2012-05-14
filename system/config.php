<?php

class Config {

    /**
     * Get
     *
     * Returns value of a given config key
     * sub-keys can be accessed "key.subkey.subsubkey"
     * where key is a filename in config/
     *
     * @param  string   $key
     * @param  mixed    $default
     * @return anything
     */
    public static function get($key='config', $default='') {
        $keys = explode('.', $key);
        $config = self::all($keys[0]);
        $res = $config;
        for ($i = 1; $i < count($keys); $i += 1)
        {
            if (array_key_exists($keys[$i], $res))
            {
                $res = $res[$keys[$i]];
            } else {
                return $default;
            }
        }
        return $res;
    }

    /**
     * All
     *
     * Returns the entire config array
     *
     * @return array
     */
    public static function all($name='config')
    {
        $config = array();

        $config = array_merge($config, require getcwd() . "/config/$name.php");

        return $config;
    }

}