<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\EnhancedSelectionStorage\Gateway;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\FetchMode;
use Doctrine\DBAL\Types\Types;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\EnhancedSelectionStorage\Gateway;

final class DoctrineStorage extends Gateway
{
    /**
     * @var \Doctrine\DBAL\Connection
     */
    private $connection;

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
                $query->expr()->andX(
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
     * @param mixed $fieldId
     * @param mixed $versionNo
     *
     * @return array
     */
    private function loadFieldData($fieldId, $versionNo): array
    {
        $query = $this->connection->createQueryBuilder();
        $query
            ->select('DISTINCT identifier')
            ->from($this->connection->quoteIdentifier('sckenhancedselection'))
            ->where(
                $query->expr()->andX(
                    $query->expr()->eq('contentobject_attribute_id', ':contentobject_attribute_id'),
                    $query->expr()->eq('contentobject_attribute_version', ':contentobject_attribute_version')
                )
            )
            ->setParameter(':contentobject_attribute_id', $fieldId, Types::INTEGER)
            ->setParameter(':contentobject_attribute_version', $versionNo, Types::INTEGER);

        $statement = $query->execute();

        $rows = $statement->fetchAll(FetchMode::ASSOCIATIVE);

        return array_map(
            static function (array $row) {
                return $row['identifier'];
            },
            $rows
        );
    }
}
