<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\Templating\Twig;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Repository\Values\ContentType\ContentType;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\Persistence\Content\ContentInfo;
use eZ\Publish\SPI\Persistence\Content\Field;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Value;
use Netgen\Bundle\EnhancedSelectionBundle\Templating\Twig\NetgenEnhancedSelectionRuntime;
use PHPUnit\Framework\TestCase;

class NetgenEnhancedSelectionRuntimeTest extends TestCase
{
    /**
     * @var NetgenEnhancedSelectionRuntime
     */
    protected $runtime;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $translationHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $contentTypeService;

    public function setUp()
    {
        $this->translationHelper = $this->getMockBuilder(TranslationHelper::class)
            ->disableOriginalConstructor()
            ->setMethods(array('getTranslatedField'))
            ->getMock();

        $this->contentTypeService = $this->getMockBuilder(ContentTypeService::class)
            ->disableOriginalConstructor()
            ->setMethods(array('loadContentType'))
            ->getMockForAbstractClass();

        $this->runtime = new NetgenEnhancedSelectionRuntime($this->contentTypeService, $this->translationHelper);
    }

    public function testInstanceOfTwigExtension()
    {
        $this->assertInstanceOf(NetgenEnhancedSelectionRuntime::class, $this->runtime);
    }

    public function testGetSelectionName()
    {
        $fieldIdentifier = 'some_field';
        $contentInfo = new ContentInfo(array('contentTypeId' => 12345));

        $versionInfo = new VersionInfo(array('contentInfo' => $contentInfo));

        $content = new Content(array('versionInfo' => $versionInfo));

        $selectionValue = new Value(array('some_name', 'some_name_2'));
        $field = new Field(array('value' => $selectionValue));

        $this->translationHelper->expects($this->once())
            ->method('getTranslatedField')
            ->with($content, $fieldIdentifier)
            ->willReturn($field);

        $fieldSettings = array(
            'options' => array(
                array(
                    'id' => 1,
                    'name' => 'Some name',
                    'identifier' => 'some_name',
                    'priority' => 1,
                ),
                array(
                    'id' => 2,
                    'name' => 'Some name 2',
                    'identifier' => 'some_name_2',
                    'priority' => 1,
                ),
            ),
        );

        $fieldDefinition = new FieldDefinition(
            array(
                'identifier' => $fieldIdentifier,
                'fieldSettings' => $fieldSettings,
            )
        );

        $contentType = new ContentType(array('fieldDefinitions' => array($fieldDefinition)));

        $this->contentTypeService->expects($this->once())
            ->method('loadContentType')
            ->with($content->contentInfo->contentTypeId)
            ->willReturn($contentType);

        $result = $this->runtime->getSelectionName($content, $fieldIdentifier);

        $this->assertInternalType('array', $result);

        $expectedResult = array(
            'some_name' => 'Some name',
            'some_name_2' => 'Some name 2',
        );

        $this->assertEquals($expectedResult, $result);
    }

    public function testGetSelectionNameBySpecifiedIdentifier()
    {
        $fieldIdentifier = 'some_field';
        $contentInfo = new ContentInfo(array('contentTypeId' => 12345));

        $versionInfo = new VersionInfo(array('contentInfo' => $contentInfo));

        $content = new Content(array('versionInfo' => $versionInfo));

        $fieldSettings = array(
            'options' => array(
                array(
                    'id' => 1,
                    'name' => 'Some name',
                    'identifier' => 'some_name',
                    'priority' => 1,
                ),
                array(
                    'id' => 2,
                    'name' => 'Some name 2',
                    'identifier' => 'some_name_2',
                    'priority' => 1,
                ),
            ),
        );

        $fieldDefinition = new FieldDefinition(
            array(
                'identifier' => $fieldIdentifier,
                'fieldSettings' => $fieldSettings,
            )
        );

        $contentType = new ContentType(array('fieldDefinitions' => array($fieldDefinition)));

        $this->contentTypeService->expects($this->once())
            ->method('loadContentType')
            ->with($content->contentInfo->contentTypeId)
            ->willReturn($contentType);

        $result = $this->runtime->getSelectionName($content, $fieldIdentifier, 'some_name');

        $this->assertInternalType('string', $result);

        $this->assertEquals('Some name', $result);
    }

    public function testGetSelectionNameForNonExistingOne()
    {
        $fieldIdentifier = 'some_field';
        $contentInfo = new ContentInfo(array('contentTypeId' => 12345));

        $versionInfo = new VersionInfo(array('contentInfo' => $contentInfo));

        $content = new Content(array('versionInfo' => $versionInfo));

        $fieldSettings = array(
            'options' => array(
                array(
                    'id' => 1,
                    'name' => 'Some name',
                    'identifier' => 'some_name',
                    'priority' => 1,
                ),
                array(
                    'id' => 2,
                    'name' => 'Some name 2',
                    'identifier' => 'some_name_2',
                    'priority' => 1,
                ),
            ),
        );

        $fieldDefinition = new FieldDefinition(
            array(
                'identifier' => $fieldIdentifier,
                'fieldSettings' => $fieldSettings,
            )
        );

        $contentType = new ContentType(array('fieldDefinitions' => array($fieldDefinition)));

        $this->contentTypeService->expects($this->once())
            ->method('loadContentType')
            ->with($content->contentInfo->contentTypeId)
            ->willReturn($contentType);

        $result = $this->runtime->getSelectionName($content, $fieldIdentifier, 'some_non_existent');

        $this->assertNull($result);
    }
}
