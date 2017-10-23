<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 26.01.2017
 * Time: 10:24
 */

define('FP_ROOT', realpath(dirname(dirname(__FILE__))));

if (!function_exists('context_path')) {
    /**
     * @return string
     */
    function context_path(){
        $rootPath = str_replace('\\', "/", FP_ROOT);
        $docRoot = str_replace('\\', "/", realpath($_SERVER['DOCUMENT_ROOT']));

        $paths = preg_split('@/@', str_replace($docRoot, '', $rootPath), NULL, PREG_SPLIT_NO_EMPTY);

        if (!empty($paths)) {
            return $paths[0];
        } else {
            return '';
        }
    }
}

define('FP_CONTEXT_PATH', context_path());

define('REMOTE_FORK_VERSION', '1.37.0');

define('DEFAULT_OUTPUT_FORMAT', 'xml');
