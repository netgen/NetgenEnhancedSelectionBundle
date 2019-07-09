<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\API\Repository\Values\Content\Query\Criterion;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use eZ\Publish\API\Repository\Values\Content\Query\CriterionInterface;
use Netgen\Bundle\EnhancedSelectionBundle\API\Repository\Values\Content\Query\Criterion\EnhancedSelection;
use PHPUnit\Framework\TestCase;

class EnhancedSelectionTest extends TestCase
{
    /**
     * @var EnhancedSelection
     */
    protected $criterion;

    protected function setUp(): void
    {
        $this->criterion = new EnhancedSelection('some_field', Operator::EQ, 'some_value');
    }

    public function testInstanceOfCriterionInterface()
    {
        self::assertInstanceOf(CriterionInterface::class, $this->criterion);
    }

    public function testCreateFromQueryBuilderMethod()
    {
        self::assertSame($this->criterion, EnhancedSelection::createFromQueryBuilder('some_field', Operator::EQ, 'some_value'));
    }
}
