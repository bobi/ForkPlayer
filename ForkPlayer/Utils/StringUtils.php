<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 20.01.2017
 * Time: 19:43
 */

namespace ForkPlayer\Utils;


class StringUtils
{

    /**
     * StringUtils constructor.
     */
    private function __construct()
    {
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @param bool $case
     * @return bool
     */
    public static function startsWith($haystack, $needle, $case = true) {
        if ($case) {
            return (strcmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
        }
        return (strcasecmp(substr($haystack, 0, strlen($needle)), $needle) === 0);
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @param bool $case
     * @return bool
     */
    public static function endsWith($haystack, $needle, $case = true) {
        if ($case) {
            return (strcmp(substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0);
        }
        return (strcasecmp(substr($haystack, strlen($haystack) - strlen($needle)), $needle) === 0);
    }
}