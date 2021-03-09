<?php

use Doctrine\Persistence\ObjectManager;
use SoureCode\Bundle\Token\Repository\TokenRepository;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $container) {
    $services = $container->services();

    // ============================
    // = Object Manager
    // ============================
    $services->set('sourecode.token.object_manager', ObjectManager::class)
        ->args(
            [
                param('sourecode.token.config.model_manager_name'),
            ]
        );

    // ============================
    // = Repository
    // ============================
    $services->set('sourecode.token.repository.token', TokenRepository::class)
        ->tag('doctrine.repository_service')
        ->public()
        ->args(
            [
                service('sourecode.token.doctrine_registry'),
            ]
        );

    $services
        ->alias(TokenRepository::class, 'sourecode.token.repository.token')
        ->public();
};
