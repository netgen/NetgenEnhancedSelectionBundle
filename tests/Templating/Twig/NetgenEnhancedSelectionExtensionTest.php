<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\Templating\Twig;

use Netgen\Bundle\EnhancedSelectionBundle\Templating\Twig\NetgenEnhancedSelectionExtension;
use PHPUnit\Framework\TestCase;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class NetgenEnhancedSelectionExtensionTest extends TestCase
{
    /**
     * @var NetgenEnhancedSelectionExtension
     */
    protected $extension;

    public function setUp()
    {
        $this->extension = new NetgenEnhancedSelectionExtension();
    }

    public function testInstanceOfTwigExtension()
    {
        $this->assertInstanceOf(AbstractExtension::class, $this->extension);
    }

    public function testGetFunctions()
    {
        foreach ($this->extension->getFunctions() as $function) {
            $this->assertInstanceOf(TwigFunction::class, $function);
        }
    }
}
