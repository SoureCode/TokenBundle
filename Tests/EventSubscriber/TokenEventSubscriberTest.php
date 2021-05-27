<?php

namespace SoureCode\Bundle\Token\Tests\EventSubscriber;

use SoureCode\Bundle\Token\Tests\AbstractTokenBundleTestCase;
use SoureCode\Bundle\Token\Tests\App\Entity\FooResource;

class TokenEventSubscriberTest extends AbstractTokenBundleTestCase
{
    public function testTokenDeletionOnResourceDeletion(): void
    {
        // Arrange
        $service = $this->getService();
        $repository = $this->getRepository();
        $entityManager = $this->getEntityManager();

        $fooRepository = $entityManager->getRepository(FooResource::class);

        $resource = new FooResource();
        $entityManager->persist($resource);
        $entityManager->flush();

        $service->create($resource, 'test');

        // Assert
        self::assertCount(1, $repository->findAll());
        self::assertCount(1, $fooRepository->findAll());

        $id = $resource->getId();

        $entityManager->clear();

        $foundResource = $fooRepository->find($id);

        self::assertNotNull($foundResource);

        // Act
        $entityManager->remove($foundResource);
        $entityManager->flush();

        // Assert
        self::assertCount(0, $repository->findAll());
        self::assertCount(0, $fooRepository->findAll());
    }
}
