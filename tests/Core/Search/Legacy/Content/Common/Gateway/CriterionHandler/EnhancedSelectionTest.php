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

final class EnhancedSelectionTest extends TestCase
{
    /**
     * @var EnhancedSelection
     */
    private $handler;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    private $db;

    /**
     * @var EnhancedSelectionCriterion
     */
    private $criterion;

    protected function setUp(): void
    {
        self::markTestIncomplete('Need to switch the tests to new criterion visitor namespaces');

        $this->db = $this->getMockBuilder(ConnectionHandler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['createSelectQuery', 'quoteColumn'])
            ->getMock();

        $this->handler = new EnhancedSelection($this->db);
        $this->criterion = new EnhancedSelectionCriterion('some_field', Operator::EQ, 'some_value');
    }

    public function testInstanceOfCriterionHandler(): void
    {
        self::assertInstanceOf(CriterionHandler::class, $this->handler);
    }

    public function testAccept(): void
    {
        self::assertTrue($this->handler->accept($this->criterion));
        $criterion = $this->getMockBuilder(Criterion::class)
            ->disableOriginalConstructor()
            ->getMock();
        self::assertFalse($this->handler->accept($criterion));
    }

    public function testHandleWithoutFieldDefinitions(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $criteriaConverter = $this->getMockBuilder(CriteriaConverter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $query = $this->getMockBuilder(SelectDoctrineQuery::class)
            ->setConstructorArgs([$connection])
            ->onlyMethods(['prepare'])
            ->getMock();

        $statement = $this->getMockBuilder(Statement::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['execute', 'fetchAll'])
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

    public function testHandle(): void
    {
        $criteriaConverter = $this->getMockBuilder(CriteriaConverter::class)
            ->disableOriginalConstructor()
            ->getMock();

        $connection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $query = $this->getMockBuilder(SelectDoctrineQuery::class)
            ->setConstructorArgs([$connection])
            ->onlyMethods(['prepare'])
            ->getMock();

        $statement = $this->getMockBuilder(Statement::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['execute', 'fetchAll'])
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
