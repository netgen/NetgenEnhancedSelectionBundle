<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Templating\Twig;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\Helper\TranslationHelper;

class NetgenEnhancedSelectionRuntime
{
    /**
     * @var \eZ\Publish\API\Repository\ContentTypeService
     */
    protected $contentTypeService;

    /**
     * @var \eZ\Publish\Core\Helper\TranslationHelper
     */
    protected $translationHelper;

    /**
     * NetgenEnhancedSelectionExtension constructor.
     *
     * @param \eZ\Publish\API\Repository\ContentTypeService $contentTypeService
     * @param \eZ\Publish\Core\Helper\TranslationHelper $translationHelper
     */
    public function __construct(ContentTypeService $contentTypeService, TranslationHelper $translationHelper)
    {
        $this->contentTypeService = $contentTypeService;
        $this->translationHelper = $translationHelper;
    }

    /**
     * Returns selection names.
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     * @param string $fieldDefIdentifier
     * @param null|string $selectionIdentifier
     *
     * @return array|string|null
     */
    public function getSelectionName(Content $content, $fieldDefIdentifier, $selectionIdentifier = null)
    {
        $names = array();
        $identifiers = array($selectionIdentifier);

        if ($selectionIdentifier === null) {
            $field = $this->translationHelper->getTranslatedField($content, $fieldDefIdentifier);
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
