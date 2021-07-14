<?php

namespace SoureCode\Bundle\Token\Tests\Service;

use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Registry;
use SoureCode\Bundle\Token\Domain\Token;
use SoureCode\Bundle\Token\Exception\InvalidArgumentException;
use SoureCode\Bundle\Token\Exception\RuntimeException;
use SoureCode\Bundle\Token\Service\TokenServiceInterface;
use SoureCode\Bundle\Token\Tests\AbstractTokenBundleTestCase;
use SoureCode\Bundle\Token\Tests\App\Entity\FooResource;
use Symfony\Component\Uid\UuidV6;

class TokenServiceTest extends AbstractTokenBundleTestCase
{
    public function testTokenServiceRegistered(): void
    {
        $container = $this->getContainer();

        // Arrange and Act and Assert
        self::assertTrue($container->has('sourecode.token.service.token'), 'It should be registered by key');
        self::assertTrue($container->has(TokenServiceInterface::class), 'It should be registered by interface');
    }

    public function testCreateWithPersistentResource(): void
    {
        // Arrange
        $service = $this->getService();

        // Act
        $resource = new FooResource();
        $token = $service->create('foo');
        $resource->setActivationToken($token);

        /**
         * @var Registry $doctrine
         */
        $entityManager = $this->getEntityManager();
        $entityManager->persist($resource);
        $entityManager->flush();

        // Assert
        self::assertIsNumeric($resource->getId());
        self::assertInstanceOf(UuidV6::class, $token->getId());
    }

    public function testGetMissingTokenConfiguration(): void
    {
        // Assert
        $this->expectException(RuntimeException::class);

        // Arrange
        $service = $this->getService();
        $resource = new FooResource();

        // Act
        $service->create('baz');
    }

    public function testSaveAndRemove(): void
    {
        // Arrange
        $service = $this->getService();
        $repository = $service->getRepository();
        $entityManager = $this->getEntityManager();

        $token = new Token();
        $token->setType('bar');

        // Act
        $service->save($token);

        // Assert
        self::assertTrue($entityManager->contains($token));
        self::assertNotNull($token->getId());
        self::assertNotNull($token->getCreatedAt());

        $id = $token->getId();

        // Act
        $service->remove($token);

        // Assert
        $entityManager->clear();

        $tokens = $repository->findAll();

        self::assertFalse($entityManager->contains($token));
        self::assertNull($service->find($id));
        self::assertCount(0, $tokens);
    }

    public function testValidateNull(): void
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange
        $service = $this->getService();

        // Act
        $service->validate(null);
    }

    public function testValidateExpired(): void
    {
        // Assert
        $this->expectException(RuntimeException::class);

        // Arrange
        $service = $this->getService();

        // Act
        $date = new DateTime();
        $token = new Token();
        $token->setType('foo');
        $token->setCreatedAt($date->sub(new DateInterval('PT5H')));
        $service->validate($token);
    }

    public function testValidateValid(): void
    {
        // Arrange
        $service = $this->getService();

        // Act
        $date = new DateTime();
        $token = new Token();
        $token->setType('foo');
        $token->setCreatedAt($date->sub(new DateInterval('PT10M')));
        $service->validate($token);

        self::assertNull(null);
    }

    public function testGetExpiresAtNotPersisted(): void
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange
        $service = $this->getService();

        // Act
        $token = new Token();
        $token->setType('foo');

        $service->getExpiresAt($token);
    }

    public function testGetExpirationIntervalInvalidType(): void
    {
        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Arrange
        $service = $this->getService();

        // Act
        $token = new Token();

        $service->getExpirationInterval($token);
    }

    public function testOrphanRemoval(): void
    {
        // Arrange
        $service = $this->getService();
        $repository = $service->getRepository();
        $entityManager = $this->getEntityManager();

        $token = $service->create('foo');
        $resource = new FooResource();

        $resource->setActivationToken($token);
        $entityManager->persist($resource);
        $entityManager->flush();
        $entityManager->clear();

        $resourceRepository = $entityManager->getRepository($resource::class);

        // Act
        $foundResource = $resourceRepository->find($resource->getId());
        $entityManager->remove($foundResource);
        $entityManager->flush();

        // Assert
        $tokens = $repository->findAll();

        self::assertCount(0, $tokens);
    }

    public function testSetNull(): void
    {
        // Arrange
        $service = $this->getService();
        $repository = $service->getRepository();
        $entityManager = $this->getEntityManager();

        $token = $service->create('foo');
        $resource = new FooResource();

        $resource->setActivationToken($token);
        $entityManager->persist($resource);
        $entityManager->flush();
        $entityManager->clear();

        $resourceRepository = $entityManager->getRepository($resource::class);

        // Act
        /**
         * @var FooResource $foundResource
         */
        $foundResource = $resourceRepository->find($resource->getId());
        $foundResource->setActivationToken(null);
        $entityManager->flush();

        // Assert
        $tokens = $repository->findAll();

        self::assertCount(0, $tokens);
    }
}
