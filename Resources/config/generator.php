<?php

use SoureCode\Component\Token\Checker\TokenUniqueChecker;
use SoureCode\Component\Token\Checker\TokenUniqueCheckerInterface;
use SoureCode\Component\Token\Generator\TokenGenerator;
use SoureCode\Component\Token\Generator\TokenGeneratorInterface;
use SoureCode\Component\Token\Generator\UniqueTokenGenerator;
use SoureCode\Component\Token\Generator\UniqueTokenGeneratorInterface;
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
