<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 26.01.2017
 * Time: 21:24
 */

$xml = new DOMDocument("1.0", "UTF-8");

$items = $xml->createElement("items");


$chElement = $xml->createElement("channel");

$elem = $xml->createElement("title");
$elem->appendChild($xml->createCDATASection("RemoteFork PHP"));
$chElement->appendChild($elem);

$elem = $xml->createElement("description");
$elem->appendChild($xml->createCDATASection("RemoteFork PHP не поддерживает DLNA"));
$chElement->appendChild($elem);

$items->appendChild($chElement);

$xml->appendChild($items);

echo $xml->saveXML();

