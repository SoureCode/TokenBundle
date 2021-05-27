<?php

use SoureCode\Bundle\Token\Service\TokenService;
use SoureCode\Bundle\Token\Service\TokenServiceInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public();

    // ============================
    // = Service
    // ============================
    $services->set('sourecode.token.service.token', TokenService::class)
        ->args(
            [
                service('sourecode.token.object_manager'),
                service('sourecode.token.repository.token'),
                param('sourecode.token.config.tokens'),
            ]
        )
        ->alias(TokenServiceInterface::class, 'sourecode.token.service.token');
};
