<?php
// This is global bootstrap for autoloading
if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    require_once(dirname(__DIR__) . '/vendor/autoload.php');
}