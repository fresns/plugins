<?php

function classLoaderGuzzle($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);

 //   $path = str_replace("GuzzleHttp/Psr7/", "", $path);
    $path = str_replace("GuzzleHttp/", "", $path);


    $file = __DIR__ . '/src/' . $path . '.php';

    if (file_exists($file)) {
       // dd($file);
        require_once $file;
    }
}
spl_autoload_register('classLoaderGuzzle');

require_once (__DIR__ . "/src/functions_include.php");

