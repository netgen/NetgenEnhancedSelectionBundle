<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\Core\Persistence\Legacy\Content\FieldValue\Converter;

use eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use Netgen\Bundle\EnhancedSelectionBundle\Core\Persistence\Legacy\Content\FieldValue\Converter\EnhancedSelectionConverter;
use PHPUnit\Framework\TestCase;

class EnhancedSelectionConverterTest extends TestCase
{
    /**
     * @var Converter
     */
    protected $converter;

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

    public function testToStorageFieldDefinitionWithEmptyFieldSettings()
    {
        $fieldDefinition = new FieldDefinition();
        $storageDefinition = new StorageFieldDefinition();

        $this->converter->toStorageFieldDefinition($fieldDefinition, $storageDefinition);
    }

    public function testToStorageFieldDefinition()
    {
        $fieldDefinition = new FieldDefinition();
        $fieldDefinition->fieldTypeConstraints->fieldSettings = array(
            'options' => array(
                array(
                    'name' => 'name',
                    'identifier' => 'id',
                    'priority' => 10,
                ),
            ),
            'delimiter' => ',',
            'query' => 'query',
        );
        $storageDefinition = new StorageFieldDefinition();

        $this->converter->toStorageFieldDefinition($fieldDefinition, $storageDefinition);
    }

    public function testToFieldDefinition()
    {
        $fieldDefinition = new FieldDefinition();
        $storageDefinition = new StorageFieldDefinition();
        $storageDefinition->dataText5 = '<?xml version="1.0"?>
            <content><options><option id="3" name="None" identifier="none" priority="0"/><option id="1" name="Include" identifier="include" priority="1"/><option id="2" name="Exclude" identifier="exclude" priority="2"/></options><multiselect>1</multiselect><expanded>1</expanded><delimiter><![CDATA[]]></delimiter><query><![CDATA[]]></query></content>';

        $this->converter->toFieldDefinition($storageDefinition, $fieldDefinition);
    }
}
