<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Enum\LanguageCode;

interface TranslatorInterface
{
    public function translate(string $content, LanguageCode $fromLanguage, LanguageCode $toLanguage): string;
}
