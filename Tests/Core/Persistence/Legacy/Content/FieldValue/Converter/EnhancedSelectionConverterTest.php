<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\Core\Persistence\Legacy\Content\FieldValue\Converter;

use eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use Netgen\Bundle\EnhancedSelectionBundle\Core\Persistence\Legacy\Content\FieldValue\Converter\EnhancedSelectionConverter;

class EnhancedSelectionConverterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Converter
     */
    private $converter;

    public function setUp()
    {
        $this->converter = new EnhancedSelectionConverter();
    }

    public function testInstanceOfConverter()
    {
        $this->assertInstanceOf(Converter::class, $this->converter);
    }

    public function testGetIndexColumnMustReturnFalse()
    {
        $this->assertFalse($this->converter->getIndexColumn());
    }

    public function testCreate()
    {
        $this->assertEquals($this->converter, EnhancedSelectionConverter::create());
    }

    public function testToStorageValueShouldDoNothing()
    {
        $fieldValue = new FieldValue();
        $storageFieldValue = new StorageFieldValue();

        $this->converter->toStorageValue($fieldValue, $storageFieldValue);
    }

    public function testToFieldValueShouldDoNothing()
    {
        $fieldValue = new FieldValue();
        $storageFieldValue = new StorageFieldValue();

        $this->converter->toFieldValue($storageFieldValue, $fieldValue);
    }
}
