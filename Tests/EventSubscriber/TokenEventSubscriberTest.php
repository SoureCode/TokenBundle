<?php

namespace SoureCode\Bundle\Token\Tests\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Registry;
use SoureCode\Bundle\Token\Repository\TokenRepository;
use SoureCode\Bundle\Token\Service\TokenServiceInterface;
use SoureCode\Bundle\Token\Tests\AbstractTokenTestCase;
use SoureCode\Bundle\Token\Tests\Mock\Entity\ResourceMock;

class TokenEventSubscriberTest extends AbstractTokenTestCase
{
    public function testFindByResourceAndType(): void
    {
        $container = static::bootKernel()->getContainer();

        // Arrange
        /**
         * @var TokenServiceInterface $service
         */
        $service = $container->get(TokenServiceInterface::class);
        /**
         * @var TokenRepository $repository
         */
        $repository = $container->get(TokenRepository::class);
        /**
         * @var Registry $doctrine
         */
        $doctrine = $container->get('doctrine');
        $manager = $doctrine->getManager();
        $resourceRepository = $manager->getRepository(ResourceMock::class);
        $mock = new ResourceMock();
        $service->create($mock, 'test');
        $id = $mock->getId();

        $manager->clear();

        $resource = $resourceRepository->find($id);

        self::assertNotNull($resource);

        // Act
        $manager->remove($resource);
        $manager->flush();

        // Assert
        self::assertCount(0, $repository->findAll());
        self::assertCount(0, $resourceRepository->findAll());
    }
}
