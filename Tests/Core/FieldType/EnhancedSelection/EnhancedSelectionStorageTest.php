<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\Core\FieldType\EnhancedSelection;

use eZ\Publish\Core\FieldType\StorageGateway;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\EnhancedSelectionStorage;
use PHPUnit\Framework\TestCase;

class EnhancedSelectionStorageTest extends TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $gateway;

    /**
     * @var EnhancedSelectionStorage
     */
    protected $storage;

    public function setUp()
    {
        $this->gateway = $this->getMockBuilder(EnhancedSelectionStorage\Gateway\DoctrineStorage::class)
            ->disableOriginalConstructor()
            ->setMethods(array('deleteFieldData', 'storeFieldData', 'getFieldData'))
            ->getMock();

        $this->storage = new EnhancedSelectionStorage($this->gateway);
    }

    public function testHasFieldData()
    {
        $this->assertTrue($this->storage->hasFieldData());
    }

    public function testGetIndexData()
    {
        $versionInfo = $this->getMockBuilder(VersionInfo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $field = $this->getMockBuilder(Field::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertFalse($this->storage->getIndexData($versionInfo, $field, array()));
    }

    public function testStoreFieldData()
    {
        $versionInfo = new VersionInfo();
        $field = new Field(
            array(
                'id' => 'some_id',
                'value' => new FieldValue(
                    array(
                        'externalData' => 'some_data',
                    )
                ),
            )
        );

        $connection = $this->getMockForAbstractClass(StorageGateway::class);
        $context = array('identifier' => 'enhancedselection', 'connection' => $connection);

        $this->gateway->expects($this->once())
            ->method('deleteFieldData');

        $this->gateway->expects($this->once())
            ->method('storeFieldData');

        $this->storage->storeFieldData($versionInfo, $field, $context);
    }

    public function testGetFieldData()
    {
        $versionInfo = new VersionInfo();
        $field = new Field(
            array(
                'id' => 'some_id',
                'value' => new FieldValue(
                    array(
                        'externalData' => 'some_data',
                    )
                ),
            )
        );

        $connection = $this->getMockForAbstractClass(StorageGateway::class);
        $context = array('identifier' => 'enhancedselection', 'connection' => $connection);

        $this->gateway->expects($this->once())
            ->method('getFieldData');

        $this->storage->getFieldData($versionInfo, $field, $context);
    }

    public function testDeleteFieldData()
    {
        $versionInfo = new VersionInfo();
        $fields = array('some_field');

        $connection = $this->getMockForAbstractClass(StorageGateway::class);
        $context = array('identifier' => 'enhancedselection', 'connection' => $connection);

        $this->gateway->expects($this->once())
            ->method('deleteFieldData');

        $this->storage->deleteFieldData($versionInfo, $fields, $context);
    }
}
