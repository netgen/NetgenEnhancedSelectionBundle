<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Core\Persistence\Legacy\Content\Search\Common\Gateway\CriterionHandler;

use eZ\Publish\Core\Persistence\Legacy\Content\Search\Common\Gateway\CriterionHandler;
use eZ\Publish\Core\Persistence\Legacy\Content\Search\Common\Gateway\CriteriaConverter;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use Netgen\Bundle\EnhancedSelectionBundle\API\Repository\Values\Content\Query\Criterion\EnhancedSelection as EnhancedSelectionCriterion;
use eZ\Publish\Core\Persistence\Database\SelectQuery;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;

/**
 * EnhancedSelection criterion handler.
 */
class EnhancedSelection extends CriterionHandler
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
     * @param \eZ\Publish\Core\Persistence\Legacy\Content\Search\Common\Gateway\CriteriaConverter $converter
     * @param \eZ\Publish\Core\Persistence\Database\SelectQuery $query
     * @param \eZ\Publish\API\Repository\Values\Content\Query\Criterion $criterion
     *
     * @return \eZ\Publish\Core\Persistence\Database\Expression
     */
    public function handle(CriteriaConverter $converter, SelectQuery $query, Criterion $criterion)
    {
        $this->checkSearchableFields($criterion->target);

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
     * Checks if there are searchable fields for the Criterion.
     *
     * @throws \eZ\Publish\Core\Base\Exceptions\InvalidArgumentException If no searchable fields are found for the given $fieldIdentifier.
     *
     * @param string $fieldIdentifier
     */
    protected function checkSearchableFields($fieldIdentifier)
    {
        $query = $this->dbHandler->createSelectQuery();
        $query
            ->select($this->dbHandler->quoteColumn('id', 'ezcontentclass_attribute'))
            ->from($this->dbHandler->quoteTable('ezcontentclass_attribute'))
            ->where(
                $query->expr->lAnd(
                    $query->expr->eq(
                        $this->dbHandler->quoteColumn('is_searchable', 'ezcontentclass_attribute'),
                        $query->bindValue(1, null, \PDO::PARAM_INT)
                    ),
                    $query->expr->eq(
                        $this->dbHandler->quoteColumn('data_type_string', 'ezcontentclass_attribute'),
                        $query->bindValue('sckenhancedselection')
                    ),
                    $query->expr->eq(
                        $this->dbHandler->quoteColumn('identifier', 'ezcontentclass_attribute'),
                        $query->bindValue($fieldIdentifier)
                    )
                )
            );

        $statement = $query->prepare();
        $statement->execute();
        $fieldDefinitionIds = $statement->fetchAll(\PDO::FETCH_COLUMN);

        if (empty($fieldDefinitionIds)) {
            throw new InvalidArgumentException(
                '$criterion->target',
                "No searchable fields found for the given criterion target '{$fieldIdentifier}'."
            );
        }
    }
}
