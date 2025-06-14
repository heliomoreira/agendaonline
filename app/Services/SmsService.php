<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SmsService
{
    public static function send($receiver, $message, $sender, $company_id)
    {

        try {
            $url = env("EZ4U_SMS_URL");

            $client = new Client();
            $response = $client->request('POST', $url . '/sendSMS.php', [
                'query' => [
                    'licensekey' => env('EZ4U_SMS_API'),
                    'phoneNumber' => $receiver,
                    'messageText' => $message,
                    'account' => env('EZ4U_SMS_ACCOUNT'),
                    'alfaSender' => $sender,
                    'CC' => $company_id,
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
