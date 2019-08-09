<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection;

use eZ\Publish\Core\FieldType\Value as BaseValue;

final class Value extends BaseValue
{
    /**
     * The list of selection identifiers.
     *
     * @var string[]
     */
    public $identifiers = [];

    /**
     * @param string[] $identifiers
     */
    public function __construct(array $identifiers = [])
    {
        $this->identifiers = $identifiers;
    }

    public function __toString(): string
    {
        return implode(', ', $this->identifiers);
    }
}
