<?php

use SoureCode\Bundle\Token\EventSubscriber\TokenEventSubscriber;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $container) {
    $services = $container->services();

    // ============================
    // = EventSubscriber
    // ============================
    $services->set('sourecode.token.event_subscriber.token', TokenEventSubscriber::class)
        ->tag('doctrine.event_subscriber')
        ->args(
            [
                service('sourecode.token.object_manager'),
                service('sourecode.token.repository.token'),
            ]
        );
};
