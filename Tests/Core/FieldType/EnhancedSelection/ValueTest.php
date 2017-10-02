<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\Core\FieldType\EnhancedSelection;

use eZ\Publish\Core\FieldType\Value as BaseValue;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Value;
use PHPUnit\Framework\TestCase;

class ValueTest extends TestCase
{
    protected $value;

    public function setUp()
    {
        $this->value = new Value(
            array(
                'identifier0', 'identifier1',
            )
        );
    }

    public function testInstanceOfFieldTypeValue()
    {
        $this->assertInstanceOf(BaseValue::class, $this->value);
    }

    public function testToStringMethod()
    {
        $identifiers = 'identifier0, identifier1';

        $this->assertEquals($identifiers, (string) ($this->value));
    }
}
