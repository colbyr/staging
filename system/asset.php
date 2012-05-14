<?php

/**
 * Asset
 *
 * Helpful class for handling assets
 */
class Asset
{

    /**
     * Url
     *
     * Generate URL to Asset of $type
     * adds a cache breaking checksum to the file
     *
     * @param  string $file
     * @param  string $suffix
     * @return string
     */
    public static function url($type, $file, $suffix='')
    {
        $assets = Config::get('application.asset_url', URL::to("public"));
        $path = "$assets/$type/$file" . (empty($suffix) ? '' : ".$suffix");
        if (file_exists($path)) {
            $fileHash = sha1($path . $file . $suffix);
            $path .= "?h=$fileHash";
        }
        return $path;
    }

    /**
     * CSS
     *
     * Generate URL to css Asset
     * automatically appends '.css' and adds a cache breaking checksum to the file
     *
     * @param  string $file
     * @param  string $suffix
     * @return string
     */
    public static function css($file, $suffix='css')
    {
		return self::url('css', $file, $suffix);
    }

    /**
     * Img
     *
     * Generate URL to image Asset
     *
     * @param  string $file
     * @param  string $suffix
     * @return string
     */
    public static function img($file, $suffix='')
    {
        return self::url('img', $file, $suffix);
    }

    /**
     * JS
     *
     * Generate URL to javascript Asset
     * automatically appends '.js' and adds a cache breaking checksum to the file
     *
     * @param  string $file
     * @param  string $suffix
     * @return string
     */
    public static function js($file, $suffix='js')
    {
		return self::url('js', $file, $suffix);
    }

}