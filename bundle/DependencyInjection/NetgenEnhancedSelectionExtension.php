<?php

declare(strict_types=1);

namespace Netgen\Bundle\EnhancedSelectionBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

use function array_keys;
use function file_get_contents;
use function in_array;

final class NetgenEnhancedSelectionExtension extends Extension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('field_types.yaml');
        $loader->load('templating.yaml');
        $loader->load('commands.yaml');
        $loader->load('installer.yaml');

        $activatedBundles = array_keys($container->getParameter('kernel.bundles'));

        if (in_array('IbexaLegacySearchEngineBundle', $activatedBundles, true)) {
            $loader->load('search/legacy.yaml');
        }

        if (in_array('IbexaSolrBundle', $activatedBundles, true)) {
            $loader->load('search/solr.yaml');
        }

        if (in_array('IbexaAdminUiBundle', $activatedBundles, true)) {
            $loader->load('ibexa/admin/services.yaml');
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        $configFile = __DIR__ . '/../Resources/config/ibexa.yaml';
        $config = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig('ibexa', $config);
        $container->addResource(new FileResource($configFile));
    }
}
