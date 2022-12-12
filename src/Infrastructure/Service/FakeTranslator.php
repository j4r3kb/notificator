<?php

declare(strict_types=1);

namespace App\Infrastructure\Service;

use App\Domain\Enum\LanguageCode;
use App\Domain\Service\TranslatorInterface;
use Symfony\Component\HttpFoundation\Response;

class FakeTranslator implements TranslatorInterface
{
    public function __construct(
        private bool $throwException = false
    ) {
    }

    public function translate(string $content, LanguageCode $fromLanguage, LanguageCode $toLanguage): string
    {
        if ($this->throwException) {
            throw new \Exception('Translation error occurred.', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $content;
    }
}
