<?php

namespace Notfoundsam\Chatwork;

use Requests;

/**
 * 
 */
class Client
{
    private static $API_URL = 'https://api.chatwork.com/v2/';

    protected $headers = [];

    private $is_prod;

    function __construct($token, $is_prod = true)
    {
        $this->headers['X-ChatWorkToken'] = $token;
        $this->is_prod = $is_prod;
    }

    public function message($room_id, $title, $message, $trace, $to = [])
    {
        $this->createMessage($title, $message, $trace, $to);

        if (!$this->is_prod)
        {
            return;
        }

        try
        {
            \Requests::post(static::$API_URL."rooms/{$room_id}/messages", $this->headers, ['body' => $message]);
        }
        catch (\Exception $e)
        {
            // Do nothing
        }
    }

    private function createMessage(&$title, &$message, &$trace, &$to)
    {
        $now = new \DateTime();
        $time = $now->modify('+9 hour')->format('Y-m-d H:i:s');

        $str_to = "";

        foreach ($to as $id => $name)
        {
            $str_to .= "\n[To:{$id}] $name";
        }

        $str_trace = '';

        foreach ($trace as $key => $value)
        {
            $str_trace .= "\n{$key} => {$value}";
        }

        $str_trace .= "\nhost_ip => {$_SERVER['SERVER_ADDR']}";

        $message = "[info][title]{$title}[/title]{$message}{$str_trace}[hr]{$time}{$str_to}[/info]";
    }
}
