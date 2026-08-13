<?php

namespace App\Classes\Telegram;

class Messenger
{
    public static function sendMessage($botId, $groupId, $msg)
    {
        $path = "https://api.telegram.org/bot" . $botId;

        $params=[
            'chat_id'   => $groupId,
            'text'      => $msg,
        ];
        $ch = curl_init($path . '/sendMessage');

        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, ($params));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);


        curl_close($ch);

        $res = json_decode($result, true);

        return (json_last_error() == JSON_ERROR_NONE) && $res["ok"];
    }
}
