<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum LanguageCode: string
{
    // include all or use vendor package
    case DE = 'de';
    case EN_GB = 'en-gb';
    case ES_ES = 'es-es';
}
