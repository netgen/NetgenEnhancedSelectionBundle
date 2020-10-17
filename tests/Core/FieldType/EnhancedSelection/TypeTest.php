<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\Core\FieldType\EnhancedSelection;

use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\Core\FieldType\ValidationError;
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
    protected $identifiers = array('identifier0', 'identifier1');

    /**
     * @var Value
     */
    protected $value;

    /**
     * @var Value
     */
    protected $emptyValue;

    public function setUp()
    {
        $this->type = new Type();
        $this->value = new Value($this->identifiers);
        $this->emptyValue = new Value();
    }

    public function testInstanceOfFieldType()
    {
        $this->assertInstanceOf(FieldType::class, $this->type);
    }

    public function testGetFieldTypeIdentifier()
    {
        $this->assertEquals('sckenhancedselection', $this->type->getFieldTypeIdentifier());
    }

    public function testGetName()
    {
        $this->assertEquals(implode(', ', $this->identifiers), $this->type->getName($this->value));
    }

    public function testGetEmptyValue()
    {
        $this->assertEquals($this->emptyValue, $this->type->getEmptyValue());
    }

    public function testFromHashWithString()
    {
        $this->assertEquals($this->emptyValue, $this->type->fromHash('some_hash'));
    }

    public function testFromHashWithArray()
    {
        $this->assertEquals($this->value, $this->type->fromHash($this->identifiers));
    }

    public function testFromHashWithEmptyArray()
    {
        $value = $this->type->fromHash(array(1, 2, 3));

        $this->assertEquals($this->emptyValue, $value);
    }

    public function testToHash()
    {
        $value = $this->type->fromHash($this->identifiers);

        $this->assertEquals($this->identifiers, $this->type->toHash($value));
    }

    public function testIsEmptyValue()
    {
        $this->assertFalse($this->type->isEmptyValue($this->value));
        $this->assertTrue($this->type->isEmptyValue($this->emptyValue));
    }

    public function testIsSearchableShouldAlwaysReturnTrue()
    {
        $this->assertTrue($this->type->isSearchable());
    }

    public function testFromPersistenceValue()
    {
        $fieldValue = new FieldValue(
            array(
                'externalData' => $this->identifiers,
            )
        );
        $this->assertEquals($this->value, $this->type->fromPersistenceValue($fieldValue));
    }

    public function testToPersistenceValue()
    {
        $fieldValue = new FieldValue(
            array(
                'data' => null,
                'externalData' => $this->identifiers,
                'sortKey' => false,
            )
        );

        $this->assertEquals($fieldValue, $this->type->toPersistenceValue($this->value));
    }

    public function testValidateFieldSettingsWithEmptyFieldSettings()
    {
        $errors = $this->type->validateFieldSettings('test');

        $this->assertEquals(new ValidationError('Field settings must be in form of an array'), $errors[0]);
    }

    public function testValidateFieldSettingsWithMissingFieldSettings()
    {
        $validationError = new ValidationError(
            "'%setting%' setting is unknown",
            null,
            array(
                '%setting%' => 'test',
            )
        );

        $fieldSettings = array(
            'test' => array(),
        );

        $errors = $this->type->validateFieldSettings($fieldSettings);

        $this->assertEquals($validationError, $errors[0]);
    }

    public function testValidateFieldSettingsWithInvalidFieldSettings()
    {
        $fieldSettings = array(
            'options' => 'test',
            'isMultiple' => 'test',
            'isExpanded' => 'test',
            'delimiter' => false,
            'query' => false,
        );

        $validationError1 = new ValidationError(
            "'%setting%' setting value must be of array type",
            null,
            array(
                '%setting%' => 'options',
            )
        );

        $validationError2 = new ValidationError(
            "'%setting%' setting value must be of boolean type",
            null,
            array(
                '%setting%' => 'isMultiple',
            )
        );

        $validationError3 = new ValidationError(
            "'%setting%' setting value must be of boolean type",
            null,
            array(
                '%setting%' => 'isExpanded',
            )
        );

        $validationError4 = new ValidationError(
            "'%setting%' setting value must be of string type",
            null,
            array(
                '%setting%' => 'delimiter',
            )
        );

        $validationError5 = new ValidationError(
            "'%setting%' setting value must be of string type",
            null,
            array(
                '%setting%' => 'query',
            )
        );

        $errors = $this->type->validateFieldSettings($fieldSettings);

        $this->assertEquals($validationError1, $errors[0]);
        $this->assertEquals($validationError2, $errors[1]);
        $this->assertEquals($validationError3, $errors[2]);
        $this->assertEquals($validationError4, $errors[3]);
        $this->assertEquals($validationError5, $errors[4]);
    }

    public function testValidateFieldSettingsWithMissingOptionsInFieldSettings()
    {
        $fieldSettings = array(
            'options' => array(
                array(
                ),
            ),
            'isMultiple' => false,
            'isExpanded' => false,
            'delimiter' => 'delimiter',
            'query' => 'query',
        );

        $validationError1 = new ValidationError(
            "'%setting%' setting value item must have a 'name' property",
            null,
            array(
                '%setting%' => 'options',
            )
        );

        $validationError2 = new ValidationError(
            "'%setting%' setting value item must have an 'identifier' property",
            null,
            array(
                '%setting%' => 'options',
            )
        );

        $validationError3 = new ValidationError(
            "'%setting%' setting value item must have an 'priority' property",
            null,
            array(
                '%setting%' => 'options',
            )
        );

        $errors = $this->type->validateFieldSettings($fieldSettings);

        $this->assertEquals($validationError1, $errors[0]);
        $this->assertEquals($validationError2, $errors[1]);
        $this->assertEquals($validationError3, $errors[2]);
    }

    public function testValidateFieldSettingsWithInvalidOptionsInFieldSettings()
    {
        $fieldSettings = array(
            'options' => array(
                array(
                    'name' => false,
                    'identifier' => false,
                    'priority' => 'test',
                ),
            ),
            'isMultiple' => false,
            'isExpanded' => false,
            'delimiter' => 'delimiter',
            'query' => 'query',
        );

        $validationError1 = new ValidationError(
            "'%setting%' setting value item's 'name' property must be of string value",
            null,
            array(
                '%setting%' => 'options',
            )
        );

        $validationError2 = new ValidationError(
            "'%setting%' setting value item's 'name' property must have a value",
            null,
            array(
                '%setting%' => 'options',
            )
        );

        $validationError3 = new ValidationError(
            "'%setting%' setting value item's 'identifier' property must be of string value",
            null,
            array(
                '%setting%' => 'options',
            )
        );

        $validationError4 = new ValidationError(
            "'%setting%' setting value item's 'identifier' property must have a value",
            null,
            array(
                '%setting%' => 'options',
            )
        );

        $validationError5 = new ValidationError(
            "'%setting%' setting value item's 'priority' property must be of numeric value",
            null,
            array(
                '%setting%' => 'options',
            )
        );

        $errors = $this->type->validateFieldSettings($fieldSettings);

        $this->assertEquals($validationError1, $errors[0]);
        $this->assertEquals($validationError2, $errors[1]);
        $this->assertEquals($validationError3, $errors[2]);
        $this->assertEquals($validationError4, $errors[3]);
        $this->assertEquals($validationError5, $errors[4]);
    }

    public function testAcceptValueWithSingle()
    {
        $value = new Value(array('1'));

        $returnedValue = $this->type->acceptValue('1');

        $this->assertEquals($value, $returnedValue);
    }

    public function testAcceptValueWithValidArray()
    {
        $returnedValue = $this->type->acceptValue($this->identifiers);

        $this->assertEquals($this->value, $returnedValue);
    }

    /**
     * @expectedException \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function testAcceptValueWithInvalidArray()
    {
        $returnedValue = $this->type->acceptValue(array(1));

        $this->assertEquals(1, $returnedValue);
    }

    /**
     * @expectedException \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function testAcceptValueWithValueObject()
    {
        $value = new Value(array(true, true));

        $this->type->acceptValue($value);
    }

    /**
     * @expectedException \eZ\Publish\Core\Base\Exceptions\InvalidArgumentType
     */
    public function testAcceptValueWithValueObjectAndIndentifiersAsString()
    {
        $value = new Value();
        $value->identifiers = 'test';

        $this->type->acceptValue($value);
    }
}
