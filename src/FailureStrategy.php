<?php declare(strict_types=1);

namespace Star\Component\Translation;

interface FailureStrategy
{
    /**
     * @param string $attribute
     * @param string $requestedLocale
     * @param string $defaultLocale
     * @param string[] $map
     * @return string
     */
    public function handleReadFailure(
        string $attribute,
        string $requestedLocale,
        string $defaultLocale,
        array $map
    ): string;
}
