<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Templating\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class NetgenEnhancedSelectionExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'netgen_enhanced_selection_name',
                [NetgenEnhancedSelectionRuntime::class, 'getSelectionName']
            ),
        ];
    }
}
