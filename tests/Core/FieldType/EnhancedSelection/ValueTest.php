<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\Core\FieldType\EnhancedSelection;

use eZ\Publish\Core\FieldType\Value as BaseValue;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Value;
use PHPUnit\Framework\TestCase;

final class ValueTest extends TestCase
{
    private $value;

    protected function setUp(): void
    {
        $this->value = new Value(
            [
                'identifier0', 'identifier1',
            ]
        );
    }

    public function testInstanceOfFieldTypeValue(): void
    {
        self::assertInstanceOf(BaseValue::class, $this->value);
    }

    public function testToStringMethod(): void
    {
        $identifiers = 'identifier0, identifier1';

        self::assertSame($identifiers, (string) ($this->value));
    }
}
