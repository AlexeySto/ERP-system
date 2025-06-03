<?php

namespace app\components\wazzup;

use yii\base\Component;
use yii\log\Logger;

class Wazzup extends Component
{
    public $apiKey = null;
    public $channelId = null;

    public function __construct($config = [])
    {
        $this->apiKey = $config['apiKey'];
        $this->channelId = $config['channelId'];
        parent::__construct($config);
    }

    public function sendMessage($phone, $message, $sendErrors = false)
    {
        $phone = trim(str_replace(' ', '', ltrim($phone, '+')));
        if (!$phone || strlen($phone) < 7) return false;

        $url = 'https://api.wazzup24.com/v3/message';
        $curl = curl_init(); // Используем curl для запроса к Wazzup API
        $post_data = json_encode(array(
            'channelId' => $this->channelId,
            'phone' => $phone,
            'chatType' => 'telegram',
            'text' => $message,
        ));

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type:application/json'
        ));
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $server_response = curl_exec($curl);
        $http_response_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($server_response, 0, $header_size);

        $msg_guid = '';
        if ($http_response_code != 201) {
            $res = json_decode($header, true);
            \Yii::getLogger()->log('Ошибка отправки сообщения на номер ' . $phone . ': ' . $res['description'], Logger::LEVEL_ERROR, 'wa');
        } else {
            $res = json_decode($header);
            $msg_guid = $res->messageId;
        }

        curl_close($curl);
        return $msg_guid;
    }
}