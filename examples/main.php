<?php

require __DIR__ . '/../vendor/autoload.php';

use Notfoundsam\Chatwork\Client as Client;

$room_id = 'room_id';
$token = 'token';

$client = new Client($token, $is_prod = false);

$client->message('[To:1328058] test', $room_id, $type = '500');
