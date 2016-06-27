<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\Core\FieldType\EnhancedSelection\EnhancedSelectionStorage\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use eZ\Publish\Core\Persistence\Doctrine\ConnectionHandler;
use eZ\Publish\Core\Persistence\Doctrine\DeleteDoctrineQuery;
use eZ\Publish\Core\Persistence\Doctrine\InsertDoctrineQuery;
use eZ\Publish\Core\Persistence\Doctrine\SelectDoctrineQuery;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\EnhancedSelectionStorage\Gateway;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\EnhancedSelectionStorage\Gateway\LegacyStorage;
use eZ\Publish\Core\Persistence\Database\DatabaseHandler;

class LegacyStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LegacyStorage
     */
    private $storage;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $connection;

    public function setUp()
    {
        $this->connection = $this->getMockBuilder(ConnectionHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(array('createDeleteQuery', 'quoteColumn', 'createInsertQuery', 'createSelectQuery'))
            ->getMock();

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

    public function testDeleteFieldData()
    {
        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $query = $this->getMockBuilder(DeleteDoctrineQuery::class)
            ->setConstructorArgs(array($connection))
            ->setMethods(array('prepare'))
            ->getMock();

        $statement = $this->getMockBuilder(Statement::class)
            ->disableOriginalConstructor()
            ->setMethods(array('execute'))
            ->getMock();
        
        $query->expects($this->once())
            ->method('prepare')
            ->willReturn($statement);

        $statement->expects($this->once())
            ->method('execute');

        $this->connection->expects($this->once())
            ->method('createDeleteQuery')
            ->willReturn($query);

        $versionInfo = new VersionInfo();
        $fields = array(1, 2, 3);

        $this->storage->setConnection($this->connection);
        $this->storage->deleteFieldData($versionInfo, $fields);
    }

    public function testStoreFieldData()
    {
        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $query = $this->getMockBuilder(InsertDoctrineQuery::class)
            ->setConstructorArgs(array($connection))
            ->setMethods(array('prepare'))
            ->getMock();

        $statement = $this->getMockBuilder(Statement::class)
            ->disableOriginalConstructor()
            ->setMethods(array('execute'))
            ->getMock();

        $query->expects($this->once())
            ->method('prepare')
            ->willReturn($statement);

        $statement->expects($this->once())
            ->method('execute');

        $this->connection->expects($this->once())
            ->method('createInsertQuery')
            ->willReturn($query);

        $versionInfo = new VersionInfo();
        $field = new Field(
            array(
                'value' => new FieldValue(
                    array(
                        'externalData' => array(
                            'identifier'
                        )
                    )
                ),
            )
        );

        $this->storage->setConnection($this->connection);
        $this->storage->storeFieldData($versionInfo, $field);
    }

    public function testGetFieldData()
    {
        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $query = $this->getMockBuilder(SelectDoctrineQuery::class)
            ->setConstructorArgs(array($connection))
            ->setMethods(array('prepare'))
            ->getMock();

        $statement = $this->getMockBuilder(Statement::class)
            ->disableOriginalConstructor()
            ->setMethods(array('execute', 'fetchAll'))
            ->getMock();

        $query->expects($this->once())
            ->method('prepare')
            ->willReturn($statement);

        $statement->expects($this->once())
            ->method('execute');

        $result = array(
            array('identifier' => 'some_identifier')
        );
        $statement->expects($this->once())
            ->method('fetchAll')
            ->willReturn($result);

        $this->connection->expects($this->once())
            ->method('createSelectQuery')
            ->willReturn($query);

        $versionInfo = new VersionInfo();
        $field = new Field(
            array(
                'id' => 'some_id',
                'versionNo' => 'some_version',
                'value' => new FieldValue(
                    array(
                        'externalData' => array(
                            'identifier'
                        )
                    )
                ),
            )
        );

        $this->storage->setConnection($this->connection);
        $this->storage->getFieldData($versionInfo, $field);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetConnection()
    {
        $versionInfo = new VersionInfo();
        $fields = array(1, 2, 3);

        $this->storage->deleteFieldData($versionInfo, $fields);
    }
}
