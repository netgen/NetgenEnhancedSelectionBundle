<?php
namespace Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use eZ\Publish\SPI\FieldType\Indexable;
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
        $selectionKeys = (array)$field->value->externalData;
        $selectionIds = [];
        $selectionNames = [];

        foreach ($fieldDefinition->fieldTypeConstraints->fieldSettings['options'] as $option) {
            if (in_array($option['identifier'], $selectionKeys)) {
                $selectionIds[] = $option['id'];
                $selectionNames[] = $option['name'];
            }
        }
        return [
            new Search\Field(
                'enhanced_selection_identifiers',
                $selectionKeys,
                new Search\FieldType\MultipleStringField()
            ),
            new Search\Field(
                'enhanced_selection_ids',
                $selectionIds,
                new Search\FieldType\MultipleIntegerField()
            ),
            new Search\Field(
                'enhanced_selection_text',
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
    /**
     * Get index field types for search backend.
     *
     * @return \eZ\Publish\SPI\Search\FieldType[]
     */
    public function getIndexDefinition()
    {
        return [
            'enhanced_selection_identifiers' => new Search\FieldType\MultipleStringField(),
            'enhanced_selection_ids' => new Search\FieldType\MultipleIntegerField(),
            'enhanced_selection_text' => new Search\FieldType\TextField(),
            'fulltext' => new Search\FieldType\FullTextField(),
        ];
    }
    /**
     * Get name of the default field to be used for matching.
     *
     * @return string
     */
    public function getDefaultMatchField()
    {
        return 'enhanced_selection_identifiers';
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
