<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\Core\FieldType\EnhancedSelection;

use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Type;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Value;
use PHPUnit\Framework\TestCase;

class TypeTest extends TestCase
{
    /**
     * @var Type
     */
    protected $type;

    /**
     * @var array
     */
    protected $identifiers = ['identifier0', 'identifier1'];

    /**
     * @var Value
     */
    protected $value;

    /**
     * @var Value
     */
    protected $emptyValue;

    protected function setUp(): void
    {
        $this->type = new Type();
        $this->value = new Value($this->identifiers);
        $this->emptyValue = new Value();
    }

    public function testInstanceOfFieldType()
    {
        self::assertInstanceOf(FieldType::class, $this->type);
    }

    public function testGetFieldTypeIdentifier()
    {
        self::assertSame('sckenhancedselection', $this->type->getFieldTypeIdentifier());
    }

    public function testGetName()
    {
        self::assertSame(implode(', ', $this->identifiers), $this->type->getName($this->value, new FieldDefinition(), 'eng-GB'));
    }

    public function testGetEmptyValue()
    {
        self::assertSame($this->emptyValue, $this->type->getEmptyValue());
    }

    public function testFromHashWithString()
    {
        self::assertSame($this->emptyValue, $this->type->fromHash('some_hash'));
    }

    public function testFromHashWithArray()
    {
        self::assertSame($this->value, $this->type->fromHash($this->identifiers));
    }

    public function testFromHashWithEmptyArray()
    {
        $value = $this->type->fromHash([1, 2, 3]);

        self::assertSame($this->emptyValue, $value);
    }

    public function testToHash()
    {
        $value = $this->type->fromHash($this->identifiers);

        self::assertSame($this->identifiers, $this->type->toHash($value));
    }

    public function testIsEmptyValue()
    {
        self::assertFalse($this->type->isEmptyValue($this->value));
        self::assertTrue($this->type->isEmptyValue($this->emptyValue));
    }

    public function testIsSearchableShouldAlwaysReturnTrue()
    {
        self::assertTrue($this->type->isSearchable());
    }

    public function testFromPersistenceValue()
    {
        $fieldValue = new FieldValue(
            [
                'externalData' => $this->identifiers,
            ]
        );
        self::assertSame($this->value, $this->type->fromPersistenceValue($fieldValue));
    }

    public function testToPersistenceValue()
    {
        $fieldValue = new FieldValue(
            [
                'data' => null,
                'externalData' => $this->identifiers,
                'sortKey' => false,
            ]
        );

        self::assertSame($fieldValue, $this->type->toPersistenceValue($this->value));
    }

    public function testValidateFieldSettingsWithEmptyFieldSettings()
    {
        $errors = $this->type->validateFieldSettings('test');

        self::assertSame(new ValidationError('Field settings must be in form of an array'), $errors[0]);
    }

    public function testValidateFieldSettingsWithMissingFieldSettings()
    {
        $validationError = new ValidationError(
            "'%setting%' setting is unknown",
            null,
            [
                '%setting%' => 'test',
            ]
        );

        $fieldSettings = [
            'test' => [],
        ];

        $errors = $this->type->validateFieldSettings($fieldSettings);

        self::assertSame($validationError, $errors[0]);
    }

    public function testValidateFieldSettingsWithInvalidFieldSettings()
    {
        $fieldSettings = [
            'options' => 'test',
            'isMultiple' => 'test',
            'delimiter' => false,
            'query' => false,
        ];

        $validationError1 = new ValidationError(
            "'%setting%' setting value must be of array type",
            null,
            [
                '%setting%' => 'options',
            ]
        );

        $validationError2 = new ValidationError(
            "'%setting%' setting value must be of boolean type",
            null,
            [
                '%setting%' => 'isMultiple',
            ]
        );

        $validationError3 = new ValidationError(
            "'%setting%' setting value must be of string type",
            null,
            [
                '%setting%' => 'delimiter',
            ]
        );

        $validationError4 = new ValidationError(
            "'%setting%' setting value must be of string type",
            null,
            [
                '%setting%' => 'query',
            ]
        );

        $errors = $this->type->validateFieldSettings($fieldSettings);

        self::assertSame($validationError1, $errors[0]);
        self::assertSame($validationError2, $errors[1]);
        self::assertSame($validationError3, $errors[2]);
        self::assertSame($validationError4, $errors[3]);
    }

