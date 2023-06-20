<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Form\Type\FieldType;

use Generator;
use Ibexa\Contracts\Core\Repository\LanguageService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class OptionType extends AbstractType
{
    private LanguageService $languageService;

    public function __construct(LanguageService $languageService)
    {
        $this->languageService = $languageService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $languageCodeChoices = (function (): Generator {
            yield 'field_definition.sckenhancedselection.settings.options.all_languages' => '';

            foreach ($this->languageService->loadLanguages() as $language) {
                yield $language->getName() => $language->languageCode;
            }
        })();

        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'required' => true,
                    'label' => 'field_definition.sckenhancedselection.settings.options.name',
                ]
            )
            ->add(
                'identifier',
                TextType::class,
                [
                    'required' => false,
                    'empty_data' => '',
                    'label' => 'field_definition.sckenhancedselection.settings.options.identifier',
                ]
            )
            ->add(
                'language_code',
                ChoiceType::class,
                [
                    'required' => false,
                    'label' => 'field_definition.sckenhancedselection.settings.options.language_code',
                    'choices' => $languageCodeChoices,
                ]
            )
            ->add(
                'priority',
                NumberType::class,
                [
                    'required' => true,
                    'label' => 'field_definition.sckenhancedselection.settings.options.priority',
                ]
            );
    }

    public function getBlockPrefix(): string
    {
        return 'ezplatform_fieldtype_sckenhancedselection_option';
    }
}
