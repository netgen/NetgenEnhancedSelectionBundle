<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\API\Repository\Values\Content\Query\Criterion;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator\Specifications;
use eZ\Publish\API\Repository\Values\Content\Query\CriterionInterface;

/**
 * A criterion that matches content based on identifier that is located in one of the fields.
 *
 * Supported operators:
 * - IN: matches against a list of identifiers (with OR operator)
 * - EQ: matches against one identifier
 */
class EnhancedSelection extends Criterion implements CriterionInterface
{
    public function getSpecifications()
    {
        return array(
            new Specifications(
                Operator::IN,
                Specifications::FORMAT_ARRAY,
                Specifications::TYPE_STRING
            ),
            new Specifications(
                Operator::EQ,
                Specifications::FORMAT_SINGLE,
                Specifications::TYPE_STRING
            ),
        );
    }

    public static function createFromQueryBuilder($target, $operator, $value)
    {
        return new self($target, $operator, $value);
    }
}
