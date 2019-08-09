<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentType;
use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\Core\FieldType\ValidationError;
use eZ\Publish\Core\FieldType\Value as BaseValue;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use eZ\Publish\SPI\Persistence\Content\FieldValue;

final class Type extends FieldType
{
    /**
     * @var array
     */
    protected $settingsSchema = [
        'options' => [
            'type' => 'array',
            'default' => [],
        ],
        'isMultiple' => [
            'type' => 'boolean',
            'default' => false,
        ],
        'delimiter' => [
            'type' => 'string',
            'default' => '',
        ],
        'query' => [
            'type' => 'string',
            'default' => '',
        ],
    ];

    public function getFieldTypeIdentifier(): string
    {
        return 'sckenhancedselection';
    }

    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        return (string) $value;
    }

    /**
     * @return \eZ\Publish\SPI\FieldType\Value|\Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Value
     */
    public function getEmptyValue(): SPIValue
    {
        return new Value();
    }

    /**
     * @param mixed $hash
     *
     * @return \eZ\Publish\SPI\FieldType\Value|\Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Value
     */
    public function fromHash($hash): SPIValue
    {
        if (!is_array($hash)) {
            return new Value();
        }

        $selectionIdentifiers = [];
        foreach ($hash as $hashItem) {
            if (!is_string($hashItem)) {
                continue;
            }

            $selectionIdentifiers[] = $hashItem;
        }

        return new Value($selectionIdentifiers);
    }

    /**
     * @param \eZ\Publish\SPI\FieldType\Value|\Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Value $value
     *
     * @return mixed
     */
    public function toHash(SPIValue $value)
    {
        return $value->identifiers;
    }

    /**
     * @param \eZ\Publish\SPI\FieldType\Value|\Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Value $value
     */
    public function toPersistenceValue(SPIValue $value): FieldValue
    {
        return new FieldValue(
            [
                'data' => null,
                'externalData' => $this->toHash($value),
                'sortKey' => $this->getSortInfo($value),
            ]
        );
    }

    /**
     * @return \eZ\Publish\SPI\FieldType\Value|\Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Value
     */
    public function fromPersistenceValue(FieldValue $fieldValue): SPIValue
    {
        return $this->fromHash($fieldValue->externalData);
    }

    /**
     * @param \eZ\Publish\SPI\FieldType\Value|\Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Value $value
     */
    public function isEmptyValue(SPIValue $value): bool
    {
        return $value === null || $value->identifiers === $this->getEmptyValue()->identifiers;
    }

    /**
     * @param mixed $fieldSettings
     *
     * @return \eZ\Publish\SPI\FieldType\ValidationError[]
     */
    public function validateFieldSettings($fieldSettings): array
    {
        $validationErrors = [];
        if (!is_array($fieldSettings)) {
            $validationErrors[] = new ValidationError('Field settings must be in form of an array');

            return $validationErrors;
        }

        foreach ($fieldSettings as $name => $value) {
            if (!isset($this->settingsSchema[$name])) {
                $validationErrors[] = new ValidationError(
                    "'%setting%' setting is unknown",
                    null,
                    [
                        '%setting%' => $name,
                    ]
                );

                continue;
            }

            switch ($name) {
                case 'options':
                    if (!is_array($value)) {
                        $validationErrors[] = new ValidationError(
                            "'%setting%' setting value must be of array type",
                            null,
                            [
                                '%setting%' => $name,
                            ]
                        );
                    } else {
                        foreach ($value as $option) {
                            if (!isset($option['name'])) {
                                $validationErrors[] = new ValidationError(
                                    "'%setting%' setting value item must have a 'name' property",
                                    null,
                                    [
                                        '%setting%' => $name,
                                    ]
                                );
                            } else {
                                if (!is_string($option['name'])) {
                                    $validationErrors[] = new ValidationError(
                                        "'%setting%' setting value item's 'name' property must be of string value",
                                        null,
                                        [
                                            '%setting%' => $name,
                                        ]
                                    );
                                }

                                if (empty($option['name'])) {
                                    $validationErrors[] = new ValidationError(
                                        "'%setting%' setting value item's 'name' property must have a value",
                                        null,
                                        [
                                            '%setting%' => $name,
                                        ]
                                    );
                                }
                            }

                            if (!isset($option['identifier'])) {
                                $validationErrors[] = new ValidationError(
                                    "'%setting%' setting value item must have an 'identifier' property",
                                    null,
                                    [
                                        '%setting%' => $name,
                                    ]
                                );
                            } else {
                                if (!is_string($option['identifier'])) {
                                    $validationErrors[] = new ValidationError(
                                        "'%setting%' setting value item's 'identifier' property must be of string value",
                                        null,
                                        [
                                            '%setting%' => $name,
                                        ]
                                    );
                                }

                                if (empty($option['identifier'])) {
                                    $validationErrors[] = new ValidationError(
                                        "'%setting%' setting value item's 'identifier' property must have a value",
                                        null,
                                        [
                                            '%setting%' => $name,
                                        ]
                                    );
                                }
                            }

                            if (!isset($option['priority'])) {
                                $validationErrors[] = new ValidationError(
                                    "'%setting%' setting value item must have an 'priority' property",
                                    null,
                                    [
                                        '%setting%' => $name,
                                    ]
                                );
                            } elseif (!is_numeric($option['priority'])) {
                                $validationErrors[] = new ValidationError(
                                    "'%setting%' setting value item's 'priority' property must be of numeric value",
                                    null,
                                    [
                                        '%setting%' => $name,
                                    ]
                                );
                            }
                        }
                    }

                    break;
                case 'isMultiple':
                    if (!is_bool($value)) {
                        $validationErrors[] = new ValidationError(
                            "'%setting%' setting value must be of boolean type",
                            null,
                            [
                                '%setting%' => $name,
                            ]
                        );
                    }

                    break;
                case 'delimiter':
                    if (!is_string($value)) {
                        $validationErrors[] = new ValidationError(
                            "'%setting%' setting value must be of string type",
                            null,
                            [
                                '%setting%' => $name,
                            ]
                        );
                    }

                    break;
                case 'query':
                    if (!is_string($value)) {
                        $validationErrors[] = new ValidationError(
                            "'%setting%' setting value must be of string type",
                            null,
                            [
                                '%setting%' => $name,
                            ]
                        );
                    }

                    break;
            }
        }

        return $validationErrors;
    }

    public function isSearchable(): bool
    {
        return true;
    }

    /**
     * @param mixed $inputValue
     *
     * @return mixed The potentially converted input value
     */
    protected function createValueFromInput($inputValue)
    {
        if (is_string($inputValue)) {
            $inputValue = new Value([$inputValue]);
        } elseif (is_array($inputValue)) {
            foreach ($inputValue as $inputValueItem) {
                if (!is_string($inputValueItem)) {
                    return $inputValue;
                }
            }

            $inputValue = new Value($inputValue);
        }

        return $inputValue;
    }

    /**
     * @param \eZ\Publish\Core\FieldType\Value|\Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Value $value
     */
    protected function checkValueStructure(BaseValue $value): void
    {
        if (!is_array($value->identifiers)) {
            throw new InvalidArgumentType(
                '$value->identifiers',
                'array',
                $value->identifiers
            );
        }

        foreach ($value->identifiers as $identifier) {
            if (!is_string($identifier)) {
                throw new InvalidArgumentType(
                    $identifier,
                    Value::class,
                    $identifier
                );
            }
        }
    }

    protected function getSortInfo(BaseValue $value)
    {
        return false;
    }
}
