<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection;

use Ibexa\Contracts\Core\FieldType\Indexable;
use Ibexa\Contracts\Core\Persistence\Content\Field;
use Ibexa\Contracts\Core\Persistence\Content\Type\FieldDefinition;
use Ibexa\Contracts\Core\Search;

use function implode;
use function in_array;

final class SearchField implements Indexable
{
    public function getIndexData(Field $field, FieldDefinition $fieldDefinition): array
    {
        $selectionKeys = (array) $field->value->externalData;
        $selectionIds = [];
        $selectionNames = [];

        foreach ($fieldDefinition->fieldTypeConstraints->fieldSettings['options'] as $option) {
            if (in_array($option['identifier'], $selectionKeys, true)) {
                $selectionIds[] = $option['id'];
                $selectionNames[] = $option['name'];
            }
        }

        return [
            new Search\Field(
                'identifiers',
                $selectionKeys,
                new Search\FieldType\MultipleStringField()
            ),
            new Search\Field(
                'ids',
                $selectionIds,
                new Search\FieldType\MultipleIntegerField()
            ),
            new Search\Field(
                'names',
                implode(' ', $selectionNames),
                new Search\FieldType\TextField()
            ),
            new Search\Field(
                'fulltext',
                implode(' ', $selectionNames),
                new Search\FieldType\FullTextField()
            ),
        ];
    }

    public function getIndexDefinition(): array
    {
        return [
            'identifiers' => new Search\FieldType\MultipleStringField(),
            'ids' => new Search\FieldType\MultipleIntegerField(),
            'names' => new Search\FieldType\TextField(),
            'fulltext' => new Search\FieldType\FullTextField(),
        ];
    }

    public function getDefaultMatchField(): string
    {
        return 'identifiers';
    }

    public function getDefaultSortField(): string
    {
        return $this->getDefaultMatchField();
    }
}
