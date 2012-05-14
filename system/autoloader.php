<?php

class Autoloader {

    /**
     * Default
     *
     * Default autoloaded directories
     *
     * @var array
     */
    public static $default = array('.', 'system', 'libraries');

    /**
     * Paths
     *
     * specified autoloaded directories
     *
     * @var array
     */
    public $paths;

    /**
     * Paths
     *
     * merges default and specified directories
     *
     * @return array
     */
    public function paths()
    {
        return array_merge($this->paths, self::$default);
    }

    /**
     * Constructor
     *
     * takes an array of paths to be autoloaded
     *
     * @param  array $paths
     * @return void
     */
    public function __construct($paths=array()) {
        $this->paths = $paths;
        spl_autoload_register(array($this, 'autoload'));
    }

    /**
     * Autoload
     *
     * the autoloader function
     *
     * @param  string $class
     * @return bool
     */
    public function autoload($class)
    {
        foreach ($this->paths() as $path) {
            $file = self::file($class, $path);
            if ($this->load($file))
            {
                return true;
            }
        }
    }

    /**
     * Load
     *
     * if the $file exists, load it
     *
     * @param  string $file
     * @return bool
     */
    public function load($file) {
        $exists = file_exists($file);
        if ($exists)
        {
            require_once $file;
        }
        return $exists;
    }

    /**
     * File
     *
     * constructs the full path from $class and $path
     *
     * @param  string $class
     * @param  string $path
     * @return string
     */
    public static function file($class, $path)
    {
        return getcwd() . '/' . $path . '/' . strtolower($class) . '.php';
    }

}