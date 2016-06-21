<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\API\Repository\Values\Content\Query\Criterion;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use eZ\Publish\API\Repository\Values\Content\Query\CriterionInterface;
use Netgen\Bundle\EnhancedSelectionBundle\API\Repository\Values\Content\Query\Criterion\EnhancedSelection;

class EnhancedSelectionTest extends \PHPUnit_Framework_TestCase
{
    public function testInstanceOfCriterionInterface()
    {
        $this->assertInstanceOf(CriterionInterface::class, new EnhancedSelection('some_field', Operator::EQ, 'some_value'));
    }
}
