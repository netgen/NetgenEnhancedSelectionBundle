<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\Form\FieldTypeHandler;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Value as EnhancedSelectionValue;
use Netgen\Bundle\EnhancedSelectionBundle\Form\FieldTypeHandler\EnhancedSelection;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilder;

class EnhancedSelectionTest extends TestCase
{
    /**
     * @var EnhancedSelection
     */
    protected $handler;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $configResolver;

    public function setUp()
    {
        $this->configResolver = $this->getMockBuilder(ConfigResolverInterface::class)
            ->setMethods(['hasParameter', 'getParameter', 'setDefaultNamespace', 'getDefaultNamespace'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->handler = new EnhancedSelection($this->configResolver);
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

    public function testconvertFieldValueToFormWithIdentifiersArrayEmpty()
    {
        $identifiers = array();
        $selection = new EnhancedSelectionValue($identifiers);
        $fieldDefinition = new FieldDefinition(
            array(
                'fieldSettings' => array(
                    'isMultiple' => false,
                ),
            )
        );

        $converted = $this->handler->convertFieldValueToForm($selection, $fieldDefinition);

        $this->assertEquals('', $converted);
    }

    public function testConvertFieldValueToFormWithFieldDefinitionMultiple()
    {
        $identifiers = array('identifier1', 'identifier2');
        $selection = new EnhancedSelectionValue($identifiers);
        $fieldDefinition = new FieldDefinition(
            array(
                'fieldSettings' => array(
                    'isMultiple' => true,
                ),
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
                ),
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

        $this->configResolver->expects($this->once())
            ->method('getParameter')
            ->willReturn(false);

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
                    'isExpanded' => true,
                ),
            )
        );

        $languageCode = 'eng-GB';

        $this->handler->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
