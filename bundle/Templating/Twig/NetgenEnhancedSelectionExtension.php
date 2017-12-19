<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Templating\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NetgenEnhancedSelectionExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return array(
            new TwigFunction(
                'netgen_enhanced_selection_name',
                array(NetgenEnhancedSelectionRuntime::class, 'getSelectionName')
            ),
        );
    }
}
