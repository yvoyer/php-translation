<?php declare(strict_types=1);

namespace Star\Component\Translation;

interface TranslationVisitor
{
    public function visitDefaultLocaleTranslation(string $attribute, string $locale, string $value): void;
    public function visitTranslation(string $attribute, string $locale, string $value): void;
}
