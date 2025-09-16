<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class VirtualminService
{
    public function createSimpleAlias($subdomain)
    {
        Log::info("URL: ".config('virtualmin.url'));
        Log::info("USER: ".config('virtualmin.user'));
        Log::info("PASS: ".config('virtualmin.pass'));
        Log::info($subdomain);

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
                    'domain'  => $subdomain,
                    'alias'   => config('tenancy.tenant_domain'),
                    'desc'    => "Alias para " . config('tenancy.tenant_domain'),
                ]
            ]);

            Log::info($res->getBody()->getContents());

        } catch (\Exception $ex) {
            Log::alert($ex);
        }
    }
}
