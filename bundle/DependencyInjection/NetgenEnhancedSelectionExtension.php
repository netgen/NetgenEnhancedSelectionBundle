<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

class NetgenEnhancedSelectionExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        $loader->load('field_types.yml');
        $loader->load('templating.yml');
        $loader->load('commands.yml');
        $loader->load('parameters.yml');

        $activatedBundles = array_keys($container->getParameter('kernel.bundles'));

        if (in_array('EzPublishLegacySearchEngineBundle', $activatedBundles, true)) {
            $loader->load('search/legacy.yml');
        }

        if (in_array('EzSystemsEzPlatformSolrSearchEngineBundle', $activatedBundles, true)) {
            $loader->load('search/solr.yml');
        }

        if (in_array('EzPlatformAdminUiBundle', $activatedBundles, true)) {
            $loader->load('ezadminui/services.yml');
        }
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $configFile = __DIR__ . '/../Resources/config/ezplatform.yml';
        $config = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig('ezpublish', $config);
        $container->addResource(new FileResource($configFile));
    }
}
