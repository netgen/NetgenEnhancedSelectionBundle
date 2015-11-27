<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Form\FieldTypeHandler;

use Netgen\Bundle\EzFormsBundle\Form\FieldTypeHandler;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\SPI\FieldType\Value;
use Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection\Value as EnhancedSelectionValue;
use Symfony\Component\Validator\Constraints;

/**
 * Class EnhancedSelection
 *
 * @package Netgen\EnhancedSelectionBundle\FieldType\FormBuilder
 */
class EnhancedSelection extends FieldTypeHandler
{
    /**
     * {@inheritdoc}
     *
     * @param EnhancedSelectionValue $value
     */
    public function convertFieldValueToForm( Value $value, FieldDefinition $fieldDefinition = null )
    {
        $isMultiple = true;
        if ( null !== $fieldDefinition ) {
            $isMultiple = $fieldDefinition->getFieldSettings()['isMultiple'];
        }

        if ( !$isMultiple )
        {
            return array_pop($value->identifiers);
        }

        return $value->identifiers;
    }

    /**
     * {@inheritdoc}
     */
    public function convertFieldValueFromForm( $data )
    {

        return new EnhancedSelectionValue( (array)$data );
    }

    /**
     * {@inheritdoc}
     */
    protected function buildFieldForm(
        FormBuilderInterface $formBuilder,
        FieldDefinition $fieldDefinition,
        $languageCode,
        Content $content = null
    )
    {
        $options = $this->getDefaultFieldOptions( $fieldDefinition, $languageCode, $content );

        $optionsValues = $fieldDefinition->getFieldSettings()['options'];
        $values = $this->getValues($optionsValues);

        $options['expanded'] = false;
        $options['multiple'] = $fieldDefinition->getFieldSettings()["isMultiple"];
        $options['choice_list'] = new ChoiceList( array_keys($values), array_values($values) );

        $formBuilder->add( $fieldDefinition->identifier, "choice", $options );
    }

    /**
     * Get key value array for display on form
     *
     * @param array $options
     *
     * @return array
     */
    protected function getValues( $options )
    {
        $values = array();

        foreach( $options as $option )
        {
            if ( !empty( $option['identifier'] ) && !empty( $option['name'] ) )
            {
                $values[$option['identifier']] = $option['name'];
            }
        }

        return $values;
    }
}
