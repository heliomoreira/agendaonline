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
        try {
            $client = new Client([
                'base_uri' => rtrim(config('sms.sms_url'), '/'),
                'headers'  => [
                    'Content-Type'  => 'application/json',
                    'Authorization' => 'Basic ' . config('sms.sms_token'),
                ],
            ]);

            $response = $client->post('/api/rest/sms', [
                'json' => [
                    'to'           => ['351' . $receiver],
                    'from'         => $sender,
                    'message'      => $message,
                    'campaignName' => $sender,
                    'parts'        => 10,
                ],
            ]);

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
                'id'          => $sms['id'],
                'is_delivered'=> $sms['isDelivered'],
                'is_clicked'  => $sms['isClicked'],
                'events'      => $sms['events'],
            ]);

        } catch (GuzzleException $e) {
            Log::error('SMS delivery details failed', ['smsId' => $smsId, 'error' => $e->getMessage()]);
            return response()->json('error', 500);
        }
    }
}
