<?php
/**
 * Created by PhpStorm.
 * User: Andrey
 * Date: 18.01.2017
 * Time: 21:18
 */


error_reporting(0);

require_once __DIR__ . '/ForkPlayer/fp.php';

use ForkPlayer\ForkPlayer;

$fp = new ForkPlayer();

print $fp->dispatch();
