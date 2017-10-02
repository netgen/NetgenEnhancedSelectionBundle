<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;

use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Persistence\Database\SelectQuery;
use eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldBase;
use Netgen\Bundle\EnhancedSelectionBundle\API\Repository\Values\Content\Query\Criterion\EnhancedSelection as EnhancedSelectionCriterion;

/**
 * EnhancedSelection criterion handler.
 */
class EnhancedSelection extends FieldBase
{
    /**
     * Check if this criterion handler accepts to handle the given criterion.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterion
     *
     * @return bool
     */
    public function accept(Criterion $criterion)
    {
        return $criterion instanceof EnhancedSelectionCriterion;
    }

    /**
     * Generate query expression for a Criterion this handler accepts.
     *
     * accept() must be called before calling this method.
     *
     * @param \eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter $converter
     * @param \eZ\Publish\Core\Persistence\Database\SelectQuery $query
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterion
     * @param array $fieldFilters
     *
     * @return \eZ\Publish\Core\Persistence\Database\Expression
     */
    public function handle(CriteriaConverter $converter, SelectQuery $query, Criterion $criterion, array $fieldFilters = null)
    {
        $fieldDefinitionIds = $this->getFieldDefinitionIds($criterion->target);

        $subSelect = $query->subSelect();
        $subSelect
            ->select($this->dbHandler->quoteColumn('contentobject_id'))
            ->from($this->dbHandler->quoteTable('ezcontentobject_attribute'))
            ->innerJoin(
                $this->dbHandler->quoteTable('sckenhancedselection'),
                $subSelect->expr->lAnd(
                    array(
                        $subSelect->expr->eq(
                            $this->dbHandler->quoteColumn('contentobject_attribute_version', 'sckenhancedselection'),
                            $this->dbHandler->quoteColumn('version', 'ezcontentobject_attribute')
                        ),
                        $subSelect->expr->eq(
                            $this->dbHandler->quoteColumn('contentobject_attribute_id', 'sckenhancedselection'),
                            $this->dbHandler->quoteColumn('id', 'ezcontentobject_attribute')
                        ),
                    )
                )
            )
            ->where(
                $subSelect->expr->lAnd(
                    $subSelect->expr->eq(
                        $this->dbHandler->quoteColumn('version', 'ezcontentobject_attribute'),
                        $this->dbHandler->quoteColumn('current_version', 'ezcontentobject')
                    ),
                    $subSelect->expr->in(
                        $this->dbHandler->quoteColumn('contentclassattribute_id', 'ezcontentobject_attribute'),
                        $fieldDefinitionIds
                    ),
                    $subSelect->expr->in(
                        $this->dbHandler->quoteColumn('identifier', 'sckenhancedselection'),
                        $criterion->value
                    )
                )
            );

        return $query->expr->in(
            $this->dbHandler->quoteColumn('id', 'ezcontentobject'),
            $subSelect
        );
    }

    /**
     * Returns a list of IDs of searchable field definitions for the given criterion target.
     *
     *
     * @param string $fieldIdentifier
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException If no searchable fields are found for the given $fieldIdentifier
     *
     * @return array
     */
    protected function getFieldDefinitionIds($fieldIdentifier)
    {
        $fieldDefinitionIdList = array();
        $fieldMap = $this->contentTypeHandler->getSearchableFieldMap();

        foreach ($fieldMap as $contentTypeIdentifier => $fieldIdentifierMap) {
            // First check if field exists in the current content type, there is nothing to do if it doesn't
            if (
                !(
                    isset($fieldIdentifierMap[$fieldIdentifier])
                    && $fieldIdentifierMap[$fieldIdentifier]['field_type_identifier'] === 'sckenhancedselection'
                )
            ) {
                continue;
            }

            $fieldDefinitionIdList[] = $fieldIdentifierMap[$fieldIdentifier]['field_definition_id'];
        }

        if (empty($fieldDefinitionIdList)) {
            throw new InvalidArgumentException(
                '$criterion->target',
                "No searchable fields found for the given criterion target '{$fieldIdentifier}'."
            );
        }

        return $fieldDefinitionIdList;
    }
}
