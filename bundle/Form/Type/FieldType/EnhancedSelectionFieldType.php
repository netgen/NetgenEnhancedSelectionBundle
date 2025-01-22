<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Form\Type\FieldType;

use Ibexa\Contracts\Core\Repository\FieldTypeService;
use Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use function usort;

final class EnhancedSelectionFieldType extends AbstractType
{
    private FieldTypeService $fieldTypeService;

    public function __construct(FieldTypeService $fieldTypeService)
    {
        $this->fieldTypeService = $fieldTypeService;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired(['field_definition', 'language_code']);

        $resolver->setAllowedTypes('field_definition', FieldDefinition::class);
        $resolver->setAllowedTypes('language_code', 'string');
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var \Ibexa\Contracts\Core\Repository\Values\ContentType\FieldDefinition $fieldDefinition */
        $fieldDefinition = $options['field_definition'];
        $fieldOptions = $fieldDefinition->fieldSettings['options'];

        /** @var string $languageCode */
        $languageCode = $options['language_code'];

        usort(
            $fieldOptions,
            static fn (array $option1, array $option2): int => $option2['priority'] <=> $option1['priority']
        );

        $choices = [];
        foreach ($fieldOptions as $fieldOption) {
            if ($fieldOption['identifier'] === '' && $fieldDefinition->isRequired) {
                continue;
            }

            if ($fieldOption['language_code'] === $languageCode || $fieldOption['language_code'] === '') {
                $choices[$fieldOption['name']] = $fieldOption['identifier'];
            }
        }

        $builder
            ->add(
                'identifiers',
                ChoiceType::class,
                [
                    'label' => $fieldDefinition->getName($languageCode) ?? $fieldDefinition->getName(),
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
