<?php

function classLoaderGuzzlePromises($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    $path = str_replace("GuzzleHttp/Promise/", "", $path);

    $file = __DIR__ . '/src/' . $path . '.php';

//    dd($file);
    if (file_exists($file)) {
       // dd($file);
        require_once $file;
    }
}
spl_autoload_register('classLoaderGuzzlePromises');

require_once (__DIR__ . "/src/functions_include.php");
