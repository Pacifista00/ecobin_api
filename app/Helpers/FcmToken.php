<?php

namespace App\Helpers;

use Firebase\JWT\JWT;

class FcmToken
{
    public static function generateAccessToken()
    {
        $serviceAccount = json_decode(file_get_contents(storage_path('app/firebase/firebase_credentials.json')), true);

        $now = time();

        $payload = [
            'iss' => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => $serviceAccount['token_uri'],
            'iat' => $now,
            'exp' => $now + 3600,
        ];

        // Generate JWT
        $jwt = JWT::encode($payload, $serviceAccount['private_key'], 'RS256');

        // Exchange JWT → Access Token
        $ch = curl_init($serviceAccount['token_uri']);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwt,
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = json_decode(curl_exec($ch), true);

        return $response['access_token'];
    }
}
