<?php

function classLoaderGuzzlePsr7($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);

    $path = str_replace("GuzzleHttp/Psr7/", "", $path);

    $file = __DIR__ . '/src/' . $path . '.php';

  //  dd($file);
    if (file_exists($file)) {
       // dd($file);
        require_once $file;
    }
}
spl_autoload_register('classLoaderGuzzlePsr7');

require_once (__DIR__ . "/src/functions_include.php");

