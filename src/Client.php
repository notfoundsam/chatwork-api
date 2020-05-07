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
            $this->send_slack($message, 'iacc_slack移行');
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

        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $str_trace .= "\nHTTP_CLIENT_IP => {$_SERVER['HTTP_CLIENT_IP']}";
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $str_trace .= "\nHTTP_X_FORWARDED_FOR => {$_SERVER['HTTP_X_FORWARDED_FOR']}";
        }
        else
        {
            $str_trace .= "\nREMOTE_ADDR => {$_SERVER['REMOTE_ADDR']}";
        }

        if (!empty($_SERVER['REQUEST_URI']))
        {
            $str_trace .= "\nREQUEST_URI => {$_SERVER['REQUEST_URI']}";
        }
        else
        {
            $str_trace .= "\nREQUEST_URI => null";
        }

        $message = "[info][title]{$title}[/title]{$message}{$str_trace}[hr]{$time}{$str_to}[/info]";
    }

    /**
     * Slack
     */
    public function cw_2_slack($v){

        $v = str_replace(["[info]", "[/info]"], ["```", "```\n"], $v);
        $v = str_replace(["[title]"], "", $v);
        $v = str_replace(["[/title]"], "[hr]", $v);
        $v = str_replace("[hr]", "\n------------------\n", $v);

        $v = str_replace(
            ["(lightbulb)", "(y)", "(devil)", "(*)", "(handshake)", "(cracker)", "(flex)"],
            [":bulb:", ":+1:", ":japanese_ogre:", ":star:", ":pray:", ":heartbeat:", ":ok_hand:"],
            $v);

        return $v;
    }

    public function send_slack($msg, $room = ""){

        $res = [];

        $params = [
            "text" => $msg,
            "token" => SLACK_TOKEN_JIGEN,
            "channel" => $room
        ];

        $options = [
            CURLOPT_URL => "https://api.iacc.tokyo/api/chat.postMessage",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($params, '', '&'),
        ];

        try{
            $ch = curl_init();
            curl_setopt_array($ch, $options);
            $res = curl_exec($ch);
            curl_close($ch);

        }catch(Exception $e){
            //
        }

        return $res;

    }
}
