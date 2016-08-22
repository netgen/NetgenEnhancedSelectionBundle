<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Templating\Twig;
use eZ\Publish\API\Repository\ContentTypeService;
use eZ\Publish\API\Repository\Values\Content\VersionInfo;
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
     * NetgenEnhancedSelectionExtension constructor.
     *
     * @param ContentTypeService $contentTypeService
     */
    public function __construct(ContentTypeService $contentTypeService)
    {
        $this->contentTypeService = $contentTypeService;
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
     * Returns selection name by given identifier
     *
     * @param Value $value
     * @param VersionInfo $versionInfo
     * @param string $fieldDefIdentifier
     *
     * @return string
     */
    public function getSelectionName(Value $value, VersionInfo $versionInfo, $fieldDefIdentifier)
    {
        $contentType = $this->contentTypeService->loadContentType(
            $versionInfo->contentInfo->contentTypeId
        );

        $fieldDefinitions = $contentType->fieldDefinitions;

        foreach ($fieldDefinitions as $fieldDefinition) {
            if ($fieldDefinition->identifier === $fieldDefIdentifier) {

                foreach ($fieldDefinition->fieldSettings['options'] as $option) {

                    if ($option['identifier'] === $value->identifiers[0]) {
                        return $option['name'];
                    }

                }
            }
        }

        return '';
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
