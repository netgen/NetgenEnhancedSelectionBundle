<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\Tests\Templating\Twig;

use Netgen\Bundle\EnhancedSelectionBundle\Templating\Twig\NetgenEnhancedSelectionExtension;
use PHPUnit\Framework\TestCase;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class NetgenEnhancedSelectionExtensionTest extends TestCase
{
    private NetgenEnhancedSelectionExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new NetgenEnhancedSelectionExtension();
    }

    public function testInstanceOfTwigExtension(): void
    {
        self::assertInstanceOf(AbstractExtension::class, $this->extension);
    }

    public function testGetFunctions(): void
    {
        foreach ($this->extension->getFunctions() as $function) {
            self::assertInstanceOf(TwigFunction::class, $function);
        }
    }
}
