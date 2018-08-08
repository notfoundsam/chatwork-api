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
    // protected $data = [];

    private $is_prod;

    function __construct($token, $is_prod = true)
    {
        $this->headers['X-ChatWorkToken'] = $token;
        $this->is_prod = $is_prod;
    }

    public function message($message, $room_id, $type = null)
    {
        $this->createMessage($message, $type);

        if (!$this->is_prod)
        {
            var_dump($message);
            return;
        }

        try
        {
            $response = \Requests::post(static::$API_URL."rooms/{$room_id}/messages", $this->headers, ['body' => $message]);
            var_dump($response);
        }
        catch (\Exception $e)
        {
            // Do nothing
        }
    }

    private function createMessage(&$message, $type = null)
    {
        switch ($type) {
            case '500':
                $message = "[info][title]500 ERROR[/title]{$message}[/info]";
                break;
            
            default:
                # code...
                break;
        }
    }
}
