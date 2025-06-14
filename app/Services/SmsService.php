<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public static function send($sender, $destinatary, $text)
    {

        try {
            $url = env("EZ4U_SMS_URL");

            $client = new Client();
            $response = $client->request('POST', $url . '/sendSMS.php', [
                'query' => [
                    'licensekey' => env('EZ4U_SMS_API'),
                    'phoneNumber' => $destinatary,
                    'messageText' => $text,
                    'account' => env('EZ4U_SMS_ACCOUNT'),
                    'alfaSender' => $sender,
                    'CC' => 1,
                ]
            ]);

            $res = json_decode($response->getBody()->getContents(), true);

            if ($res['Result'] == "OK") {
                return response()->json('ok', 200);
            } else {
                return response()->json('error', $response->getStatusCode());
            }

        } catch (\GuzzleHttp\Exception\ClientException $ex) {
            Log::error($ex);
            $response = $ex->getResponse();
            return response()->json('error', $response->getStatusCode());
        }
    }
}
