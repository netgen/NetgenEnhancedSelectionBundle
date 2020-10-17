<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Core\FieldType\EnhancedSelection;

use EzSystems\RepositoryForms\Data\Content\FieldData;
use EzSystems\RepositoryForms\Data\FieldDefinitionData;
use EzSystems\RepositoryForms\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\RepositoryForms\FieldType\FieldValueFormMapperInterface;
use Netgen\Bundle\EnhancedSelectionBundle\Form\Type\FieldType\EnhancedSelectionFieldType;
use Netgen\Bundle\EnhancedSelectionBundle\Form\Type\FieldType\OptionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints;

class FormMapper implements FieldDefinitionFormMapperInterface, FieldValueFormMapperInterface
{
    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        $fieldForm
            ->add(
                $fieldForm->getConfig()->getFormFactory()->createBuilder()
                    ->create(
                        'value',
                        EnhancedSelectionFieldType::class,
                        array(
                            'required' => $data->fieldDefinition->isRequired,
                            'label' => $data->fieldDefinition->getName(),
                            'field_definition' => $data->fieldDefinition,
                        )
                    )
                    ->setAutoInitialize(false)
                    ->getForm()
            );
    }

    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data)
    {
        $fieldDefinitionForm
            ->add(
                'options',
                CollectionType::class,
                array(
                    'required' => true,
                    'property_path' => 'fieldSettings[options]',
                    'label' => 'field_definition.sckenhancedselection.settings.options',
                    'entry_type' => OptionType::class,
                    'entry_options' => array(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'delete_empty' => true,
                )
            )
            ->add(
                'isMultiple', CheckboxType::class, array(
                    'required' => false,
                    'property_path' => 'fieldSettings[isMultiple]',
                    'label' => 'field_definition.sckenhancedselection.settings.is_multiple',
                    'constraints' => array(
                        new Constraints\Type(array('type' => 'bool')),
                        new Constraints\NotNull(),
                    ),
                )
            )
            ->add(
                'isExpanded', CheckboxType::class, array(
                    'required' => false,
                    'property_path' => 'fieldSettings[isExpanded]',
                    'label' => 'field_definition.sckenhancedselection.settings.is_expanded',
                    'constraints' => array(
                        new Constraints\Type(array('type' => 'bool')),
                        new Constraints\NotNull(),
                    ),
                )
            )
            ->add(
                'delimiter', TextType::class, array(
                    'required' => false,
                    'property_path' => 'fieldSettings[delimiter]',
                    'label' => 'field_definition.sckenhancedselection.settings.delimiter',
                    'empty_data' => '',
                    'constraints' => array(
                        new Constraints\Type(array('type' => 'string')),
                    ),
                )
            )
            ->add(
                'query', TextareaType::class, array(
                    'required' => false,
                    'property_path' => 'fieldSettings[query]',
                    'label' => 'field_definition.sckenhancedselection.settings.query',
                    'empty_data' => '',
                    'constraints' => array(
                        new Constraints\Type(array('type' => 'string')),
                    ),
                )
            );
    }
}
