<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 18.01.2017
 * Time: 21:18
 */

require_once __DIR__ . '/ForkPlayer/fp.php';

libxml_use_internal_errors(true);

use ForkPlayer\ForkPlayer;

$fp = new ForkPlayer();

print $fp->dispatch();

libxml_clear_errors();