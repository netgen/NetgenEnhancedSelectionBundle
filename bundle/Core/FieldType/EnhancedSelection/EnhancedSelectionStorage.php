<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection;

use Ibexa\Contracts\Core\FieldType\GatewayBasedStorage;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\VersionInfo;

final class EnhancedSelectionStorage extends GatewayBasedStorage
{
    public function storeFieldData(VersionInfo $versionInfo, Field $field): ?bool
    {
        $this->gateway->deleteFieldData($versionInfo, [$field->id]);
        if (!empty($field->value->externalData)) {
            $this->gateway->storeFieldData($versionInfo, $field);
        }

        return null;
    }

    public function getFieldData(VersionInfo $versionInfo, Field $field): void
    {
        $this->gateway->getFieldData($versionInfo, $field);
    }

    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds): bool
    {
        $this->gateway->deleteFieldData($versionInfo, $fieldIds);

        return true;
    }

    public function hasFieldData(): bool
    {
        return true;
    }

    public function getIndexData(VersionInfo $versionInfo, Field $field): bool
    {
        return false;
    }
}
