<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Domain\Enum\LanguageCode;
use App\Domain\Service\TranslatorInterface;

class FakeTranslator implements TranslatorInterface
{
    public function translate(string $content, LanguageCode $fromLanguage, LanguageCode $toLanguage): string
    {
        return $content;
    }
}
