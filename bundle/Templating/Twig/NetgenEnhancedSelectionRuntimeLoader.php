<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Templating\Twig;

use Twig\RuntimeLoader\RuntimeLoaderInterface;

class NetgenEnhancedSelectionRuntimeLoader implements RuntimeLoaderInterface
{
    /**
     * @var \Netgen\Bundle\EnhancedSelectionBundle\Templating\Twig\NetgenEnhancedSelectionRuntime
     */
    protected $runtime;

    public function __construct(NetgenEnhancedSelectionRuntime $runtime)
    {
        $this->runtime = $runtime;
    }

    public function load($class)
    {
        if (!is_a($this->runtime, $class, true)) {
            return;
        }

        return $this->runtime;
    }
}
