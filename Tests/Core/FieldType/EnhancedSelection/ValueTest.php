<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\Core\FieldType\EnhancedSelection;

use eZ\Publish\Core\FieldType\Value as BaseValue;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Value;

class ValueTest extends \PHPUnit_Framework_TestCase
{
    public function testInstanceOfFieldTypeValue()
    {
        $this->assertInstanceOf(BaseValue::class, new Value(array()));
    }
}
