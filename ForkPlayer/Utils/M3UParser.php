<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 28.01.2017
 * Time: 16:18
 */

namespace ForkPlayer\Utils;


use ForkPlayer\Playlist\Item;
use ForkPlayer\Playlist\ItemType;

class M3UParser
{
    /**
     * @param string $m3u
     * @return Item[]
     * @throws \Exception
     */
    public static function parse($m3u)
    {
        $source = self::split(trim($m3u));

        $lines = array_filter($source, function ($v) {
            /** @var $v string */
            return !StringUtils::startsWith($v, '#EXTREM:') && !StringUtils::startsWith($v, '#EXTM3U');
        });

        $lines = array_values($lines);

        if (count($lines) % 2 != 0) {
            throw new \Exception("m3u is not properly fomatted");
        }

        $items = array();

        for ($i = 0; $i < count($lines); $i += 2) {
            $info = str_replace('#EXTINF:', '', $lines[$i]);

            $name = trim(substr($info, strpos($info, ',') + 1));
            array_push(
                $items,
                new Item(
                    $name,
                    "",
                    trim($lines[$i + 1]),
                    "",
                    ItemType::from(ItemType::FILE)
                )
            );
        }

        return $items;
    }

    /**
     * @param string $string
     * @return array
     */
    private static function split($string)
    {
        $res = str_replace("\r\n", "\n", $string);

        $arr = explode("\n", $res);

        $arr = array_map('trim', $arr);

        return array_filter($arr, 'strlen');
    }
}