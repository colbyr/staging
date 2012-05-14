<?php

require getcwd() . '/system/autoloader.php';

$autoloader = new Autoloader();

Router::execute();
