<?php

/**
 * Autoload unincluded class files
 */
spl_autoload_register(function ($className) {
    $filename = __DIR__."/classes/$className.php";
    if (file_exists($filename))
        include_once("$filename");
});