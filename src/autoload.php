<?php

spl_autoload_register(function ($class) {
    # $base_dir = __DIR__ . '/src/'; // base directory for the namespace prefix

    $file = str_replace('\\', '/', $class) . '.php';

    if (file_exists($file)) { // if the file exists, require it
        require $file;
    }

});