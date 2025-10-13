<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\Core\FieldType\EnhancedSelection;

use Ibexa\Contracts\Core\FieldType\StorageGateway;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\FieldValue;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\EnhancedSelectionStorage;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class EnhancedSelectionStorageTest extends TestCase
{
    private MockObject $gateway;

    private EnhancedSelectionStorage $storage;

    protected function setUp(): void
    {
        $this->gateway = $this->getMockBuilder(EnhancedSelectionStorage\Gateway::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['deleteFieldData', 'storeFieldData', 'getFieldData'])
            ->getMock();

        $this->storage = new EnhancedSelectionStorage($this->gateway);
    }

    public function testHasFieldData(): void
    {
        self::assertTrue($this->storage->hasFieldData());
    }

    public function testGetIndexData(): void
    {
        $versionInfo = $this->getMockBuilder(VersionInfo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $field = $this->getMockBuilder(Field::class)
            ->disableOriginalConstructor()
            ->getMock();

        self::assertFalse($this->storage->getIndexData($versionInfo, $field));
    }

    public function testStoreFieldData(): void
    {
        $versionInfo = new VersionInfo();
        $field = new Field(
            [
                'id' => 'some_id',
                'value' => new FieldValue(
                    [
                        'externalData' => 'some_data',
                    ]
                ),
            ]
        );

        $connection = $this->getMockForAbstractClass(StorageGateway::class);
        $context = ['identifier' => 'enhancedselection', 'connection' => $connection];

        $this->gateway->expects(self::once())
            ->method('deleteFieldData');

        $this->gateway->expects(self::once())
            ->method('storeFieldData');

        $this->storage->storeFieldData($versionInfo, $field, $context);
    }

    public function testGetFieldData(): void
    {
        $versionInfo = new VersionInfo();
        $field = new Field(
            [
                'id' => 'some_id',
                'value' => new FieldValue(
                    [
                        'externalData' => 'some_data',
                    ]
                ),
            ]
        );

        $connection = $this->getMockForAbstractClass(StorageGateway::class);
        $context = ['identifier' => 'enhancedselection', 'connection' => $connection];

        $this->gateway->expects(self::once())
            ->method('getFieldData');

        $this->storage->getFieldData($versionInfo, $field, $context);
    }

    public function testDeleteFieldData(): void
    {
        $versionInfo = new VersionInfo();
        $fields = ['some_field'];

        $connection = $this->getMockForAbstractClass(StorageGateway::class);
        $context = ['identifier' => 'enhancedselection', 'connection' => $connection];

        $this->gateway->expects(self::once())
            ->method('deleteFieldData');

        $this->storage->deleteFieldData($versionInfo, $fields, $context);
    }
}
