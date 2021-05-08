<?php

namespace SoureCode\Bundle\Token\Tests;

use SoureCode\Bundle\Common\SoureCodeCommonBundle;
use SoureCode\Bundle\Token\SoureCodeTokenBundle;
use SoureCode\BundleTest\Configurator\KernelConfigurator;
use SoureCode\BundleTest\TestCase\AbstractKernelTestCase;
use SoureCode\BundleTest\TestCase\DoctrineSetupTrait;

abstract class AbstractTokenTestCase extends AbstractKernelTestCase
{
    use DoctrineSetupTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupDoctrine();
    }

    protected static function bootKernel(array $options = [])
    {
        $kernel = parent::bootKernel($options);

        static::prepareDatabase();

        return $kernel;
    }

    protected static function getKernelConfigurator(): KernelConfigurator
    {
        $configurator = parent::getKernelConfigurator();

        $configurator->setBundle(SoureCodeCommonBundle::class);
        $configurator->setBundle(
            SoureCodeTokenBundle::class,
            'soure_code_token',
            [
                'tokens' => [
                    'foo' => [
                        'expiration' => 'PT1H',
                        'length' => 6,
                    ],
                    'bar' => [
                        'expiration' => 'PT4H',
                        'length' => 10,
                    ],
                    'test' => [
                        'expiration' => 'PT4H',
                        'length' => 10,
                    ],
                ],
            ]
        );

        return $configurator;
    }

    protected function tearDown(): void
    {
        static::clearDatabase();

        parent::tearDown();
    }

    protected function getDoctrineMappings(): array
    {
        return [
            'SoureCodeTokenBundle' => [
                'prefix' => 'SoureCode\Bundle\Token\Model',
                'type' => 'xml',
            ],
            'SoureCodeTokenTest' => [
                'type' => 'annotation',
                'dir' => __DIR__.'/Mock/Entity',
                'prefix' => 'SoureCode\Bundle\Token\Tests\Mock\Entity',
                'is_bundle' => false,
            ],
        ];
    }

    protected function getDoctrineMigrations(): array
    {
        return [
            'SoureCode\Bundle\Token\Migrations' => __DIR__.'/../Migrations',
            'SoureCode\Bundle\Token\Tests\Mock\Migrations' => __DIR__.'/Mock/Migrations',
        ];
    }
}
