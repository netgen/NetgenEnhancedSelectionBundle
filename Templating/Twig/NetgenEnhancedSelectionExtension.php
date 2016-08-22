<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Templating\Twig;

use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\Helper\TranslationHelper;
use eZ\Publish\API\Repository\Values\Content\Content;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Value;

/**
 * Class NetgenEnhancedSelectionExtension
 * @package Netgen\Bundle\EnhancedSelectionBundle\Templating\Twig
 */
class NetgenEnhancedSelectionExtension extends \Twig_Extension
{
    /**
     * @var ContentTypeService
     */
    protected $contentTypeService;

    /**
     * @var TranslationHelper
     */
    protected $translationHelper;

    /**
     * NetgenEnhancedSelectionExtension constructor.
     *
     * @param ContentTypeService $contentTypeService
     * @param TranslationHelper $translationHelper
     */
    public function __construct(ContentTypeService $contentTypeService, TranslationHelper $translationHelper)
    {
        $this->contentTypeService = $contentTypeService;
        $this->translationHelper = $translationHelper;
    }

    /**
     * @inheritdoc
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction(
                'netgen_enhanced_selection_name',
                array($this, 'getSelectionName')
            ),
        );
    }

    /**
     * Returns selection names
     *
     * @param Content $content
     * @param string $fieldDefIdentifier
     * @param null|string $selectionIdentifier
     * @return array
     */
    public function getSelectionName(Content $content, $fieldDefIdentifier, $selectionIdentifier = null)
    {
        $names = array();

        if (empty($selectionIdentifier)) {
            $field = $this->translationHelper->getTranslatedField($content, $fieldDefIdentifier);
            $identifiers = $field->value->identifiers;
        }

        try {
            $contentType = $this->contentTypeService->loadContentType(
                $content->contentInfo->contentTypeId
            );
        } catch (NotFoundException $e) {
            return $names;
        }


        $fieldDefinitions = $contentType->fieldDefinitions;

        foreach ($fieldDefinitions as $fieldDefinition) {
            if ($fieldDefinition->identifier === $fieldDefIdentifier) {
                foreach ($fieldDefinition->fieldSettings['options'] as $option) {
                    if (!is_null($selectionIdentifier) && $option['identifier'] === $selectionIdentifier) {
                        return array($option['name']);
                    } else if (in_array($option['identifier'], $identifiers)) {
                        $names[] = $option['name'];
                    }
                }
            }
        }

        return $names;
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'netgen_enhanced_selection';
    }
}
