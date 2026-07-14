<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private Client $client;

    public function __construct()
    {

    }

    public static function send(string $receiver, string $message, string $sender): \Illuminate\Http\JsonResponse
    {
        Log::info("Entrou no SMS.");
        try {
            Log::debug('Sending SMS to ' . $receiver);

            $client = new Client([
                'base_uri' => rtrim(config('sms.sms_url'), '/'),
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Basic ' . config('sms.sms_token'),
                ],
            ]);


            $encoding = self::detectEncoding($message);

            $response = $client->post('/api/rest/sms', [
                'json' => [
                    'to' => [$receiver],
                    'from' => $sender,
                    'message' => $message,
                    'campaignName' => $sender,
                    'parts' => 10,
                    'encoding' => $encoding,
                ],
            ]);

            Log::debug('SMS Response ' . $response->getBody()->getContents());

            $res = json_decode($response->getBody()->getContents(), true);

            return isset($res['result'][0]['accepted']) && $res['result'][0]['accepted'] === true
                ? response()->json('ok', 200)
                : response()->json('error', 422);

        } catch (GuzzleException $e) {
            Log::error('SMS send failed', ['error' => $e->getMessage()]);
            return response()->json('error', 500);
        }
    }

    public function getSmsDeliveryDetails(string $smsId): \Illuminate\Http\JsonResponse
    {
        try {
            $response = $this->client->get("/api/rest/sms/{$smsId}");
            $res = json_decode($response->getBody()->getContents(), true);

            $sms = $res['data'][0] ?? null;

            if (!$sms) {
                return response()->json('not_found', 404);
            }

            return response()->json([
                'id' => $sms['id'],
                'is_delivered' => $sms['isDelivered'],
                'is_clicked' => $sms['isClicked'],
                'events' => $sms['events'],
            ]);

        } catch (GuzzleException $e) {
            Log::error('SMS delivery details failed', ['smsId' => $smsId, 'error' => $e->getMessage()]);
            return response()->json('error', 500);
        }
    }

    private static function detectEncoding(string $message): string
    {
        // Caracteres do GSM7 base (tabela padrão + extensão com escape)
        $gsm7 = ' @£$¥èéùìòÇØøÅåΔ_ΦΓΛΩΠΨΣΘΞÆæßÉ!"#¤%&\'()*+,-./0123456789:;<=>?'
            . '¡ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÑÜ§¿abcdefghijklmnopqrstuvwxyzäöñüà'
            . '^{}\\[~]|€'
            . chr(10) . chr(13) . chr(12);

        // Extra cobertos pela variante gsm-pt (ex.: minúsculas acentuadas PT)
        $gsmPtExtra = "áíóúâêôãõàçÁÍÓÚÂÊÔÃÕÀ";

        $isGsm = true;
        $isGsmPt = true;

        foreach (preg_split('//u', $message, -1, PREG_SPLIT_NO_EMPTY) as $char) {
            if (mb_strpos($gsm7, $char) === false) {
                $isGsm = false;
                if (mb_strpos($gsmPtExtra, $char) === false) {
                    $isGsmPt = false;
                }
            }
        }

        if ($isGsm)   return 'gsm';
        if ($isGsmPt) return 'gsm-pt';
        return 'utf-16';
    }

    public static function countParts(string $message): int
    {
        $len = mb_strlen($message);
        $encoding = self::detectEncoding($message);

        // chars por parte conforme encoding
        [$single, $multi] = match ($encoding) {
            'utf-16' => [70, 67],
            'gsm-pt' => [155, 149],
            default  => [160, 153], // gsm
        };

        if ($len <= $single) {
            return 1;
        }

        return (int) ceil($len / $multi);
    }

    public static function messageCost(string $message): float
    {
        return self::countParts($message) * (float) config('sms.sms_value', config('sms.sms_value'));
    }
}
