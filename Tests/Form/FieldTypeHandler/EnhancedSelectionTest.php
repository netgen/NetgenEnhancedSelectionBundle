<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EnhancedSelectionBundle\Form\FieldTypeHandler\EnhancedSelection;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Value as EnhancedSelectionValue;
use Symfony\Component\Form\FormBuilder;

class EnhancedSelectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EnhancedSelection
     */
    private $handler;

    public function setUp()
    {
        $this->handler = new EnhancedSelection();
    }

    public function testInstanceOfFieldTypeHandler()
    {
        $this->assertInstanceOf(FieldTypeHandler::class, $this->handler);
    }

    public function testConvertFieldValueToForm()
    {
        $identifiers = array('identifier1', 'identifier2');
        $selection = new EnhancedSelectionValue($identifiers);

        $converted = $this->handler->convertFieldValueToForm($selection);

        $this->assertEquals($identifiers, $converted);
    }

    public function testConvertFieldValueToFormWithFieldDefinitionMultiple()
    {
        $identifiers = array('identifier1', 'identifier2');
        $selection = new EnhancedSelectionValue($identifiers);
        $fieldDefinition = new FieldDefinition(
            array(
                'fieldSettings' => array(
                    'isMultiple' => true,
                )
            )
        );

        $converted = $this->handler->convertFieldValueToForm($selection, $fieldDefinition);

        $this->assertEquals($identifiers, $converted);
    }

    public function testConvertFieldValueToFormWithFieldDefinitionSingle()
    {
        $identifiers = array('identifier1', 'identifier2');
        $selection = new EnhancedSelectionValue($identifiers);
        $fieldDefinition = new FieldDefinition(
            array(
                'fieldSettings' => array(
                    'isMultiple' => false,
                )
            )
        );

        $converted = $this->handler->convertFieldValueToForm($selection, $fieldDefinition);

        $this->assertEquals($identifiers[0], $converted);
    }

    public function testConvertFieldValueFromForm()
    {
        $identifiers = array('identifier1', 'identifier2');
        $selection = new EnhancedSelectionValue($identifiers);

        $converted = $this->handler->convertFieldValueFromForm($identifiers);

        $this->assertEquals($selection, $converted);
    }

    public function testBuildFieldCreateForm()
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(array('add'))
            ->getMock();

        $formBuilder->expects($this->once())
            ->method('add');

        $fieldDefinition = new FieldDefinition(
            array(
                'id' => 'id',
                'identifier' => 'identifier',
                'isRequired' => true,
                'descriptions' => array('fre-FR' => 'fre-FR'),
                'names' => array('fre-FR' => 'fre-FR'),
                'fieldSettings' => array(
                    'options' => array(
                        array(
                            'identifier' => 'identifier0',
                            'name' => 'Identifier0',
                        ),
                        array(
                            'identifier' => 'identifier1',
                            'name' => 'Identifier1',
                        ),
                    ),
                    'isMultiple' => true,
                )
            )
        );

        $languageCode = 'eng-GB';

        $this->handler->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
