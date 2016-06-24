<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\Core\FieldType\EnhancedSelection\EnhancedSelectionStorage\Gateway;

use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\EnhancedSelectionStorage\Gateway;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\EnhancedSelectionStorage\Gateway\LegacyStorage;
use eZ\Publish\Core\Persistence\Database\DatabaseHandler;

class LegacyStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LegacyStorage
     */
    private $storage;

    public function setUp()
    {
        $this->storage = new LegacyStorage();
    }

    public function testInstanceOfGateway()
    {
        $this->assertInstanceOf(Gateway::class, $this->storage);
    }

    public function testConnectionHandling()
    {
        $handler = $this->getMockForAbstractClass(DatabaseHandler::class);

        $this->storage->setConnection($handler);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Invalid connection passed
     */
    public function testConnectionHandlingWithInvalidConnection()
    {
        $handler = new \stdClass();

        $this->storage->setConnection($handler);
    }
}
