<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Form\Type\FieldType;

use Ibexa\Contracts\Core\Repository\FieldTypeService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class EnhancedSelectionFieldType extends AbstractType
{
    private FieldTypeService $fieldTypeService;

    public function __construct(FieldTypeService $fieldTypeService)
    {
        $this->fieldTypeService = $fieldTypeService;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['field_definition']);
        $resolver->setAllowedTypes('field_definition', FieldDefinition::class);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition */
        $fieldDefinition = $options['field_definition'];

        $choices = [];
        foreach ($fieldDefinition->fieldSettings['options'] as $option) {
            $choices[$option['name']] = $option['identifier'];
        }

        $builder
            ->add(
                'identifiers',
                ChoiceType::class,
                [
                    'choices' => $choices,
                    'multiple' => $fieldDefinition->fieldSettings['isMultiple'],
                    'expanded' => $fieldDefinition->fieldSettings['isExpanded'],
                ]
            )
            ->addModelTransformer(
                new FieldValueTransformer(
                    $this->fieldTypeService->getFieldType('sckenhancedselection'),
                    $fieldDefinition
                )
            );
    }

    public function getBlockPrefix(): string
    {
        return 'ezplatform_fieldtype_sckenhancedselection';
    }
}
