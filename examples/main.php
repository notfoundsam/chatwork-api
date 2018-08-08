<?php

require __DIR__ . '/../vendor/autoload.php';

use Notfoundsam\Chatwork\Client as Client;

$room_id = 'room_id';
$token = 'token';

// Send message only if it's production
$client = new Client($token, $is_prod = true);
$client->message($room_id, $title = 'FATAL ERROR', 'Mesage', $to = ['0000000' => 'Mr.John']);
