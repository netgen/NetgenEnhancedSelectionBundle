<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Core\Persistence\Legacy\Content\FieldValue\Converter;

use DOMDocument;
use eZ\Publish\Core\FieldType\FieldSettings;
use eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;
use function simplexml_load_string;

final class EnhancedSelectionConverter implements Converter
{
    public function toStorageValue(FieldValue $value, StorageFieldValue $storageFieldValue): void
    {
    }

    public function toFieldValue(StorageFieldValue $value, FieldValue $fieldValue): void
    {
    }

    public function toStorageFieldDefinition(FieldDefinition $fieldDef, StorageFieldDefinition $storageDef): void
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

    public function toFieldDefinition(StorageFieldDefinition $storageDef, FieldDefinition $fieldDef): void
    {
        $simpleXml = simplexml_load_string($storageDef->dataText5);
        $options = [];
        $isMultiple = false;
        $isExpanded = false;
        $delimiter = '';
        $query = '';

        if ($simpleXml !== false) {
            if (!empty($simpleXml->options)) {
                foreach ($simpleXml->options->option as $option) {
                    $options[] = [
                        'id' => (int) $option['id'],
                        'name' => (string) $option['name'],
                        'identifier' => (string) $option['identifier'],
                        'priority' => (int) $option['priority'],
                    ];
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
            [
                'isMultiple' => $isMultiple,
                'isExpanded' => $isExpanded,
                'delimiter' => $delimiter,
                'options' => $options,
                'query' => $query,
            ]
        );
    }

    public function getIndexColumn()
    {
        return false;
    }
}