    public function testValidateFieldSettingsWithMissingOptionsInFieldSettings()
    {
        $fieldSettings = [
            'options' => [
                [
                ],
            ],
            'isMultiple' => false,
            'delimiter' => 'delimiter',
            'query' => 'query',
        ];

        $validationError1 = new ValidationError(
            "'%setting%' setting value item must have a 'name' property",
            null,
            [
                '%setting%' => 'options',
            ]
        );

        $validationError2 = new ValidationError(
            "'%setting%' setting value item must have an 'identifier' property",
            null,
            [
                '%setting%' => 'options',
            ]
        );

        $validationError3 = new ValidationError(
            "'%setting%' setting value item must have an 'priority' property",
            null,
            [
                '%setting%' => 'options',
            ]
        );

        $errors = $this->type->validateFieldSettings($fieldSettings);

        self::assertSame($validationError1, $errors[0]);
        self::assertSame($validationError2, $errors[1]);
        self::assertSame($validationError3, $errors[2]);
    }

    public function testValidateFieldSettingsWithInvalidOptionsInFieldSettings()
    {
        $fieldSettings = [
            'options' => [
                [
                    'name' => false,
                    'identifier' => false,
                    'priority' => 'test',
                ],
            ],
            'isMultiple' => false,
            'delimiter' => 'delimiter',
            'query' => 'query',
        ];

        $validationError1 = new ValidationError(
            "'%setting%' setting value item's 'name' property must be of string value",
            null,
            [
                '%setting%' => 'options',
            ]
        );

        $validationError2 = new ValidationError(
            "'%setting%' setting value item's 'name' property must have a value",
            null,
            [
                '%setting%' => 'options',
            ]
        );

        $validationError3 = new ValidationError(
            "'%setting%' setting value item's 'identifier' property must be of string value",
            null,
            [
                '%setting%' => 'options',
            ]
        );

        $validationError4 = new ValidationError(
            "'%setting%' setting value item's 'identifier' property must have a value",
            null,
            [
                '%setting%' => 'options',
            ]
        );

        $validationError5 = new ValidationError(
            "'%setting%' setting value item's 'priority' property must be of numeric value",
            null,
            [
                '%setting%' => 'options',
            ]
        );

        $errors = $this->type->validateFieldSettings($fieldSettings);

        self::assertSame($validationError1, $errors[0]);
        self::assertSame($validationError2, $errors[1]);
        self::assertSame($validationError3, $errors[2]);
        self::assertSame($validationError4, $errors[3]);
        self::assertSame($validationError5, $errors[4]);
    }

    public function testAcceptValueWithSingle()
    {
        $value = new Value(['1']);

        $returnedValue = $this->type->acceptValue('1');

        self::assertSame($value, $returnedValue);
    }

    public function testAcceptValueWithValidArray()
    {
        $returnedValue = $this->type->acceptValue($this->identifiers);

        self::assertSame($this->value, $returnedValue);
    }

    public function testAcceptValueWithInvalidArray()
    {
        $this->expectException(InvalidArgumentType::class);

        $returnedValue = $this->type->acceptValue([1]);

        self::assertSame(1, $returnedValue);
    }

    public function testAcceptValueWithValueObject()
    {
        $this->expectException(InvalidArgumentType::class);

        $value = new Value([true, true]);

        $this->type->acceptValue($value);
    }

    public function testAcceptValueWithValueObjectAndIndentifiersAsString()
    {
        $this->expectException(InvalidArgumentType::class);

        $value = new Value();
        $value->identifiers = 'test';

        $this->type->acceptValue($value);
    }
}
