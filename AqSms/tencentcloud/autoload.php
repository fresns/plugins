<?php

function classLoader($class)
{
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__ . '/src/' . $path . '.php';

//    dd($file);
    if (file_exists($file)) {
       // dd($file);
        require_once $file;
    }
}
spl_autoload_register('classLoader');

