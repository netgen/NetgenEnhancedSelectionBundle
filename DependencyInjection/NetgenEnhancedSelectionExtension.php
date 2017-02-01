<?php

namespace Netgen\Bundle\EnhancedSelectionBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\Yaml\Yaml;

class NetgenEnhancedSelectionExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('fieldtypes.yml');
        $loader->load('storage_engines.yml');
        $loader->load('templating.yml');

        $activatedBundles = array_keys($container->getParameter('kernel.bundles'));

        if ($container->hasParameter('ezpublish.persistence.legacy.search.gateway.sort_clause_handler.common.field.class')) {
            $loader->load('search/legacy_old_namespaces.yml');
        } elseif (in_array('EzPublishLegacySearchEngineBundle', $activatedBundles)) {
            $loader->load('legacy.yml');
        }

        if (in_array('EzSystemsEzPlatformSolrSearchEngineBundle', $activatedBundles)) {
            $loader->load('solr.yml');
        }
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $configFile = __DIR__ . '/../Resources/config/ezpublish.yml';
        $config = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig('ezpublish', $config);
        $container->addResource(new FileResource($configFile));
    }
}
