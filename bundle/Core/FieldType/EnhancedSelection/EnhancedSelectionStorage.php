<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection;

use eZ\Publish\SPI\FieldType\GatewayBasedStorage;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;

class EnhancedSelectionStorage extends GatewayBasedStorage
{
    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context): ?bool
    {
        $this->gateway->deleteFieldData($versionInfo, [$field->id]);
        if (!empty($field->value->externalData)) {
            $this->gateway->storeFieldData($versionInfo, $field);
        }

        return null;
    }

    public function getFieldData(VersionInfo $versionInfo, Field $field, array $context): void
    {
        $this->gateway->getFieldData($versionInfo, $field);
    }

    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds, array $context): bool
    {
        $this->gateway->deleteFieldData($versionInfo, $fieldIds);

        return true;
    }

    public function hasFieldData(): bool
    {
        return true;
    }

    public function getIndexData(VersionInfo $versionInfo, Field $field, array $context)
    {
        return false;
    }
}
