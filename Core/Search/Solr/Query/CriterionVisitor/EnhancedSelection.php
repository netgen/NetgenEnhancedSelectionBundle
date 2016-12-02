<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Core\Search\Solr\Query\CriterionVisitor;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use EzSystems\EzPlatformSolrSearchEngine\Query\CriterionVisitor;
use Netgen\Bundle\EnhancedSelectionBundle\API\Repository\Values\Content\Query\Criterion\EnhancedSelection as EnhancedSelectionCriterion;
use EzSystems\EzPlatformSolrSearchEngine\Query\Content\CriterionVisitor\Field;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion\Operator;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;

class EnhancedSelection extends Field
{
    /**
     * @inheritDoc
     */
    public function canVisit(Criterion $criterion)
    {
        return
            $criterion instanceof EnhancedSelectionCriterion &&
            (($criterion->operator ?: Operator::IN) === Operator::IN
                || $criterion->operator === Operator::CONTAINS
                || $criterion->operator === Operator::EQ);
    }

    /**
     * @inheritDoc
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

        $criterion->value = (array)$criterion->value;

        $queries = array();
        foreach ($searchFields as $name => $fieldType) {
            foreach ($criterion->value as $value) {
                $preparedValues = (array)$this->mapSearchFieldvalue($value, $fieldType);

                foreach ($preparedValues as $prepValue) {
                    $queries[] = $name . ':"' . $this->escapeQuote($this->toString($prepValue), true) . '"';
                }
            }
        }

        switch ($criterion->operator) {
            case Operator::CONTAINS:
                $op = ' AND ';
                break;
            case Operator::IN:
            case Operator::EQ:
            default:
                $op = ' OR ';
        }

        return '(' . implode($op, $queries) . ')';
    }
}