<?php

namespace Netgen\Bundle\EnhancedSelectionBundle;

use Netgen\Bundle\EnhancedSelectionBundle\DependencyInjection\CompilerPass\TwigRuntimePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class NetgenEnhancedSelectionBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TwigRuntimePass());
    }
}
