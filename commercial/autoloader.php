<?php

spl_autoload_register(function ($className) {
    $baseDir = __DIR__.DIRECTORY_SEPARATOR;
    $path = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    $fullPath = $baseDir.$path.'.php';

    if (is_file($fullPath)) {
        require $fullPath;
    }
});
