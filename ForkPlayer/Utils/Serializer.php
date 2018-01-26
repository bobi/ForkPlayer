<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 20.01.2017
 * Time: 14:42
 */

namespace ForkPlayer\Utils;


use DOMDocument;
use ForkPlayer\Playlist\Item;
use ForkPlayer\Playlist\ItemType;
use ForkPlayer\Playlist\Playlist;

class Serializer
{

    /**
     * Serializer constructor.
     */
    private function __construct()
    {
    }

    /**
     * @param Playlist $playlist
     * @return string
     */
    public static function toXml($playlist)
    {
        $xml = new DOMDocument("1.0", "UTF-8");

        $channels = $playlist->getItems();

        $items = $xml->createElement("items");

        if (!empty($channels)) {
            $playlistName = $playlist->getName();
            if (!empty($playlistName)) {
                $elem = $xml->createElement("playlist_name");
                $elem->appendChild($xml->createCDATASection($playlistName));

                $items->appendChild($elem);
            }

            $nextPageUrl = $playlist->getNextPage();
            if (!empty($nextPageUrl)) {
                $elem = $xml->createElement("next_page_url");
                $elem->appendChild($xml->createCDATASection($nextPageUrl));

                $items->appendChild($elem);
            }

            $typeList = $playlist->getTypeList();
            if (!empty($typeList)) {
                $elem = $xml->createElement("typeList");
                $elem->appendChild($xml->createCDATASection($typeList));

                $items->appendChild($elem);
            }

            $getInfo = $playlist->getGetInfo();
            if (!empty($getInfo)) {
                $elem = $xml->createElement("get_info");
                $elem->appendChild($xml->createCDATASection($getInfo));

                $items->appendChild($elem);
            }

            $isIpTv = $playlist->isIpTv();
            if (!empty($isIpTv)) {
                $elem = $xml->createElement("is_iptv");
                $elem->appendChild($xml->createCDATASection($isIpTv ? 'true' : 'false'));

                $items->appendChild($elem);
            }

            $timeout = $playlist->getTimeout();
            if (!empty($timeout)) {
                $elem = $xml->createElement("timeout");
                $elem->appendChild($xml->createCDATASection($timeout));

                $items->appendChild($elem);
            }

            foreach ($channels as $channel) {
                $chElement = $xml->createElement("channel");

                $elem = $xml->createElement("title");
                $elem->appendChild($xml->createCDATASection($channel->getName()));
                $chElement->appendChild($elem);

                $chDescription = $channel->getDescription();
                if (!empty($chDescription)) {
                    $elem = $xml->createElement("description");
                    $elem->appendChild($xml->createCDATASection($chDescription));
                    $chElement->appendChild($elem);
                }

                switch ($channel->getType()->get()) {
                    case ItemType::DIRECTORY:
                        $elem = $xml->createElement("playlist_url");
                        $elem->appendChild($xml->createCDATASection($channel->getLink()));
                        $chElement->appendChild($elem);
                        break;
                    case ItemType::FILE:
                        $elem = $xml->createElement("stream_url");
                        $elem->appendChild($xml->createCDATASection($channel->getLink()));
                        $chElement->appendChild($elem);
                        break;
                    case ItemType::SEARCH:
                        $elem = $xml->createElement("playlist_url");
                        $elem->appendChild($xml->createCDATASection($channel->getLink()));
                        $chElement->appendChild($elem);

                        $elem = $xml->createElement("search_on");
                        $elem->appendChild($xml->createTextNode($channel->getName()));
                        $chElement->appendChild($elem);
                        break;
                }

                $imageLink = $channel->getImageLink();
                if (!empty($imageLink)) {
                    $elem = $xml->createElement("logo_30x30");
                    $elem->appendChild($xml->createCDATASection($imageLink));
                    $chElement->appendChild($elem);
                }

                $items->appendChild($chElement);
            }
        }

        $xml->appendChild($items);

        return $xml->saveXML();
    }

    /**
     * @param Playlist $playlist
     * @return string
     */
    public static function toJson($playlist) {
        $channels = $playlist->getItems();

        $result = array();

        if (!empty($channels)) {
            $playlistName = $playlist->getName();
            if (!empty($playlistName)) {
                $result['playlist_name'] = $playlistName;
            }

            $nextPageUrl = $playlist->getNextPage();
            if (!empty($nextPageUrl)) {
                $result['next_page_url'] = $nextPageUrl;
            }

            $typeList = $playlist->getTypeList();
            if (!empty($typeList)) {
                $result['typeList'] = $typeList;
            }

            $getInfo = $playlist->getGetInfo();
            if (!empty($getInfo)) {
                $result['get_info'] = $getInfo;
            }

            $isIpTv = $playlist->isIpTv();
            if (!empty($isIpTv)) {
                $result['is_iptv'] = $isIpTv ? 'true' : 'false';
            }

            $timeout = $playlist->getTimeout();
            if (!empty($timeout)) {
                $result['timeout'] = $timeout;
            }

            $items = array();

            /**
             * @var Item channel
             */
            foreach ($channels as $channel) {
                $ch = array();

                $ch['title'] = $channel->getName();

                $description = $channel->getDescription();
                if (!empty($description)) {
                    $ch['description'] = $description;
                }

                switch ($channel->getType()->get()) {
                    case ItemType::DIRECTORY:
                        $ch['playlist_url'] = $channel->getLink();
                        break;
                    case ItemType::FILE:
                        $ch['stream_url'] = $channel->getLink();
                        break;
                    case ItemType::SEARCH:
                        $ch['playlist_url'] = $channel->getLink();
                        $ch['search_on'] = $channel->getName();
                        break;
                }

                $imageLink = $channel->getImageLink();
                if (!empty($imageLink)) {
                    $ch['logo_30x30'] = $imageLink;
                }

                array_push($items, $ch);
            }

            $result['channels'] = $items;
        }

        $res = json_encode($result, JSON_UNESCAPED_UNICODE);

        if ($res) {
            return $res;
        } else {
            return json_encode(array('channels' => array(array('title' => json_last_error_msg()))));
        }
    }
}