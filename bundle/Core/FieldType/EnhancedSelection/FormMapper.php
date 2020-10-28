<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection;

use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformContentForms\Data\Content\FieldData;
use EzSystems\EzPlatformContentForms\FieldType\FieldValueFormMapperInterface;
use Netgen\Bundle\EnhancedSelectionBundle\Form\Type\FieldType\EnhancedSelectionFieldType;
use Netgen\Bundle\EnhancedSelectionBundle\Form\Type\FieldType\OptionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints;

final class FormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data): void
    {
        $fieldForm
            ->add(
                $fieldForm->getConfig()->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        EnhancedSelectionFieldType::class,
                        [
                            'required' => $data->fieldDefinition->isRequired,
                            'label' => $data->fieldDefinition->getName(),
                            'field_definition' => $data->fieldDefinition,
                        ]
                    )
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }

    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        $fieldDefinitionForm
            ->add(
                'options',
                CollectionType::class,
                [
                    'required' => true,
                    'property_path' => 'fieldSettings[options]',
                    'label' => 'field_definition.sckenhancedselection.settings.options',
                    'entry_type' => OptionType::class,
                    'entry_options' => [],
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                ]
            )
            ->add(
                'isMultiple',
                CheckboxType::class,
                [
                    'required' => false,
                    'property_path' => 'fieldSettings[isMultiple]',
                    'label' => 'field_definition.sckenhancedselection.settings.is_multiple',
                    'constraints' => [
                        new Constraints\Type(['type' => 'bool']),
                        new Constraints\NotNull(),
                    ],
                ]
            )
            ->add(
                'isExpanded',
                CheckboxType::class,
                [
                    'required' => false,
                    'property_path' => 'fieldSettings[isExpanded]',
                    'label' => 'field_definition.sckenhancedselection.settings.is_expanded',
                    'constraints' => [
                        new Constraints\Type(['type' => 'bool']),
                        new Constraints\NotNull(),
                    ],
                ]
            )
            ->add(
                'delimiter',
                TextType::class,
                [
                    'required' => false,
                    'property_path' => 'fieldSettings[delimiter]',
                    'label' => 'field_definition.sckenhancedselection.settings.delimiter',
                    'empty_data' => '',
                    'constraints' => [
                        new Constraints\Type(['type' => 'string']),
                    ],
                ]
            )
            ->add(
                'query',
                TextareaType::class,
                [
                    'required' => false,
                    'property_path' => 'fieldSettings[query]',
                    'label' => 'field_definition.sckenhancedselection.settings.query',
                    'empty_data' => '',
                    'constraints' => [
                        new Constraints\Type(['type' => 'string']),
                    ],
                ]
            );
    }
}
