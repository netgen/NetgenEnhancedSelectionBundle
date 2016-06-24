<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\Core\FieldType\EnhancedSelection;

use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Type;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Value;

class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Type
     */
    private $type;

    /**
     * @var array
     */
    private $identifiers = array('identifier0', 'identifier1');

    /**
     * @var Value
     */
    private $value;

    /**
     * @var Value
     */
    private $emptyValue;

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
}
