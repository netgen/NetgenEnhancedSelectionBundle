<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\EnhancedSelectionStorage\Gateway;

use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\EnhancedSelectionStorage\Gateway;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;
use eZ\Publish\Core\Persistence\Database\DatabaseHandler;
use RuntimeException;
use PDO;

class LegacyStorage extends Gateway
{
    /**
     * Connection
     *
     * @var \eZ\Publish\Core\Persistence\Database\DatabaseHandler
     */
    protected $connection;

    /**
     * Sets the data storage connection to use
     *
     * @throws \RuntimeException if $connection is not an instance of
     *         {@link \eZ\Publish\Core\Persistence\Database\DatabaseHandler}
     *
     * @param \eZ\Publish\Core\Persistence\Database\DatabaseHandler $connection
     */
    public function setConnection( $connection )
    {
        // This obviously violates the Liskov substitution Principle, but with
        // the given class design there is no sane other option. Actually the
        // dbHandler *should* be passed to the constructor, and there should
        // not be the need to post-inject it.
        if ( !$connection instanceof DatabaseHandler )
        {
            throw new RuntimeException( "Invalid connection passed" );
        }

        $this->connection = $connection;
    }

    /**
     * Returns the active connection
     *
     * @throws \RuntimeException if no connection has been set, yet.
     *
     * @return \eZ\Publish\Core\Persistence\Database\DatabaseHandler
     */
    protected function getConnection()
    {
        if ( $this->connection === null )
        {
            throw new RuntimeException( "Missing database connection." );
        }

        return $this->connection;
    }

    /**
     * Stores the identifiers in the database based on the given field data
     *
     * @param \eZ\Publish\SPI\Persistence\Content\VersionInfo $versionInfo
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     */
    public function storeFieldData( VersionInfo $versionInfo, Field $field )
    {
        $connection = $this->getConnection();

        foreach ( $field->value->externalData as $identifier )
        {
            $insertQuery = $connection->createInsertQuery();
            $insertQuery
                ->insertInto( $connection->quoteTable( "sckenhancedselection" ) )
                ->set(
                    $connection->quoteColumn( "contentobject_attribute_id" ),
                    $insertQuery->bindValue( $field->id, null, PDO::PARAM_INT )
                )->set(
                    $connection->quoteColumn( "contentobject_attribute_version" ),
                    $insertQuery->bindValue( $versionInfo->versionNo, null, PDO::PARAM_INT )
                )->set(
                    $connection->quoteColumn( "identifier" ),
                    $insertQuery->bindValue( $identifier )
                );

            $insertQuery->prepare()->execute();
        }
    }

    /**
     * Gets the identifiers stored in the field
     *
     * @param \eZ\Publish\SPI\Persistence\Content\VersionInfo $versionInfo
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     */
    public function getFieldData( VersionInfo $versionInfo, Field $field )
    {
        $field->value->externalData = $this->loadFieldData( $field->id, $versionInfo->versionNo );
    }

    /**
     * Deletes field data for all $fieldIds in the version identified by
     * $versionInfo.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\VersionInfo $versionInfo
     * @param array $fieldIds
     */
    public function deleteFieldData( VersionInfo $versionInfo, array $fieldIds )
    {
        $connection = $this->getConnection();

        $query = $connection->createDeleteQuery();
        $query
            ->deleteFrom( $connection->quoteTable( "sckenhancedselection" ) )
            ->where(
                $query->expr->lAnd(
                    $query->expr->in(
                        $connection->quoteColumn( "contentobject_attribute_id" ),
                        $fieldIds
                    ),
                    $query->expr->eq(
                        $connection->quoteColumn( "contentobject_attribute_version" ),
                        $query->bindValue( $versionInfo->versionNo, null, PDO::PARAM_INT )
                    )
                )
            );

        $query->prepare()->execute();
    }

    /**
     * Returns the data for the given $fieldId and $versionNo
     *
     * @param mixed $fieldId
     * @param mixed $versionNo
     *
     * @return array
     */
    protected function loadFieldData( $fieldId, $versionNo )
    {
        $connection = $this->getConnection();

        $query = $connection->createSelectQuery();
        $query
            ->selectDistinct( "identifier" )
            ->from( $connection->quoteTable( "sckenhancedselection" ) )
            ->where(
                $query->expr->lAnd(
                    $query->expr->eq(
                        $connection->quoteColumn( "contentobject_attribute_id", "sckenhancedselection" ),
                        $query->bindValue( $fieldId, null, PDO::PARAM_INT )
                    ),
                    $query->expr->eq(
                        $connection->quoteColumn( "contentobject_attribute_version", "sckenhancedselection" ),
                        $query->bindValue( $versionNo, null, PDO::PARAM_INT )
                    )
                )
            );

        $statement = $query->prepare();
        $statement->execute();

        $rows = $statement->fetchAll( PDO::FETCH_ASSOC );

        return array_map(
            function( array $row )
            {
                return $row["identifier"];
            },
            $rows
        );
    }
}
