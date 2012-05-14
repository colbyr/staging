<?php

class Str
{

    /**
     * Limit
     *
     * conveniently shorten strings for previews and the like
     *
     * @param  string $string
     * @param  int    $limit
     * @param  string $end
     * @return string
     */
    public static function limit($string, $limit=100, $end='...')
    {
        if (strlen($string) <= $limit) return $string;
        return substr($string, 0, $limit) . $end;
    }
	
	/**
     * Limit
     *
     * detects if the given string exceeds the specified character limit
     *
     * @param  string $string
     * @param  int    $limit
     * @return boolean
     */
	public static function exceeds_limit($string, $limit=100)
    {
        return (strlen($string) >= $limit);
    }

}