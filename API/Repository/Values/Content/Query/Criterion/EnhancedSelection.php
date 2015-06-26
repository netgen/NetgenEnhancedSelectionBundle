<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\API\Repository\Values\Content\Query\Criterion;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator\Specifications;
use eZ\Publish\API\Repository\Values\Content\Query\CriterionInterface;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;

/**
 * A criterion that matches content based on identifier that is located in one of the fields
 *
 * Supported operators:
 * - IN: matches against a list of identifiers (with OR operator)
 * - EQ: matches against one identifier
 */
class EnhancedSelection extends Criterion implements CriterionInterface
{
    /**
     * Creates a new EnhancedSelection criterion
     *
     * @param string $target Field definition identifier
     * @param string|null $operator
     *        The operator the Criterion uses. If null is given, will default to Operator::IN if $value is an array,
     *        Operator::EQ if it is not.
     * @param string|string[] $value One or more identifiers that must be matched
     *
     * @throws \InvalidArgumentException if a non string identifier is given
     * @throws \InvalidArgumentException if the value type doesn't match the operator
     */
    public function __construct( $target, $operator, $value )
    {
        parent::__construct( $target, $operator, $value );
    }

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

    public static function createFromQueryBuilder( $target, $operator, $value )
    {
        return new self( $target, $operator, $value );
    }
}
