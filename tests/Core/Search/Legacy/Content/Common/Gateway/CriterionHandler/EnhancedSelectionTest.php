<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Statement;
use eZ\Publish\API\Repository\Exceptions\InvalidArgumentException;
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

    protected function setUp(): void
    {
        self::markTestIncomplete('Need to switch the tests to new criterion visitor namespaces');

        $this->db = $this->getMockBuilder(ConnectionHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(['createSelectQuery', 'quoteColumn'])
            ->getMock();

        $this->handler = new EnhancedSelection($this->db);
        $this->criterion = new EnhancedSelectionCriterion('some_field', Operator::EQ, 'some_value');
    }

    public function testInstanceOfCriterionHandler()
    {
        self::assertInstanceOf(CriterionHandler::class, $this->handler);
    }

    public function testAccept()
    {
        self::assertTrue($this->handler->accept($this->criterion));
        $criterion = $this->getMockBuilder(Criterion::class)
            ->disableOriginalConstructor()
            ->getMock();
        self::assertFalse($this->handler->accept($criterion));
    }

    public function testHandleWithoutFieldDefinitions()
    {
        $this->expectException(InvalidArgumentException::class);

        $criteriaConverter = $this->getMockBuilder(CriteriaConverter::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $query = $this->getMockBuilder(SelectDoctrineQuery::class)
            ->setConstructorArgs([$connection])
            ->setMethods(['prepare'])
            ->getMock();

        $statement = $this->getMockBuilder(Statement::class)
            ->disableOriginalConstructor()
            ->setMethods(['execute', 'fetchAll'])
            ->getMock();

        $statement->expects(self::once())
            ->method('execute');

        $statement->expects(self::once())
            ->method('fetchAll')
            ->willReturn([]);

        $query->expects(self::once())
            ->method('prepare')
            ->willReturn($statement);

        $this->db->expects(self::once())
            ->method('createSelectQuery')
            ->willReturn($query);

        $this->handler->handle($criteriaConverter, $query, $this->criterion);
    }

    public function testHandle()
    {
        $criteriaConverter = $this->getMockBuilder(CriteriaConverter::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $query = $this->getMockBuilder(SelectDoctrineQuery::class)
            ->setConstructorArgs([$connection])
            ->setMethods(['prepare'])
            ->getMock();

        $statement = $this->getMockBuilder(Statement::class)
            ->disableOriginalConstructor()
            ->setMethods(['execute', 'fetchAll'])
            ->getMock();

        $statement->expects(self::once())
            ->method('execute');

        $statement->expects(self::once())
            ->method('fetchAll')
            ->willReturn([1, 2, 3]);

        $query->expects(self::once())
            ->method('prepare')
            ->willReturn($statement);

        $this->db->expects(self::once())
            ->method('createSelectQuery')
            ->willReturn($query);

        $this->handler->handle($criteriaConverter, $query, $this->criterion);
    }
}
