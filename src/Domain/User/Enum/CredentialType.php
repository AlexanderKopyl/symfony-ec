<?php

declare(strict_types=1);

namespace App\Domain\User\Enum;

enum CredentialType: string
{
    case PASSWORD = 'password';
    case SMS      = 'sms';
    case EMAIL    = 'email';
    case API      = 'api';
}
