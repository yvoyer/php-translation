<?php declare(strict_types=1);

namespace Star\Component\Translation\Tests;

use PHPUnit\Framework\TestCase;
use Star\Component\Translation\FailureStrategy;
use Star\Component\Translation\Strategy\ReturnDefaultValue;
use Star\Component\Translation\Strategy\ThrowExceptionOnFailure;
use Star\Component\Translation\Translator;

final class TranslatorTest extends TestCase
{
    public function test_it_should_add_translation_on_write(): void
    {
        $object = new TargetStub('initial', 'en', new ThrowExceptionOnFailure());

        self::assertSame('initial', $object->getName());

        $object->rename('new_name', 'en');

        self::assertSame('new_name', $object->getName());
    }

    public function test_it_should_return_default_locale_when_locale_passed_on_read(): void
    {
        $object = new TargetStub('initial', 'en', new ThrowExceptionOnFailure());

        self::assertSame('initial', $object->getName());
    }

    public function test_it_should_change_attribute_value(): void
    {
        $object = new TargetStub('initial', 'en', new ReturnDefaultValue('default'));

        self::assertSame('initial', $object->getName());
        self::assertSame('initial', $object->getName('en'));
        self::assertSame('default', $object->getName('fr'));

        $object->rename('name-en', 'en');

        self::assertSame('name-en', $object->getName());
        self::assertSame('name-en', $object->getName('en'));
        self::assertSame('default', $object->getName('fr'));

        $object->rename('name-fr', 'fr');

        self::assertSame('name-en', $object->getName());
        self::assertSame('name-en', $object->getName('en'));
        self::assertSame('name-fr', $object->getName('fr'));

        $object->rename('', 'en');
        $object->rename('', 'fr');

        self::assertSame('', $object->getName());
        self::assertSame('', $object->getName('en'));
        self::assertSame('', $object->getName('fr'));
    }
}

final class TargetStub
{
    private string $name;
    private FailureStrategy $strategy;

    public function __construct(
        string $name,
        string $defaultLocale,
        FailureStrategy $strategy
    ) {
        $this->strategy = $strategy;
        $this->name = Translator::initAttribute($name, $defaultLocale)->toJson();
    }

    public function rename(string $newName, string $locale): void
    {
        $this->name = Translator::writeAttribute(
            'name',
            $newName,
            $locale,
            $this->name
        );
    }

    public function getName(string $locale = null): string
    {
        return Translator::readAttribute(
            'name',
            $this->name,
            $locale,
            $this->strategy
        );
    }
}
