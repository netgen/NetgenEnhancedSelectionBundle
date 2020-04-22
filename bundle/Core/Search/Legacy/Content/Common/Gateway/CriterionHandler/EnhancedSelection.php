<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\CriteriaConverter;
use eZ\Publish\Core\Search\Legacy\Content\Common\Gateway\CriterionHandler\FieldBase;
use Netgen\Bundle\EnhancedSelectionBundle\API\Repository\Values\Content\Query\Criterion\EnhancedSelection as EnhancedSelectionCriterion;

final class EnhancedSelection extends FieldBase
{
    public function accept(Criterion $criterion): bool
    {
        return $criterion instanceof EnhancedSelectionCriterion;
    }

    public function handle(CriteriaConverter $converter, QueryBuilder $queryBuilder, Criterion $criterion, array $languageSettings): string
    {
        $fieldDefinitionIds = $this->getFieldDefinitionIds($criterion->target);

        $subSelect = $this->connection->createQueryBuilder();
        $subSelect
            ->select('t1.contentobject_id')
            ->from('ezcontentobject_attribute', 't1')
            ->innerJoin(
                't1',
                'sckenhancedselection',
                't2',
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('t2.contentobject_attribute_version', 't1.version'),
                    $queryBuilder->expr()->eq('t2.contentobject_attribute_id', 't1.id')
                )
            )
            ->where(
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('t1.version', 'c.current_version'),
                    $subSelect->expr()->in(
                        't1.contentclassattribute_id',
                        $queryBuilder->createNamedParameter($fieldDefinitionIds, Connection::PARAM_INT_ARRAY)
                    ),
                    $subSelect->expr()->in(
                        't2.identifier',
                        $queryBuilder->createNamedParameter($criterion->value, Connection::PARAM_STR_ARRAY)
                    )
                )
            );

        return $queryBuilder->expr()->in('c.id', $subSelect->getSQL());
    }

    private function getFieldDefinitionIds(string $fieldIdentifier): array
    {
        $fieldDefinitionIdList = [];
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
