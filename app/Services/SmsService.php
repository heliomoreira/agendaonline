<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SmsService
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => rtrim(config('sms.sms_url'), '/'),
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . config('sms.sms_token'),
            ],
        ]);
    }

    public static function send(string $receiver, string $message, string $sender): JsonResponse
    {
        Log::info('Entrou no SMS.');

        try {
            Log::debug('Sending SMS', [
                'receiver' => $receiver,
                'sender' => $sender,
            ]);

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

            // Ler o body apenas uma vez
            $body = (string)$response->getBody();

            Log::debug('SMS Response', [
                'body' => $body,
            ]);

            $res = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid SMS JSON response', [
                    'body' => $body,
                    'json_error' => json_last_error_msg(),
                ]);

                return response()->json('error', 500);
            }

            if (!empty($res['result'][0]['accepted'])) {
                return response()->json('ok', 200);
            }

            Log::warning('SMS not accepted', [
                'response' => $res,
            ]);

            return response()->json('error', 422);

        } catch (GuzzleException $e) {
            Log::error('SMS send failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json('error', 500);
        }
    }

    public function getSmsDeliveryDetails(string $smsId): JsonResponse
    {
        try {
            $response = $this->client->get("/api/rest/sms/{$smsId}");

            $body = (string)$response->getBody();

            Log::debug('SMS Delivery Response', [
                'body' => $body,
            ]);

            $res = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid SMS delivery JSON response', [
                    'body' => $body,
                    'json_error' => json_last_error_msg(),
                ]);

                return response()->json('error', 500);
            }

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
            Log::error('SMS delivery details failed', [
                'smsId' => $smsId,
                'error' => $e->getMessage(),
            ]);

            return response()->json('error', 500);
        }
    }

    private static function detectEncoding(string $message): string
    {
        // Caracteres GSM 7-bit
        $gsm7 = ' @£$¥èéùìòÇØøÅåΔ_ΦΓΛΩΠΨΣΘΞÆæßÉ!"#¤%&\'()*+,-./0123456789:;<=>?'
            . '¡ABCDEFGHIJKLMNOPQRSTUVWXYZÄÖÑÜ§¿abcdefghijklmnopqrstuvwxyzäöñüà'
            . '^{}\\[~]|€'
            . chr(10) . chr(13) . chr(12);

        // Caracteres adicionais suportados por gsm-pt
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

        if ($isGsm) {
            return 'gsm';
        }

        if ($isGsmPt) {
            return 'gsm-pt';
        }

        return 'utf-16';
    }

    public static function countParts(string $message): int
    {
        $len = mb_strlen($message);
        $encoding = self::detectEncoding($message);

        [$single, $multi] = match ($encoding) {
            'utf-16' => [70, 67],
            'gsm-pt' => [155, 149],
            default => [160, 153],
        };

        if ($len <= $single) {
            return 1;
        }

        return (int)ceil($len / $multi);
    }

    public static function messageCost(string $message): float
    {
        return self::countParts($message) * (float)config('sms.sms_value');
    }
}
