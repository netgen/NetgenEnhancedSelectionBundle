<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Form\FieldTypeHandler;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\MVC\ConfigResolverInterface;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Value as EnhancedSelectionValue;
use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpKernel\Kernel;

class EnhancedSelection extends FieldTypeHandler
{
    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    protected $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    /**
     * Converts the eZ Publish field type value to a format that can be accepted by the form.
     *
     * @param \eZ\Publish\SPI\FieldType\Value $value
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition
     *
     * @return mixed
     */
    public function convertFieldValueToForm(Value $value, FieldDefinition $fieldDefinition = null)
    {
        $isMultiple = true;
        if ($fieldDefinition !== null) {
            $fieldSettings = $fieldDefinition->getFieldSettings();
            $isMultiple = $fieldSettings['isMultiple'];
        }

        if (!$isMultiple) {
            if (empty($value->identifiers)) {
                return '';
            }

            return $value->identifiers[0];
        }

        return $value->identifiers;
    }

    /**
     * Converts the form data to a format that can be accepted by eZ Publish field type.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function convertFieldValueFromForm($data)
    {
        if ($data === null) {
            return new EnhancedSelectionValue();
        }
        return new EnhancedSelectionValue(is_array($data) ? $data : array($data));
    }

    /**
     * In most cases implementations of methods {@link self::buildCreateFieldForm()}
     * and {@link self::buildUpdateFieldForm()} will be the same, therefore default
     * handler implementation of those falls back to this method.
     *
     * @param \Symfony\Component\Form\FormBuilderInterface $formBuilder
     * @param \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition
     * @param string $languageCode
     * @param \eZ\Publish\API\Repository\Values\Content\Content $content
     */
    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        $languageCode,
        Content $content = null
    ) {
        $options = $this->getDefaultFieldOptions($fieldDefinition, $languageCode, $content);

        $fieldSettings = $fieldDefinition->getFieldSettings();
        $optionsValues = $fieldSettings['options'];

        $options['multiple'] = $fieldSettings['isMultiple'];
        $options['expanded'] = $fieldSettings['isExpanded'];
        $options['choices_as_values'] = true;
        $options['choices'] = $this->getValues($optionsValues);

        $formBuilder->add(
            $fieldDefinition->identifier,
            Kernel::VERSION_ID < 20800 ? 'choice' : ChoiceType::class,
            $options
        );
    }

    /**
     * Get key value array for display on form.
     *
     * @param array $options
     *
     * @return array
     */
    protected function getValues($options)
    {
        $values = array();

        foreach ($options as $option) {
            if (!empty($option['identifier']) && !empty($option['name'])) {
                $values[$option['name']] = $option['identifier'];
            }
        }

        return $values;
    }
}
