<?php declare(strict_types=1);

namespace Star\Component\Translation;

use Assert\Assertion;
use function array_key_exists;
use function json_decode;
use function json_encode;
use function sprintf;

final class LocalizationMap
{
    private string $defaultLocale;

    /**
     * @var string[] Indexed by locale
     */
    private array $translations;

    /**
     * @param string $defaultLocale
     * @param string[] $translations
     */
    private function __construct(string $defaultLocale, array $translations)
    {
        $this->defaultLocale = $defaultLocale;
        $this->translations = $translations;
    }

    public function setLocalizedValue(string $attribute, string $value, string $locale): string
    {
        $new = $this->translations;
        $new[$locale] = $value;

        return self::fromArray($this->defaultLocale, $new)->toJson();
    }

    public function getLocalizedValue(
        string $attribute,
        ?string $locale,
        FailureStrategy $strategy
    ): string {
        if (!$locale) {
            $locale = $this->defaultLocale;
        }

        if (!array_key_exists($locale, $this->translations)) {
            return $strategy->handleReadFailure(
                $attribute,
                $locale,
                $this->defaultLocale,
                $this->translations
            );
        }

        return $this->translations[$locale];
    }

    public function toJson(): string
    {
        return json_encode( // @phpstan-ignore-line
            [
                Translator::KEY_DEFAULT_LOCALE => $this->defaultLocale,
                'translations' => $this->translations,
            ]
        );
    }

    public function acceptTranslationVisitor(
        string $attribute,
        TranslationVisitor $visitor
    ): void {
        foreach ($this->translations as $locale => $value) {
            if ($this->defaultLocale === $locale) {
                $visitor->visitDefaultLocaleTranslation($attribute, $locale, $value);
                continue;
            }

            $visitor->visitTranslation($attribute, $locale, $value);
        }
    }

    /**
     * @@param string $defaultLocale
     * @param string[] $map
     * @return static
     */
    public static function fromArray(string $defaultLocale, array $map): self
    {
        Assertion::allString($map);

        $normalizedMap = [];
        foreach ($map as $locale => $value) {
            $normalizedMap[$locale] = $value;
        }

        return new self($defaultLocale, $normalizedMap);
    }

    public static function fromJson(string $json): self
    {
        Assertion::isJsonString($json);
        /**
         * @var string[][] $map
         */
        $map = json_decode($json, true);
        Assertion::keyExists($map, 'translations');

        if (!array_key_exists(Translator::KEY_DEFAULT_LOCALE, $map)) {
            throw new MissingDefaultTranslationKey(
                sprintf(
                    'The key "%s" is missing from the locale map, got "%s".',
                    Translator::KEY_DEFAULT_LOCALE,
                    json_encode($map)
                )
            );
        }

        /**
         * @var string $defaultLocale
         */
        $defaultLocale = $map[Translator::KEY_DEFAULT_LOCALE];
        Assertion::string($defaultLocale);
        unset($map[Translator::KEY_DEFAULT_LOCALE]);

        return self::fromArray($defaultLocale, $map['translations']);
    }
}
