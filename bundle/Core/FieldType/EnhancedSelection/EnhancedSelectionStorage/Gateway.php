<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\EnhancedSelectionStorage;

use Ibexa\Contracts\Core\FieldType\StorageGateway;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;

abstract class Gateway extends StorageGateway
{
    /**
     * Stores the identifiers in the database based on the given field data.
     */
    abstract public function storeFieldData(VersionInfo $versionInfo, Field $field): void;

    /**
     * Gets the identifiers stored in the field.
     */
    abstract public function getFieldData(VersionInfo $versionInfo, Field $field): void;

    /**
     * Deletes field data for all $fieldIds in the version identified by
     * $versionInfo.
     */
    abstract public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds): void;
}
