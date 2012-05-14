<?php

class Session
{

    /**
     * Current
     *
     * cache the current user for future retrievals
     *
     * @var User
     */
    public static $current = null;

    /**
     * User
     *
     * Retrieve the current user from the session
     *
     * @return User
     */
    public static function user()
    {
        if (is_null(self::$current))
        {
            $uid = (isset($_COOKIE[SITE_COOKIE_NAME])) ? $_COOKIE[SITE_COOKIE_NAME] : "";
            $user = new User();
            $current = $user->find($uid);
            self::$current = $current;
        }
        return self::$current;
    }

    /**
     * Set
     *
     * Set the user manually
     *
     * @param  string $uid
     * @return void
     */
    public static function set($uid)
    {
        $user = new User();
        $current = $user->find($uid);
        self::$current = $current;
    }

}