<?php declare(strict_types=1);

namespace Star\Component\Translation\Strategy;

use Star\Component\Translation\FailureStrategy;

final class ReturnDefaultValue implements FailureStrategy
{
    private string $defaultValue;

    public function __construct(string $defaultValue)
    {
        $this->defaultValue = $defaultValue;
    }

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
    ): string {
        return $this->defaultValue;
    }
}
