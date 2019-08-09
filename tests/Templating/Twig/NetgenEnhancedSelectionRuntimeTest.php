<?php

declare(strict_types=1);

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

final class NetgenEnhancedSelectionRuntimeTest extends TestCase
{
    /**
     * @var NetgenEnhancedSelectionRuntime
     */
    private $runtime;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $translationHelper;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $contentTypeService;

    protected function setUp(): void
    {
        $this->translationHelper = $this->getMockBuilder(TranslationHelper::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getTranslatedField'])
            ->getMock();

        $this->contentTypeService = $this->getMockBuilder(ContentTypeService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['loadContentType'])
            ->getMockForAbstractClass();

        $this->runtime = new NetgenEnhancedSelectionRuntime($this->contentTypeService, $this->translationHelper);
    }

    public function testInstanceOfTwigExtension(): void
    {
        self::assertInstanceOf(NetgenEnhancedSelectionRuntime::class, $this->runtime);
    }

    public function testGetSelectionName(): void
    {
        $fieldIdentifier = 'some_field';
        $contentInfo = new ContentInfo(['contentTypeId' => 12345]);

        $versionInfo = new VersionInfo(['contentInfo' => $contentInfo]);

        $content = new Content(['versionInfo' => $versionInfo]);

        $selectionValue = new Value(['some_name', 'some_name_2']);
        $field = new Field(['value' => $selectionValue]);

        $this->translationHelper->expects(self::once())
            ->method('getTranslatedField')
            ->with($content, $fieldIdentifier)
            ->willReturn($field);

        $fieldSettings = [
            'options' => [
                [
                    'id' => 1,
                    'name' => 'Some name',
                    'identifier' => 'some_name',
                    'priority' => 1,
                ],
                [
                    'id' => 2,
                    'name' => 'Some name 2',
                    'identifier' => 'some_name_2',
                    'priority' => 1,
                ],
            ],
        ];

        $fieldDefinition = new FieldDefinition(
            [
                'identifier' => $fieldIdentifier,
                'fieldSettings' => $fieldSettings,
            ]
        );

        $contentType = new ContentType(['fieldDefinitions' => [$fieldDefinition]]);

        $this->contentTypeService->expects(self::once())
            ->method('loadContentType')
            ->with($content->contentInfo->contentTypeId)
            ->willReturn($contentType);

        $result = $this->runtime->getSelectionName($content, $fieldIdentifier);

        self::assertIsArray($result);

        $expectedResult = [
            'some_name' => 'Some name',
            'some_name_2' => 'Some name 2',
        ];

        self::assertSame($expectedResult, $result);
    }

    public function testGetSelectionNameBySpecifiedIdentifier(): void
    {
        $fieldIdentifier = 'some_field';
        $contentInfo = new ContentInfo(['contentTypeId' => 12345]);

        $versionInfo = new VersionInfo(['contentInfo' => $contentInfo]);

        $content = new Content(['versionInfo' => $versionInfo]);

        $fieldSettings = [
            'options' => [
                [
                    'id' => 1,
                    'name' => 'Some name',
                    'identifier' => 'some_name',
                    'priority' => 1,
                ],
                [
                    'id' => 2,
                    'name' => 'Some name 2',
                    'identifier' => 'some_name_2',
                    'priority' => 1,
                ],
            ],
        ];

        $fieldDefinition = new FieldDefinition(
            [
                'identifier' => $fieldIdentifier,
                'fieldSettings' => $fieldSettings,
            ]
        );

        $contentType = new ContentType(['fieldDefinitions' => [$fieldDefinition]]);

        $this->contentTypeService->expects(self::once())
            ->method('loadContentType')
            ->with($content->contentInfo->contentTypeId)
            ->willReturn($contentType);

        $result = $this->runtime->getSelectionName($content, $fieldIdentifier, 'some_name');

        self::assertIsString($result);

        self::assertSame('Some name', $result);
    }

    public function testGetSelectionNameForNonExistingOne(): void
    {
        $fieldIdentifier = 'some_field';
        $contentInfo = new ContentInfo(['contentTypeId' => 12345]);

        $versionInfo = new VersionInfo(['contentInfo' => $contentInfo]);

        $content = new Content(['versionInfo' => $versionInfo]);

        $fieldSettings = [
            'options' => [
                [
                    'id' => 1,
                    'name' => 'Some name',
                    'identifier' => 'some_name',
                    'priority' => 1,
                ],
                [
                    'id' => 2,
                    'name' => 'Some name 2',
                    'identifier' => 'some_name_2',
                    'priority' => 1,
                ],
            ],
        ];

        $fieldDefinition = new FieldDefinition(
            [
                'identifier' => $fieldIdentifier,
                'fieldSettings' => $fieldSettings,
            ]
        );

        $contentType = new ContentType(['fieldDefinitions' => [$fieldDefinition]]);

        $this->contentTypeService->expects(self::once())
            ->method('loadContentType')
            ->with($content->contentInfo->contentTypeId)
            ->willReturn($contentType);

        $result = $this->runtime->getSelectionName($content, $fieldIdentifier, 'some_non_existent');

        self::assertNull($result);
    }
}
