<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\Core\Persistence\Legacy\Content\FieldValue\Converter;

use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\FieldValue\Converter;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use Ibexa\Core\Persistence\Legacy\Content\StorageFieldValue;
use Netgen\Bundle\EnhancedSelectionBundle\Core\Persistence\Legacy\Content\FieldValue\Converter\EnhancedSelectionConverter;
use PHPUnit\Framework\TestCase;

final class EnhancedSelectionConverterTest extends TestCase
{
    private EnhancedSelectionConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new EnhancedSelectionConverter();
    }

    public function testInstanceOfConverter(): void
    {
        self::assertInstanceOf(Converter::class, $this->converter);
    }

    public function testGetIndexColumnMustReturnFalse(): void
    {
        self::assertFalse($this->converter->getIndexColumn());
    }

    public function testToStorageValueShouldDoNothing(): void
    {
        $fieldValue = new FieldValue();
        $storageFieldValue = new StorageFieldValue();

        $this->converter->toStorageValue($fieldValue, $storageFieldValue);
    }

    public function testToFieldValueShouldDoNothing(): void
    {
        $fieldValue = new FieldValue();
        $storageFieldValue = new StorageFieldValue();

        $this->converter->toFieldValue($storageFieldValue, $fieldValue);
    }

    public function testToStorageFieldDefinitionWithEmptyFieldSettings(): void
    {
        $fieldDefinition = new FieldDefinition();
        $storageDefinition = new StorageFieldDefinition();

        $this->converter->toStorageFieldDefinition($fieldDefinition, $storageDefinition);
    }

    public function testToStorageFieldDefinition(): void
    {
        $fieldDefinition = new FieldDefinition();
        $fieldDefinition->fieldTypeConstraints->fieldSettings = [
            'options' => [
                [
                    'name' => 'name',
                    'identifier' => 'id',
                    'priority' => 10,
                ],
            ],
            'delimiter' => ',',
            'query' => 'query',
        ];
        $storageDefinition = new StorageFieldDefinition();

        $this->converter->toStorageFieldDefinition($fieldDefinition, $storageDefinition);
    }

    public function testToFieldDefinition(): void
    {
        $fieldDefinition = new FieldDefinition();
        $storageDefinition = new StorageFieldDefinition();
        $storageDefinition->dataText5 = '<?xml version="1.0"?>
            <content><options><option id="3" name="None" identifier="none" priority="0"/><option id="1" name="Include" identifier="include" priority="1"/><option id="2" name="Exclude" identifier="exclude" priority="2"/></options><multiselect>1</multiselect><expanded>1</expanded><delimiter><![CDATA[]]></delimiter><query><![CDATA[]]></query></content>';

        $this->converter->toFieldDefinition($storageDefinition, $fieldDefinition);
    }
}
