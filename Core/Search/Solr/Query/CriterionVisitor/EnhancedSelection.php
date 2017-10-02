<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Core\Search\Solr\Query\CriterionVisitor;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use EzSystems\EzPlatformSolrSearchEngine\Query\Common\CriterionVisitor\Field;
use EzSystems\EzPlatformSolrSearchEngine\Query\CriterionVisitor;
use Netgen\Bundle\EnhancedSelectionBundle\API\Repository\Values\Content\Query\Criterion\EnhancedSelection as EnhancedSelectionCriterion;

class EnhancedSelection extends Field
{
    /**
     * {@inheritdoc}
     */
    public function canVisit(Criterion $criterion)
    {
        return $criterion instanceof EnhancedSelectionCriterion;
    }

    /**
     * {@inheritdoc}
     */
    public function visit(Criterion $criterion, CriterionVisitor $subVisitor = null)
    {
        $searchFields = $this->getSearchFields($criterion);

        if (empty($searchFields)) {
            throw new InvalidArgumentException(
                '$criterion->target',
                "No searchable fields found for the given criterion target '{$criterion->target}'."
            );
        }

        $criterion->value = (array) $criterion->value;

        $queries = array();
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
