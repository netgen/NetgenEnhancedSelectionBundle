<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Form\Type\FieldType;

use eZ\Publish\API\Repository\FieldTypeService;
use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnhancedSelectionFieldType extends AbstractType
{
    /**
     * @var \eZ\Publish\API\Repository\FieldTypeService
     */
    private $fieldTypeService;

    public function __construct(FieldTypeService $fieldTypeService)
    {
        $this->fieldTypeService = $fieldTypeService;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(array('field_definition'));
        $resolver->setAllowedTypes('field_definition', FieldDefinition::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \eZ\Publish\API\Repository\Values\ContentType\FieldDefinition $fieldDefinition */
        $fieldDefinition = $options['field_definition'];

        $choices = array();
        foreach ($fieldDefinition->fieldSettings['options'] as $option) {
            $choices[$option['name']] = $option['identifier'];
        }

        $builder
            ->add(
                'identifiers',
                ChoiceType::class,
                array(
                    'choices' => $choices,
                    'choices_as_values' => true,
                    'multiple' => $fieldDefinition->fieldSettings['isMultiple'],
                    'expanded' => $fieldDefinition->fieldSettings['isExpanded'],
                )
            )
            ->addModelTransformer(
                new FieldValueTransformer(
                    $this->fieldTypeService->getFieldType('sckenhancedselection'),
                    $fieldDefinition
                )
            );
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_sckenhancedselection';
    }
}
