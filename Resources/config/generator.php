<?php

use SoureCode\Bundle\Token\Checker\TokenUniqueChecker;
use SoureCode\Bundle\Token\Checker\TokenUniqueCheckerInterface;
use SoureCode\Bundle\Token\Generator\TokenGenerator;
use SoureCode\Bundle\Token\Generator\TokenGeneratorInterface;
use SoureCode\Bundle\Token\Generator\UniqueTokenGenerator;
use SoureCode\Bundle\Token\Generator\UniqueTokenGeneratorInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $container) {
    $services = $container->services();
    $services->defaults()->public();

    // ============================
    // = Generator
    // ============================
    $services->set('sourecode.token.generator.token', TokenGenerator::class)
        ->args(
            [
                service('sourecode.common.generator.random'),
            ]
        )
        ->alias(TokenGeneratorInterface::class, 'sourecode.token.generator.token');

    $services->set('sourecode.token.checker.unique_token', TokenUniqueChecker::class)
        ->args(
            [
                service('sourecode.token.repository.token'),
            ]
        )
        ->alias(TokenUniqueCheckerInterface::class, 'sourecode.token.checker.unique_token');

    $services->set('sourecode.token.generator.unique_token', UniqueTokenGenerator::class)
        ->args(
            [
                service('sourecode.token.generator.token'),
                service('sourecode.token.checker.unique_token'),
            ]
        )
        ->alias(UniqueTokenGeneratorInterface::class, 'sourecode.token.generator.unique_token');
};
