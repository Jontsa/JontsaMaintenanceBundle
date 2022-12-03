<?php
declare(strict_types=1);

namespace Jontsa\Bundle\MaintenanceBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

class JontsaMaintenanceExtension extends Extension
{

    public function load(array $configs, ContainerBuilder $container) : void
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../../Resources/config')
        );
        $loader->load('services.xml');

        $container->setParameter('jontsa_maintenance.whitelist.ip', $config['whitelist']['ip']);
        $container->setParameter('jontsa_maintenance.lock_path', $config['lock_path']);
    }

}
