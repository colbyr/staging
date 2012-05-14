<?php

class Redirect
{

    public static function to($location='')
    {
        return header('Location: ' . URL::to($location));
    }

}