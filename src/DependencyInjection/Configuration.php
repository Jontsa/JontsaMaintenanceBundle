<?php
declare(strict_types=1);

namespace Jontsa\Bundle\MaintenanceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('jontsa_maintenance');
        $rootNode = method_exists(TreeBuilder::class, 'getRootNode') ? $treeBuilder->getRootNode() : $treeBuilder->root('jontsa_maintenance');

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
            ->end();

        return $treeBuilder;
    }
}