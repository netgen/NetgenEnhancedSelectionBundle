<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\Core\FieldType\EnhancedSelection;

use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\EnhancedSelectionStorage;

class EnhancedSelectionStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EnhancedSelectionStorage
     */
    private $storage;
    
    public function setUp() 
    {
        $this->storage = new EnhancedSelectionStorage();
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
}
