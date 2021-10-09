<?php

function classLoaderSdk($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);

  //  $path = str_replace('AlibabaCloud/Client/', '', $path);

    $file = __DIR__ . '/src/' . $path . '.php';

    if (file_exists($file)) {
       // dd($file);
        require_once $file;
    }
}
spl_autoload_register('classLoaderSdk');

