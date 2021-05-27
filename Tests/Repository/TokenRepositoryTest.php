<?php

namespace SoureCode\Bundle\Token\Tests\Repository;

use SoureCode\Bundle\Token\Tests\AbstractTokenBundleTestCase;
use SoureCode\Bundle\Token\Tests\App\Entity\FooResource;

class TokenRepositoryTest extends AbstractTokenBundleTestCase
{
    public function testFindByResourceAndType(): void
    {
        // Arrange
        $service = $this->getService();
        $repository = $this->getRepository();
        $entityManager = $this->getEntityManager();

        $resource = new FooResource();
        $entityManager->persist($resource);
        $entityManager->flush();

        $token = $service->create($resource, 'bar');

        $entityManager->clear();

        // Act
        $actual = $repository->findByResourceAndType($resource, 'bar');

        // Assert
        self::assertNotNull($actual);
        self::assertSame($token->getId()->toBase58(), $actual->getId()->toBase58());
        self::assertSame($token->getType(), $actual->getType());
        self::assertSame($token->getResourceType(), $actual->getResourceType());
        self::assertSame($token->getResourceId(), $actual->getResourceId());
    }
}
