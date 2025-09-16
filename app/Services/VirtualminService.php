<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class VirtualminService
{
    public function createSimpleAlias($subdomain)
    {

        Log::info(config('virtualmin.url'));
        Log::info(config('virtualmin.user'));
        Log::info(config('virtualmin.pass'));

        try {
            $url = config('virtualmin.url');

            Log::info($url);

            $client = new Client();
            $res = $client->request('POST', $url, [
                'auth' => [
                    config('virtualmin.user'),
                    config('virtualmin.pass'),
                ],
                'query' => [
                    'program' => 'create-domain',
                    'pass' => Hash::make($subdomain),
                    'domain' => $subdomain,
                    'alias' => config('tenancy.tenant_domain')
                ]
            ]);

            Log::info($res->getBody()->getContents());

        } catch (\Exception $ex) {
            Log::alert($ex);
        }
    }
}
