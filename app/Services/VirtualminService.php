<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class VirtualminService
{
    public function createSimpleAlias($subdomain)
    {

        try {
            $url = "https://bitsislab.xyz:10000/virtual-server/remote.cgi";

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
                    'domain' => config('tenancy.tenant_domain'),
                    'alias' => $subdomain
                ]
            ]);

            Log::info($res->getBody()->getContents());

        } catch (\Exception $ex) {
            Log::alert($ex);
        }
    }
}
