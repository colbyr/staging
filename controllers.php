<?php

/**
 * Controllers
 *
 * a collection of methods that can be executed by routes
 */
class Controllers
{

    /**
     * Before
     *
     * Run before every request
     *
     * @return void
     */
    public static function before() {}

    /**
     * Before
     *
     * Run following every request
     *
     * @return void
     */
    public static function after() {}

    /**
     * Index
     *
     * The default landing page
     *
     * @return void
     */
    public static function action_index()
    {
        return View::make('index')->render();
    }

}