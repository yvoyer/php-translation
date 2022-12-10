<?php declare(strict_types=1);

namespace Star\Component\Translation;

final class Translator
{
    public const KEY_DEFAULT_LOCALE = 'default_locale';

    public static function initAttribute(
        string $value,
        string $defaultLocale
    ): LocalizationMap {
        return LocalizationMap::fromArray(
            $defaultLocale,
            [
                $defaultLocale => $value,
            ]
        );
    }

    public static function readAttribute(
        string $attribute,
        string $attributeData,
        ?string $locale,
        FailureStrategy $strategy
    ): string {
        return LocalizationMap::fromJson($attributeData)
            ->getLocalizedValue($attribute, $locale, $strategy);
    }

    public static function writeAttribute(
        string $attribute,
        string $newValue,
        string $locale,
        string $attributeData
    ): string {
        return LocalizationMap::fromJson($attributeData)->setLocalizedValue($attribute, $newValue, $locale);
    }
}
