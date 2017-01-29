<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 20.01.2017
 * Time: 13:53
 */

require_once __DIR__ . '/config.php';

if (!class_exists('\ForkPlayer')) {
    spl_autoload_register(function ($klass) {
        $parts = explode('\\', $klass);

        // Issue #164
        if ($parts[0] == '') {
            array_shift($parts);
        }

        if ($parts[0] == 'ForkPlayer') {
            array_shift($parts);

            $path = __DIR__ . '/' . implode('/', $parts) . '.php';
            if (file_exists($path)) {
                require $path;
            }
        }
    });
}