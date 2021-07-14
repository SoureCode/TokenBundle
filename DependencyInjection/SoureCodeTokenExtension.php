<?php

namespace SoureCode\Bundle\Token\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Alias;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class SoureCodeTokenExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->populateConfiguration($container, $config);

        $container->setParameter('sourecode.token.backend_type_orm', true);

        $loader->load('doctrine.php');

        $container->setAlias('sourecode.token.doctrine_registry', new Alias('doctrine', false));

        $definition = $container->getDefinition('sourecode.token.object_manager');
        $definition->setFactory(
            [
                new Reference('sourecode.token.doctrine_registry'),
                'getManager',
            ]
        );

        $loader->load('services.php');
    }

    private function populateConfiguration(ContainerBuilder $container, array $config): void
    {
        foreach ($config as $key => $value) {
            $container->setParameter(sprintf('sourecode.token.config.%s', $key), $value);
        }
    }
}
