<?php declare(strict_types=1);

namespace Star\Component\Translation\Strategy;

use Star\Component\Translation\FailureStrategy;
use Star\Component\Translation\TranslationNotFound;
use function json_encode;
use function sprintf;

final class ThrowExceptionOnFailure implements FailureStrategy
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
    ): string {
        throw new TranslationNotFound(
            sprintf(
                'No localized value was found for attribute "%s" with locale "%s". '
                . 'Available localized values: "%s".',
                $attribute,
                $requestedLocale,
                json_encode($map)
            )
        );
    }
}
