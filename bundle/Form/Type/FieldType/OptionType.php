<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Form\Type\FieldType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class OptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'name',
                TextType::class,
                array(
                    'required' => true,
                    'label' => 'field_definition.sckenhancedselection.settings.options.name',
                )
            )
            ->add(
                'identifier',
                TextType::class,
                array(
                    'required' => true,
                    'label' => 'field_definition.sckenhancedselection.settings.options.identifier',
                )
            )
            ->add(
                'priority',
                NumberType::class,
                array(
                    'required' => true,
                    'label' => 'field_definition.sckenhancedselection.settings.options.priority',
                )
            );
    }

    public function getBlockPrefix()
    {
        return 'ezplatform_fieldtype_sckenhancedselection_option';
    }
}
