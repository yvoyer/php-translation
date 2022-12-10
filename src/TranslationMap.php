<?php declare(strict_types=1);

namespace Star\Component\Translation;

use Star\Component\Translation\Strategy\ThrowExceptionOnFailure;
use function array_key_exists;
use function array_keys;
use function implode;
use function sprintf;

final class TranslationMap
{
    private string $defaultLocale;

    /**
     * @var LocalizationMap[] Indexed by attribute name
     */
    private array $map;

    /**
     * @param string $defaultLocale
     * @param LocalizationMap[] $map
     */
    private function __construct(string $defaultLocale, array $map)
    {
        $this->defaultLocale = $defaultLocale;
        $this->map = $map;
    }

    public function getValue(string $attribute, string $locale = null): string
    {
        if (!$locale) {
            $locale = $this->defaultLocale;
        }

        if (!array_key_exists($attribute, $this->map)) {
            throw new TranslationNotFound(
                sprintf(
                    'Attribute "%s" was not found, available attributes are "%s".',
                    $attribute,
                    implode(', ', array_keys($this->map))
                )
            );
        }

        return $this->map[$attribute]->getLocalizedValue($attribute, $locale, new ThrowExceptionOnFailure());
    }

    public function acceptTranslationVisitor(TranslationVisitor $visitor): void
    {
        foreach ($this->map as $attribute => $localeMap) {
            $localeMap->acceptTranslationVisitor($attribute, $visitor);
        }
    }

    /**
     * @param string $defaultLocale
     * @param array<string, array<string, string>> $map
     * @return static
     */
    public static function fromArray(string $defaultLocale, array $map): self
    {
        $normalizedMap = [];
        foreach ($map as $attribute => $locales) {
            $normalizedMap[$attribute] = LocalizationMap::fromArray($defaultLocale, $locales);
        }

        return new self($defaultLocale, $normalizedMap);
    }
}
