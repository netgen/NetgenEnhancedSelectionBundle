<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Core\Persistence\Legacy\Content\FieldValue\Converter;

use DOMDocument;
use eZ\Publish\Core\FieldType\FieldSettings;
use eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;

class EnhancedSelectionConverter implements Converter
{
    /**
     * Factory for current class.
     *
     * @note Class should instead be configured as service if it gains dependencies.
     *
     * @return \Netgen\Bundle\EnhancedSelectionBundle\Core\Persistence\Legacy\Content\FieldValue\Converter\EnhancedSelectionConverter
     */
    public static function create()
    {
        return new self();
    }

    /**
     * Converts data from $value to $storageFieldValue.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\FieldValue $value
     * @param \eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue $storageFieldValue
     */
    public function toStorageValue(FieldValue $value, StorageFieldValue $storageFieldValue)
    {
    }

    /**
     * Converts data from $value to $fieldValue.
     *
     * @param \eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue $value
     * @param \eZ\Publish\SPI\Persistence\Content\FieldValue $fieldValue
     */
    public function toFieldValue(StorageFieldValue $value, FieldValue $fieldValue)
    {
    }

    /**
     * Converts field definition data in $fieldDef into $storageFieldDef.
     *
     * @param \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition $fieldDef
     * @param \eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition $storageDef
     */
    public function toStorageFieldDefinition(FieldDefinition $fieldDef, StorageFieldDefinition $storageDef)
    {
        $fieldSettings = $fieldDef->fieldTypeConstraints->fieldSettings;

        $xml = new DOMDocument('1.0', 'utf-8');
        $xml->appendChild(
            $selection = $xml->createElement('content')
        );

        // Options
        if (!empty($fieldSettings['options'])) {
            $selection->appendChild(
                $options = $xml->createElement('options')
            );

            foreach ($fieldSettings['options'] as $key => $option) {
                $options->appendChild(
                    $optionNode = $xml->createElement('option')
                );

                $optionNode->setAttribute('id', (string) ($key + 1));
                $optionNode->setAttribute('name', (string) $option['name']);
                $optionNode->setAttribute('identifier', (string) $option['identifier']);
                $optionNode->setAttribute('priority', (string) $option['priority']);
            }
        }

        // Multiselect
        $multiSelectNode = $xml->createElement(
            'multiselect',
            !empty($fieldSettings['isMultiple']) ? '1' : '0'
        );
        $selection->appendChild($multiSelectNode);

        // Expanded
        $expandedNode = $xml->createElement(
            'expanded',
            !empty($fieldSettings['isExpanded']) ? '1' : '0'
        );
        $selection->appendChild($expandedNode);

        // Delimiter
        if (!empty($fieldSettings['delimiter'])) {
            $delimiterElement = $xml->createElement('delimiter');
            $delimiterElement->appendChild($xml->createCDATASection($fieldSettings['delimiter']));
            $selection->appendChild($delimiterElement);
        }

        // DB query
        if (!empty($fieldSettings['query'])) {
            $queryElement = $xml->createElement('query');
            $queryElement->appendChild($xml->createCDATASection($fieldSettings['query']));
            $selection->appendChild($queryElement);
        }

        $storageDef->dataText5 = $xml->saveXML();
    }

    /**
     * Converts field definition data in $storageDef into $fieldDef.
     *
     * @param \eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition $storageDef
     * @param \eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition $fieldDef
     */
    public function toFieldDefinition(StorageFieldDefinition $storageDef, FieldDefinition $fieldDef)
    {
        $simpleXml = simplexml_load_string($storageDef->dataText5);
        $options = array();
        $isMultiple = false;
        $isExpanded = false;
        $delimiter = '';
        $query = '';

        if ($simpleXml !== false) {
            if (!empty($simpleXml->options)) {
                foreach ($simpleXml->options->option as $option) {
                    $options[] = array(
                        'id' => (int) $option['id'],
                        'name' => (string) $option['name'],
                        'identifier' => (string) $option['identifier'],
                        'priority' => (int) $option['priority'],
                    );
                }
            }

            if (!empty($simpleXml->multiselect)) {
                $isMultiple = true;
            }

            if (!empty($simpleXml->expanded)) {
                $isExpanded = true;
            }

            if (!empty($simpleXml->delimiter)) {
                $delimiter = (string) $simpleXml->delimiter;
            }

            if (!empty($simpleXml->query)) {
                $query = (string) $simpleXml->query;
            }
        }

        $fieldDef->fieldTypeConstraints->fieldSettings = new FieldSettings(
            array(
                'isMultiple' => $isMultiple,
                'isExpanded' => $isExpanded,
                'delimiter' => $delimiter,
                'options' => $options,
                'query' => $query,
            )
        );
    }

    /**
     * Returns the name of the index column in the attribute table.
     *
     * Returns the name of the index column the datatype uses, which is either
     * "sort_key_int" or "sort_key_string". This column is then used for
     * filtering and sorting for this type.
     *
     * If the indexing is not supported, this method must return false.
     *
     * @return string|false
     */
    public function getIndexColumn()
    {
        return false;
    }
}
