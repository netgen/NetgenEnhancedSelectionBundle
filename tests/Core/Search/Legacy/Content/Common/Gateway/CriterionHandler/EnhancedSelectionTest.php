<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use eZ\Publish\Core\Persistence\Doctrine\ConnectionHandler;
use eZ\Publish\Core\Persistence\Doctrine\SelectDoctrineQuery;
use Netgen\Bundle\EnhancedSelectionBundle\API\Repository\Values\Content\Query\Criterion\EnhancedSelection as EnhancedSelectionCriterion;
use Netgen\Bundle\EnhancedSelectionBundle\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\EnhancedSelection;
use PHPUnit\Framework\TestCase;

class EnhancedSelectionTest extends TestCase
{
    /**
     * @var EnhancedSelection
     */
    protected $handler;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $db;

    /**
     * @var EnhancedSelectionCriterion
     */
    protected $criterion;

    public function setUp()
    {
        $this->markTestIncomplete('Need to switch the tests to new criterion visitor namespaces');

        $this->db = $this->getMockBuilder(ConnectionHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(array('createSelectQuery', 'quoteColumn'))
            ->getMock();

        $this->handler = new EnhancedSelection($this->db);
        $this->criterion = new EnhancedSelectionCriterion('some_field', Operator::EQ, 'some_value');
    }

    public function testInstanceOfCriterionHandler()
    {
        $this->assertInstanceOf(CriterionHandler::class, $this->handler);
    }

    public function testAccept()
    {
        $this->assertTrue($this->handler->accept($this->criterion));
        $criterion = $this->getMockBuilder(Criterion::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->assertFalse($this->handler->accept($criterion));
    }

    /**
     * @expectedException \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException
     */
    public function testHandleWithoutFieldDefinitions()
    {
        $criteriaConverter = $this->getMockBuilder(CriteriaConverter::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

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

        $statement->expects($this->once())
            ->method('execute');

        $statement->expects($this->once())
            ->method('fetchAll')
            ->willReturn(array());

        $query->expects($this->once())
            ->method('prepare')
            ->willReturn($statement);

        $this->db->expects($this->once())
            ->method('createSelectQuery')
            ->willReturn($query);

        $this->handler->handle($criteriaConverter, $query, $this->criterion);
    }

    public function testHandle()
    {
        $criteriaConverter = $this->getMockBuilder(CriteriaConverter::class)
            ->disableOriginalConstructor()
            ->setMethods(array())
            ->getMock();

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

        $statement->expects($this->once())
            ->method('execute');

        $statement->expects($this->once())
            ->method('fetchAll')
            ->willReturn(array(1, 2, 3));

        $query->expects($this->once())
            ->method('prepare')
            ->willReturn($statement);

        $this->db->expects($this->once())
            ->method('createSelectQuery')
            ->willReturn($query);

        $this->handler->handle($criteriaConverter, $query, $this->criterion);
    }
}
