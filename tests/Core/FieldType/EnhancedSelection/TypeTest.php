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
use function implode;

final class TypeTest extends TestCase
{
    /**
     * @var Type
     */
    private $type;

    /**
     * @var array
     */
    private $identifiers = ['identifier0', 'identifier1'];

    /**
     * @var Value
     */
    private $value;

    /**
     * @var Value
     */
    private $emptyValue;

    protected function setUp(): void
    {
        $this->type = new Type();
        $this->value = new Value($this->identifiers);
        $this->emptyValue = new Value();
    }

    public function testInstanceOfFieldType(): void
    {
        self::assertInstanceOf(FieldType::class, $this->type);
    }

    public function testGetFieldTypeIdentifier(): void
    {
        self::assertSame('sckenhancedselection', $this->type->getFieldTypeIdentifier());
    }

    public function testGetName(): void
    {
        self::assertSame(implode(', ', $this->identifiers), $this->type->getName($this->value, new FieldDefinition(), 'eng-GB'));
    }

    public function testGetEmptyValue(): void
    {
        self::assertSame($this->emptyValue->identifiers, $this->type->getEmptyValue()->identifiers);
    }

    public function testFromHashWithString(): void
    {
        self::assertSame($this->emptyValue->identifiers, $this->type->fromHash('some_hash')->identifiers);
    }

    public function testFromHashWithArray(): void
    {
        self::assertSame($this->value->identifiers, $this->type->fromHash($this->identifiers)->identifiers);
    }

    public function testFromHashWithEmptyArray(): void
    {
        $value = $this->type->fromHash([1, 2, 3]);

        self::assertSame($this->emptyValue->identifiers, $value->identifiers);
    }

    public function testToHash(): void
    {
        $value = $this->type->fromHash($this->identifiers);

        self::assertSame($this->identifiers, $this->type->toHash($value));
    }

    public function testIsEmptyValue(): void
    {
        self::assertFalse($this->type->isEmptyValue($this->value));
        self::assertTrue($this->type->isEmptyValue($this->emptyValue));
    }

    public function testIsSearchableShouldAlwaysReturnTrue(): void
    {
        self::assertTrue($this->type->isSearchable());
    }

    public function testFromPersistenceValue(): void
    {
        $fieldValue = new FieldValue(
            [
                'externalData' => $this->identifiers,
            ]
        );
        self::assertSame($this->value->identifiers, $this->type->fromPersistenceValue($fieldValue)->identifiers);
    }

    public function testToPersistenceValue(): void
    {
        $fieldValue = new FieldValue(
            [
                'data' => null,
                'externalData' => $this->identifiers,
                'sortKey' => false,
            ]
        );

        self::assertSame($fieldValue->externalData, $this->type->toPersistenceValue($this->value)->externalData);
    }

    public function testValidateFieldSettingsWithEmptyFieldSettings(): void
    {
        $errors = $this->type->validateFieldSettings('test');

        $validationError = new ValidationError('Field settings must be in form of an array');

        self::assertSame($validationError->getTarget(), $errors[0]->getTarget());
        self::assertSame((string) $validationError->getTranslatableMessage(), (string) $errors[0]->getTranslatableMessage());
    }

    public function testValidateFieldSettingsWithMissingFieldSettings(): void
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

        self::assertSame($validationError->getTarget(), $errors[0]->getTarget());
        self::assertSame((string) $validationError->getTranslatableMessage(), (string) $errors[0]->getTranslatableMessage());
    }

    public function testValidateFieldSettingsWithInvalidFieldSettings(): void
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

        self::assertSame($validationError1->getTarget(), $errors[0]->getTarget());
        self::assertSame((string) $validationError1->getTranslatableMessage(), (string) $errors[0]->getTranslatableMessage());

        self::assertSame($validationError2->getTarget(), $errors[1]->getTarget());
        self::assertSame((string) $validationError2->getTranslatableMessage(), (string) $errors[1]->getTranslatableMessage());

        self::assertSame($validationError3->getTarget(), $errors[2]->getTarget());
        self::assertSame((string) $validationError3->getTranslatableMessage(), (string) $errors[2]->getTranslatableMessage());

        self::assertSame($validationError4->getTarget(), $errors[3]->getTarget());
        self::assertSame((string) $validationError4->getTranslatableMessage(), (string) $errors[3]->getTranslatableMessage());
    }

    public function testValidateFieldSettingsWithMissingOptionsInFieldSettings(): void
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

        self::assertSame($validationError1->getTarget(), $errors[0]->getTarget());
        self::assertSame((string) $validationError1->getTranslatableMessage(), (string) $errors[0]->getTranslatableMessage());

        self::assertSame($validationError2->getTarget(), $errors[1]->getTarget());
        self::assertSame((string) $validationError2->getTranslatableMessage(), (string) $errors[1]->getTranslatableMessage());

        self::assertSame($validationError3->getTarget(), $errors[2]->getTarget());
        self::assertSame((string) $validationError3->getTranslatableMessage(), (string) $errors[2]->getTranslatableMessage());
    }

    public function testValidateFieldSettingsWithInvalidOptionsInFieldSettings(): void
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

        self::assertSame($validationError1->getTarget(), $errors[0]->getTarget());
        self::assertSame((string) $validationError1->getTranslatableMessage(), (string) $errors[0]->getTranslatableMessage());

        self::assertSame($validationError2->getTarget(), $errors[1]->getTarget());
        self::assertSame((string) $validationError2->getTranslatableMessage(), (string) $errors[1]->getTranslatableMessage());

        self::assertSame($validationError3->getTarget(), $errors[2]->getTarget());
        self::assertSame((string) $validationError3->getTranslatableMessage(), (string) $errors[2]->getTranslatableMessage());

        self::assertSame($validationError4->getTarget(), $errors[3]->getTarget());
        self::assertSame((string) $validationError4->getTranslatableMessage(), (string) $errors[3]->getTranslatableMessage());

        self::assertSame($validationError5->getTarget(), $errors[4]->getTarget());
        self::assertSame((string) $validationError5->getTranslatableMessage(), (string) $errors[4]->getTranslatableMessage());
    }

    public function testAcceptValueWithSingle(): void
    {
        $value = new Value(['1']);

        $returnedValue = $this->type->acceptValue('1');

        self::assertSame($value->identifiers, $returnedValue->identifiers);
    }

    public function testAcceptValueWithValidArray(): void
    {
        $returnedValue = $this->type->acceptValue($this->identifiers);

        self::assertSame($this->value->identifiers, $returnedValue->identifiers);
    }

    public function testAcceptValueWithInvalidArray(): void
    {
        $this->expectException(InvalidArgumentType::class);

        $returnedValue = $this->type->acceptValue([1]);

        self::assertSame(1, $returnedValue);
    }

    public function testAcceptValueWithValueObject(): void
    {
        $this->expectException(InvalidArgumentType::class);

        $value = new Value([true, true]);

        $this->type->acceptValue($value);
    }

    public function testAcceptValueWithValueObjectAndIdentifiersAsString(): void
    {
        $this->expectException(InvalidArgumentType::class);

        $value = new Value();
        $value->identifiers = 'test';

        $this->type->acceptValue($value);
    }
}
