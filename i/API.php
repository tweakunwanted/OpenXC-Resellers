<?php
header('Content-type:application/json');
header('Access-Control-Allow-Origin: *');
require './shorty.php';
require './config.php';

$shorty = new Shorty($hostname, $connection);

$shorty->set_chars($chars);
$shorty->set_salt($salt);
$shorty->set_padding($padding);

$shorty->getUrlsByCreator(0);
?>
