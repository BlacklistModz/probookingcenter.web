<?php

// echo substr(md5(trim(123456)),0,20);die;

require 'config.php';

// Also spl_autoload_register (Take a look at it if you like)
function __autoload($class) {
    require LIBS . $class .".php";
}
// Load the Bootstrap!
$bootstrap = new Bootstrap();

$bootstrap->init();