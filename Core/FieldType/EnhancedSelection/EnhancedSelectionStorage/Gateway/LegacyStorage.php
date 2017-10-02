<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\EnhancedSelectionStorage\Gateway;

use eZ\Publish\Core\Persistence\Database\DatabaseHandler;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\EnhancedSelectionStorage\Gateway;
use PDO;

class LegacyStorage extends Gateway
{
    /**
     * @var \eZ\Publish\Core\Persistence\Database\DatabaseHandler
     */
    protected $dbHandler;

    /**
     * Constructor.
     *
     * @param \eZ\Publish\Core\Persistence\Database\DatabaseHandler $dbHandler
     */
    public function __construct(DatabaseHandler $dbHandler)
    {
        $this->dbHandler = $dbHandler;
    }

    /**
     * Stores the identifiers in the database based on the given field data.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\VersionInfo $versionInfo
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     */
    public function storeFieldData(VersionInfo $versionInfo, Field $field)
    {
        foreach ($field->value->externalData as $identifier) {
            $insertQuery = $this->dbHandler->createInsertQuery();
            $insertQuery
                ->insertInto($this->dbHandler->quoteTable('sckenhancedselection'))
                ->set(
                    $this->dbHandler->quoteColumn('contentobject_attribute_id'),
                    $insertQuery->bindValue($field->id, null, PDO::PARAM_INT)
                )->set(
                    $this->dbHandler->quoteColumn('contentobject_attribute_version'),
                    $insertQuery->bindValue($versionInfo->versionNo, null, PDO::PARAM_INT)
                )->set(
                    $this->dbHandler->quoteColumn('identifier'),
                    $insertQuery->bindValue($identifier)
                );

            $insertQuery->prepare()->execute();
        }
    }

    /**
     * Gets the identifiers stored in the field.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\VersionInfo $versionInfo
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     */
    public function getFieldData(VersionInfo $versionInfo, Field $field)
    {
        $field->value->externalData = $this->loadFieldData($field->id, $versionInfo->versionNo);
    }

    /**
     * Deletes field data for all $fieldIds in the version identified by
     * $versionInfo.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\VersionInfo $versionInfo
     * @param array $fieldIds
     */
    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds)
    {
        $query = $this->dbHandler->createDeleteQuery();
        $query
            ->deleteFrom($this->dbHandler->quoteTable('sckenhancedselection'))
            ->where(
                $query->expr->lAnd(
                    $query->expr->in(
                        $this->dbHandler->quoteColumn('contentobject_attribute_id'),
                        $fieldIds
                    ),
                    $query->expr->eq(
                        $this->dbHandler->quoteColumn('contentobject_attribute_version'),
                        $query->bindValue($versionInfo->versionNo, null, PDO::PARAM_INT)
                    )
                )
            );

        $query->prepare()->execute();
    }

    /**
     * Returns the data for the given $fieldId and $versionNo.
     *
     * @param mixed $fieldId
     * @param mixed $versionNo
     *
     * @return array
     */
    protected function loadFieldData($fieldId, $versionNo)
    {
        $query = $this->dbHandler->createSelectQuery();
        $query
            ->selectDistinct('identifier')
            ->from($this->dbHandler->quoteTable('sckenhancedselection'))
            ->where(
                $query->expr->lAnd(
                    $query->expr->eq(
                        $this->dbHandler->quoteColumn('contentobject_attribute_id', 'sckenhancedselection'),
                        $query->bindValue($fieldId, null, PDO::PARAM_INT)
                    ),
                    $query->expr->eq(
                        $this->dbHandler->quoteColumn('contentobject_attribute_version', 'sckenhancedselection'),
                        $query->bindValue($versionNo, null, PDO::PARAM_INT)
                    )
                )
            );

        $statement = $query->prepare();
        $statement->execute();

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            function (array $row) {
                return $row['identifier'];
            },
            $rows
        );
    }
}
