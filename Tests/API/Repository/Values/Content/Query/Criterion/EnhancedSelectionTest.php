<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\API\Repository\Values\Content\Query\Criterion;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use eZ\Publish\API\Repository\Values\Content\Query\CriterionInterface;
use Netgen\Bundle\EnhancedSelectionBundle\API\Repository\Values\Content\Query\Criterion\EnhancedSelection;

class EnhancedSelectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var EnhancedSelection
     */
    private $criterion;

    public function setUp()
    {
        $this->criterion = new EnhancedSelection('some_field', Operator::EQ, 'some_value');
    }

    public function testInstanceOfCriterionInterface()
    {
        $this->assertInstanceOf(CriterionInterface::class, $this->criterion);
    }

    public function testCreateFromQueryBuilderMethod()
    {
        $this->assertEquals($this->criterion, EnhancedSelection::createFromQueryBuilder('some_field', Operator::EQ, 'some_value'));
    }
}
