<?php
declare(strict_types=1);

namespace Jontsa\Bundle\MaintenanceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('jontsa_maintenance');
        /** @var NodeDefinition|ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('whitelist')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('ip')
                            ->scalarPrototype()->end()
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('lock_path')
                    ->defaultValue('%kernel.project_dir%/var/cache/maintenance')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
