<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\EnhancedSelectionStorage\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\EnhancedSelectionStorage\Gateway;

use function array_map;

final class DoctrineStorage extends Gateway
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function storeFieldData(VersionInfo $versionInfo, Field $field): void
    {
        foreach ($field->value->externalData as $identifier) {
            $insertQuery = $this->connection->createQueryBuilder();
            $insertQuery
                ->insert($this->connection->quoteIdentifier('sckenhancedselection'))
                ->values(
                    [
                        'contentobject_attribute_id' => ':contentobject_attribute_id',
                        'contentobject_attribute_version' => ':contentobject_attribute_version',
                        'identifier' => ':identifier',
                    ]
                )
                ->setParameter(':contentobject_attribute_id', $field->id, Types::INTEGER)
                ->setParameter(':contentobject_attribute_version', $versionInfo->versionNo, Types::INTEGER)
                ->setParameter(':identifier', $identifier, Types::STRING);

            $insertQuery->execute();
        }
    }

    public function getFieldData(VersionInfo $versionInfo, Field $field): void
    {
        $field->value->externalData = $this->loadFieldData($field->id, $versionInfo->versionNo);
    }

    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds): void
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->delete($this->connection->quoteIdentifier('sckenhancedselection'))
            ->where(
                $query->expr()->and(
                    $query->expr()->in('contentobject_attribute_id', [':contentobject_attribute_id']),
                    $query->expr()->eq('contentobject_attribute_version', ':contentobject_attribute_version')
                )
            )
            ->setParameter(':contentobject_attribute_id', $fieldIds, Connection::PARAM_INT_ARRAY)
            ->setParameter(':contentobject_attribute_version', $versionInfo->versionNo, Types::INTEGER);

        $query->execute();
    }

    /**
     * Returns the data for the given $fieldId and $versionNo.
     *
     * @return string[]
     */
    private function loadFieldData(int $fieldId, int $versionNo): array
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->select('DISTINCT identifier')
            ->from($this->connection->quoteIdentifier('sckenhancedselection'))
            ->where(
                $query->expr()->and(
                    $query->expr()->eq('contentobject_attribute_id', ':contentobject_attribute_id'),
                    $query->expr()->eq('contentobject_attribute_version', ':contentobject_attribute_version')
                )
            )
            ->setParameter(':contentobject_attribute_id', $fieldId, Types::INTEGER)
            ->setParameter(':contentobject_attribute_version', $versionNo, Types::INTEGER);

        $statement = $query->execute();

        $rows = $statement->fetchAllAssociative();

        return array_map(
            static fn (array $row): string => $row['identifier'],
            $rows
        );
    }
}
