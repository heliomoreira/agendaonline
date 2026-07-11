<?php

namespace App\Enums;

enum ClientValidation: string
{
    case Email         = 'email';
    case Phone         = 'phone_1';
    case EmailAndPhone = 'email_and_phone';
}
