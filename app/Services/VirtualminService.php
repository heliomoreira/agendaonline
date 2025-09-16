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
            $url = config('virtualmin.url');

            $client = new Client();
            $res = $client->request('POST', $url, [
                'auth' => [
                    config('virtualmin.user'),
                    config('virtualmin.pass'),
                ],
                'query' => [
                    'program' => 'create-domain',
                    'domain' => $subdomain . '.' . config('tenancy.tenant_domain'),
                    'alias-with-dns' => config('tenancy.tenant_domain'),
                    'default-features' => ''
                    //'dns' => '',
                    //'web' =>''
                ]
            ]);


        } catch (\Exception $ex) {
            Log::alert($ex);
        }
    }
}
