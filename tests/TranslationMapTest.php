<?php declare(strict_types=1);

namespace Star\Component\Translation\Tests;

use PHPUnit\Framework\TestCase;
use Star\Component\Translation\TranslationMap;
use Star\Component\Translation\TranslationNotFound;

final class TranslationMapTest extends TestCase
{
    public function test_it_should_throw_exception_when_no_attribute_found(): void
    {
        $map = TranslationMap::fromArray(
            'en',
            [
                'name' => [
                    'default_locale' => 'en',
                    'fr' => 'value',
                ],
            ]
        );

        $this->expectException(TranslationNotFound::class);
        $this->expectExceptionMessage('Attribute "not-found" was not found, available attributes are "name".');
        $map->getValue('not-found', 'fr');
    }

    public function test_it_should_throw_exception_when_no_value_found_for_locale(): void
    {
        $map = TranslationMap::fromArray(
            'en',
            [
                'name' => [
                    'fr' => 'value',
                ],
            ]
        );

        $this->expectException(TranslationNotFound::class);
        $this->expectExceptionMessage(
            'No localized value was found for attribute "name" with locale "not-found". '
            . 'Available localized values: "{"fr":"value"}".'
        );
        $map->getValue('name', 'not-found');
    }

    public function test_it_should_build_map_from_array(): void
    {
        $map = TranslationMap::fromArray(
            'en',
            [
                'name' => [
                    'default_locale' => 'en',
                    'en' => 'Name (EN)',
                    'fr' => 'Name (FR)',
                ],
                'description' => [
                    'default_locale' => 'en',
                    'en' => 'Description (EN)',
                    'fr' => 'Description (FR)',
                ],
            ]
        );

        self::assertSame('Name (EN)', $map->getValue('name'));
        self::assertSame('Name (EN)', $map->getValue('name', 'en'));
        self::assertSame('Name (FR)', $map->getValue('name', 'fr'));
        self::assertSame('Description (EN)', $map->getValue('description'));
        self::assertSame('Description (EN)', $map->getValue('description', 'en'));
        self::assertSame('Description (FR)', $map->getValue('description', 'fr'));
    }
}
