<?php

namespace SoureCode\Bundle\Token\Tests;

use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Nyholm\BundleTest\BaseBundleTestCase;
use SoureCode\Bundle\Token\Repository\TokenRepository;
use SoureCode\Bundle\Token\Service\TokenServiceInterface;
use SoureCode\Bundle\Token\SoureCodeTokenBundle;
use SoureCode\BundleTest\CommandTrait;
use SoureCode\BundleTest\DatabaseTrait;
use SoureCode\BundleTest\KernelTrait;
use Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle;

abstract class AbstractTokenBundleTestCase extends BaseBundleTestCase
{
    use CommandTrait;
    use DatabaseTrait;
    use KernelTrait;

    protected function getBundleClass()
    {
        return SoureCodeTokenBundle::class;
    }

    protected function setUp(): void
    {
        $kernel = $this->createKernel();

        $kernel->addConfigFile(__DIR__.'/config.yml');
        $kernel->addBundle(DoctrineBundle::class);
        $kernel->addBundle(DAMADoctrineTestBundle::class);
        $kernel->addBundle(StofDoctrineExtensionsBundle::class);

        $this->setUpKernel($kernel);
        $this->setUpCommand();
        $this->setUpDatabase();
    }

    protected function tearDown(): void
    {
        $this->tearDownCommand();
        $this->tearDownDatabase();
        $this->tearDownKernel();
    }

    protected function getService(): TokenServiceInterface
    {
        return $this->getContainer()->get(TokenServiceInterface::class);
    }

    protected function getRepository(): TokenRepository
    {
        return $this->getContainer()->get(TokenRepository::class);
    }
}
