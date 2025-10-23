<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Core\Search\Solr\Query\CriterionVisitor;

use Ibexa\Contracts\Core\Repository\Values\Content\Query\CriterionInterface;
use Ibexa\Contracts\Solr\Query\CriterionVisitor;
use Ibexa\Core\Base\Exceptions\InvalidArgumentException;
use Ibexa\Solr\Query\Common\CriterionVisitor\Field;
use Netgen\Bundle\EnhancedSelectionBundle\API\Repository\Values\Content\Query\Criterion\EnhancedSelection as EnhancedSelectionCriterion;

use function implode;

final class EnhancedSelection extends Field
{
    public function canVisit(CriterionInterface $criterion): bool
    {
        return $criterion instanceof EnhancedSelectionCriterion;
    }

    /**
     * @param \Netgen\Bundle\EnhancedSelectionBundle\API\Repository\Values\Content\Query\Criterion\EnhancedSelection $criterion
     *
     * @throws \Ibexa\Core\Base\Exceptions\InvalidArgumentException
     */
    public function visit(CriterionInterface $criterion, ?CriterionVisitor $subVisitor = null): string
    {
        $searchFields = $this->getSearchFields($criterion);

        if (empty($searchFields)) {
            throw new InvalidArgumentException(
                '$criterion->target',
                "No searchable fields found for the given criterion target '{$criterion->target}'."
            );
        }

        $criterion->value = (array) $criterion->value;

        $queries = [];
        foreach ($searchFields as $name => $fieldType) {
            foreach ($criterion->value as $value) {
                $preparedValues = (array) $this->mapSearchFieldvalue($value, $fieldType);

                foreach ($preparedValues as $prepValue) {
                    $queries[] = $name . ':"' . $this->escapeQuote($this->toString($prepValue), true) . '"';
                }
            }
        }

        return '(' . implode(' OR ', $queries) . ')';
    }
}
