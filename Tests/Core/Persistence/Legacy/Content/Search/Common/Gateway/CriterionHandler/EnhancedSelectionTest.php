<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\Core\Persistence\Legacy\Content\Search\Common\Gateway\CriterionHandler;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use eZ\Publish\Core\Persistence\Database\DatabaseHandler;
use eZ\Publish\Core\Persistence\Legacy\Content\Search\Common\Gateway\CriterionHandler;
use Netgen\Bundle\EnhancedSelectionBundle\Core\Persistence\Legacy\Content\Search\Common\Gateway\CriterionHandler\EnhancedSelection;
use Netgen\Bundle\EnhancedSelectionBundle\API\Repository\Values\Content\Query\Criterion\EnhancedSelection as EnhancedSelectionCriterion;

class EnhancedSelectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EnhancedSelection
     */
    private $handler;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $db;

    public function setUp()
    {
        $this->db = $this->getMockForAbstractClass(DatabaseHandler::class);
        $this->handler = new EnhancedSelection($this->db);
    }

    public function testInstanceOfCriterionHandler()
    {
        $this->assertInstanceOf(CriterionHandler::class, $this->handler);
    }

    public function testAccept()
    {
        $criterion = new EnhancedSelectionCriterion('some_field', Operator::EQ, 'some_value');
        $this->assertTrue($this->handler->accept($criterion));
        $criterion = $this->getMockBuilder(Criterion::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->assertFalse($this->handler->accept($criterion));
    }
}
