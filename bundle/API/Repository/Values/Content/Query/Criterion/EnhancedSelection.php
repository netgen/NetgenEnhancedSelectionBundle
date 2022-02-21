<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\API\Repository\Values\Content\Query\Criterion;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator;
use Ibexa\Contracts\Core\Repository\Values\Content\Query\Criterion\Operator\Specifications;

/**
 * A criterion that matches content based on identifier that is located in one of the fields.
 *
 * Supported operators:
 * - IN: matches against a list of identifiers (with OR operator)
 * - EQ: matches against one identifier
 */
final class EnhancedSelection extends Criterion
{
    public function getSpecifications(): array
    {
        return [
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
        ];
    }
}
