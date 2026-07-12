<?php

namespace App\Services;

use App\Enums\ClientValidation;
use App\Models\Client;
use App\Models\Setting;

class CustomerService
{
    public static function findOrCreate(array $data): Client
    {
        $mode  = Setting::current()->client_validation;
        $email = $data['email']   ?? null;
        $phone = $data['phone_1'] ?? null;

        $criteria = match ($mode) {
            ClientValidation::Email =>
            !empty($email) ? ['email' => $email] : null,

            ClientValidation::Phone =>
            !empty($phone) ? ['phone_1' => $phone] : null,

            ClientValidation::EmailAndPhone =>
            (!empty($email) && !empty($phone))
                ? ['email' => $email, 'phone_1' => $phone]
                : null,

            default => null,
        };

        if ($criteria === null) {
            return Client::create($data);
        }

        return Client::updateOrCreate($criteria, $data);
    }
}
