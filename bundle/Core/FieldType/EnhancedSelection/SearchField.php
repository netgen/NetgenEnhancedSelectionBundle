<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection;

use eZ\Publish\SPI\FieldType\Indexable;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use eZ\Publish\SPI\Search;

class SearchField implements Indexable
{
    /**
     * Get index data for field for search backend.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\Field $field
     * @param \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition $fieldDefinition
     *
     * @return \eZ\Publish\SPI\Search\Field[]
     */
    public function getIndexData(Field $field, FieldDefinition $fieldDefinition)
    {
        $selectionKeys = (array) $field->value->externalData;
        $selectionIds = array();
        $selectionNames = array();

        foreach ($fieldDefinition->fieldTypeConstraints->fieldSettings['options'] as $option) {
            if (in_array($option['identifier'], $selectionKeys, true)) {
                $selectionIds[] = $option['id'];
                $selectionNames[] = $option['name'];
            }
        }

        return array(
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
        );
    }

    /**
     * Get index field types for search backend.
     *
     * @return \eZ\Publish\SPI\Search\FieldType[]
     */
    public function getIndexDefinition()
    {
        return array(
            'identifiers' => new Search\FieldType\MultipleStringField(),
            'ids' => new Search\FieldType\MultipleIntegerField(),
            'names' => new Search\FieldType\TextField(),
            'fulltext' => new Search\FieldType\FullTextField(),
        );
    }

    /**
     * Get name of the default field to be used for matching.
     *
     * @return string
     */
    public function getDefaultMatchField()
    {
        return 'identifiers';
    }

    /**
     * Get name of the default field to be used for sorting.
     *
     * @return string
     */
    public function getDefaultSortField()
    {
        return $this->getDefaultMatchField();
    }
}
