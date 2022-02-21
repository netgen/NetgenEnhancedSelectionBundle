<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Templating\Twig;

use Ibexa\Contracts\Core\Repository\ContentTypeService;
use Ibexa\Contracts\Core\Repository\Values\Content\Content;
use function in_array;

final class NetgenEnhancedSelectionRuntime
{
    /**
     * @var \Ibexa\Contracts\Core\Repository\ContentTypeService
     */
    private $contentTypeService;

    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
    }

    /**
     * Returns selection names.
     *
     * @return array|string|null
     */
    public function getSelectionName(Content $content, string $fieldDefIdentifier, ?string $selectionIdentifier = null)
    {
        $names = [];
        $identifiers = [$selectionIdentifier];

        if ($selectionIdentifier === null) {
            $field = $content->getField($fieldDefIdentifier);
            $identifiers = $field->value->identifiers;
        }

        $contentType = $this->contentTypeService->loadContentType(
            $content->contentInfo->contentTypeId
        );

        $fieldDefinition = $contentType->getFieldDefinition($fieldDefIdentifier);

        foreach ($fieldDefinition->fieldSettings['options'] as $option) {
            if (in_array($option['identifier'], $identifiers, true)) {
                $names[$option['identifier']] = $option['name'];
            }
        }

        if ($selectionIdentifier !== null) {
            return !empty($names) ? $names[$selectionIdentifier] : null;
        }

        return $names;
    }
}
