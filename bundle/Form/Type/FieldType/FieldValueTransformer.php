<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Form\Type\FieldType;

use eZ\Publish\API\Repository\FieldType;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\FieldType\Value;
use Symfony\Component\Form\DataTransformerInterface;
use function is_array;

final class FieldValueTransformer implements DataTransformerInterface
{
    /**
     * @var \eZ\Publish\API\Repository\FieldType
     */
    private $fieldType;

    /**
     * @var \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition
     */
    private $fieldDefinition;

    public function __construct(FieldType $fieldType, FieldDefinition $fieldDefinition)
    {
        $this->fieldType = $fieldType;
        $this->fieldDefinition = $fieldDefinition;
    }

    public function transform($value): ?array
    {
        if (!$value instanceof Value) {
            return null;
        }

        $hash = $this->fieldType->toHash($value);

        $identifiers = ['identifiers' => null];
        if ($this->fieldDefinition->fieldSettings['isMultiple']) {
            $identifiers['identifiers'] = $hash;
        } elseif (isset($hash[0])) {
            $identifiers['identifiers'] = $hash[0];
        }

        return $identifiers;
    }

    public function reverseTransform($value): Value
    {
        if ($value === null) {
            return $this->fieldType->getEmptyValue();
        }

        $hash = is_array($value['identifiers']) ?
            $value['identifiers'] :
            [$value['identifiers']];

        return $this->fieldType->fromHash($hash);
    }
}
