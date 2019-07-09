<?php

declare(strict_types=1);

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

    protected function setUp(): void
    {
        $this->configResolver = $this->getMockBuilder(ConfigResolverInterface::class)
            ->setMethods(['hasParameter', 'getParameter', 'setDefaultNamespace', 'getDefaultNamespace'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->handler = new EnhancedSelection($this->configResolver);
    }

    public function testInstanceOfFieldTypeHandler()
    {
        self::assertInstanceOf(FieldTypeHandler::class, $this->handler);
    }

    public function testConvertFieldValueToForm()
    {
        $identifiers = ['identifier1', 'identifier2'];
        $selection = new EnhancedSelectionValue($identifiers);

        $converted = $this->handler->convertFieldValueToForm($selection);

        self::assertSame($identifiers, $converted);
    }

    public function testconvertFieldValueToFormWithIdentifiersArrayEmpty()
    {
        $identifiers = [];
        $selection = new EnhancedSelectionValue($identifiers);
        $fieldDefinition = new FieldDefinition(
            [
                'fieldSettings' => [
                    'isMultiple' => false,
                ],
            ]
        );

        $converted = $this->handler->convertFieldValueToForm($selection, $fieldDefinition);

        self::assertSame('', $converted);
    }

    public function testConvertFieldValueToFormWithFieldDefinitionMultiple()
    {
        $identifiers = ['identifier1', 'identifier2'];
        $selection = new EnhancedSelectionValue($identifiers);
        $fieldDefinition = new FieldDefinition(
            [
                'fieldSettings' => [
                    'isMultiple' => true,
                ],
            ]
        );

        $converted = $this->handler->convertFieldValueToForm($selection, $fieldDefinition);

        self::assertSame($identifiers, $converted);
    }

    public function testConvertFieldValueToFormWithFieldDefinitionSingle()
    {
        $identifiers = ['identifier1', 'identifier2'];
        $selection = new EnhancedSelectionValue($identifiers);
        $fieldDefinition = new FieldDefinition(
            [
                'fieldSettings' => [
                    'isMultiple' => false,
                ],
            ]
        );

        $converted = $this->handler->convertFieldValueToForm($selection, $fieldDefinition);

        self::assertSame($identifiers[0], $converted);
    }

    public function testConvertFieldValueFromForm()
    {
        $identifiers = ['identifier1', 'identifier2'];
        $selection = new EnhancedSelectionValue($identifiers);

        $converted = $this->handler->convertFieldValueFromForm($identifiers);

        self::assertSame($selection->identifiers, $converted->identifiers);
    }

    public function testBuildFieldCreateForm()
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->setMethods(['add'])
            ->getMock();

        $formBuilder->expects(self::once())
            ->method('add');

        $this->configResolver->expects(self::once())
            ->method('getParameter')
            ->willReturn(false);

        $fieldDefinition = new FieldDefinition(
            [
                'id' => 'id',
                'identifier' => 'identifier',
                'isRequired' => true,
                'descriptions' => ['fre-FR' => 'fre-FR'],
                'names' => ['fre-FR' => 'fre-FR'],
                'fieldSettings' => [
                    'options' => [
                        [
                            'identifier' => 'identifier0',
                            'name' => 'Identifier0',
                        ],
                        [
                            'identifier' => 'identifier1',
                            'name' => 'Identifier1',
                        ],
                    ],
                    'isMultiple' => true,
                ],
            ]
        );

        $languageCode = 'eng-GB';

        $this->handler->buildFieldCreateForm($formBuilder, $fieldDefinition, $languageCode);
    }
}
