<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum Channel: string
{
    case EMAIL = 'email';
    case FACEBOOK = 'facebook';
    case PUSH = 'push';
    case SMS = 'sms';
}
