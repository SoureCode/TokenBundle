<?php

namespace SoureCode\Bundle\Token\Tests\Domain;

use SoureCode\Bundle\Token\Tests\AbstractTokenBundleTestCase;
use SoureCode\Bundle\Token\Tests\App\Entity\BarResource;
use Symfony\Component\Uid\Uuid;

class TokenAwareTraitTest extends AbstractTokenBundleTestCase
{
    public function testAddToken(): void
    {
        // Arrange
        $service = $this->getService();
        $repository = $this->getRepository();
        $entityManager = $this->getEntityManager();
        $resourceRepository = $entityManager->getRepository(BarResource::class);

        $resource = new BarResource();
        $entityManager->persist($resource);

        $tokenA = $service->create('bar');
        $tokenB = $service->create('foo');

        // Act
        $resource->addToken($tokenA);
        $resource->addToken($tokenB);

        $entityManager->flush();

        // Assert
        $entityManager->clear();
        /**
         * @var BarResource $foundResource
         */
        $foundResource = $resourceRepository->find($resource->getId());
        $tokens = $foundResource->getTokens();

        self::assertCount(2, $tokens);
        self::assertCount(2, $repository->findAll());

        self::assertEquals('bar', $tokens[0]->getType());
        self::assertEquals('foo', $tokens[1]->getType());
        self::assertInstanceOf(Uuid::class, $tokens[0]->getId());
        self::assertInstanceOf(Uuid::class, $tokens[1]->getId());
    }

    public function testRemoveToken(): void
    {
        // Arrange
        $service = $this->getService();
        $repository = $this->getRepository();
        $entityManager = $this->getEntityManager();
        $resourceRepository = $entityManager->getRepository(BarResource::class);

        $resource = new BarResource();
        $entityManager->persist($resource);

        $resource->addToken($service->create('bar'));
        $resource->addToken($service->create('foo'));

        $entityManager->flush();
        $entityManager->clear();

        // Act
        /**
         * @var BarResource $foundResource
         */
        $foundResource = $resourceRepository->find($resource->getId());
        $foundResource->removeToken($foundResource->getTokens()[1]);
        $entityManager->flush();

        // Assert
        $entityManager->clear();
        /**
         * @var BarResource $foundResource
         */
        $foundResource = $resourceRepository->find($resource->getId());
        $tokens = $foundResource->getTokens();

        self::assertCount(1, $tokens);
        self::assertCount(1, $repository->findAll());

        self::assertEquals('bar', $tokens[0]->getType());
    }

    public function testRemoveTokenOrphan(): void
    {
        // Arrange
        $service = $this->getService();
        $repository = $this->getRepository();
        $entityManager = $this->getEntityManager();
        $resourceRepository = $entityManager->getRepository(BarResource::class);

        $resource = new BarResource();
        $entityManager->persist($resource);

        $resource->addToken($service->create('bar'));
        $resource->addToken($service->create('foo'));

        $entityManager->flush();
        $entityManager->clear();

        // Act
        /**
         * @var BarResource $foundResource
         */
        $foundResource = $resourceRepository->find($resource->getId());
        $entityManager->remove($foundResource);
        $entityManager->flush();

        // Assert
        $entityManager->clear();

        self::assertCount(0, $resourceRepository->findAll());
        self::assertCount(0, $repository->findAll());
    }

    public function testRemoveTokenButKeepResource(): void
    {
        // Arrange
        $service = $this->getService();
        $repository = $this->getRepository();
        $entityManager = $this->getEntityManager();
        $resourceRepository = $entityManager->getRepository(BarResource::class);

        $resource = new BarResource();
        $entityManager->persist($resource);

        $resource->addToken($service->create('bar'));

        $entityManager->flush();
        $entityManager->clear();

        // Act
        /**
         * @var BarResource $foundResource
         */
        $foundResource = $resourceRepository->find($resource->getId());
        $entityManager->remove($foundResource->getTokens()[0]);
        $entityManager->flush();

        // Assert
        $entityManager->clear();

        self::assertCount(1, $resourceRepository->findAll());
        self::assertCount(0, $repository->findAll());
    }
}
