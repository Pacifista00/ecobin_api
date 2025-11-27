<?php

namespace App\Helpers;

class FcmSend
{
    public static function send($targetToken, $title, $body, $data = [])
    {
        $accessToken = \App\Helpers\FcmToken::generateAccessToken();

        $url = "https://fcm.googleapis.com/v1/projects/ecobin-edde9/messages:send";

        $message = [
            "message" => [
                "token" => $targetToken,
                "notification" => [
                    "title" => $title,
                    "body" => $body,
                ],
                "data" => $data
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer $accessToken",
            "Content-Type: application/json",
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($message));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);

        return $response;
    }
}
