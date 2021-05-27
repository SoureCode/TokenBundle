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
use Symfony\Component\Uid\UuidV4;

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
        $persistentResource = new FooResource();
        /**
         * @var Registry $doctrine
         */
        $entityManager = $this->getEntityManager();
        $entityManager->persist($persistentResource);
        $entityManager->flush();

        self::assertIsNumeric($persistentResource->getId());

        // Act
        $actual = $service->create($persistentResource, 'foo');

        // Assert
        self::assertInstanceOf(UuidV4::class, $actual->getId());
        self::assertSame(FooResource::class, $actual->getResourceType());
        self::assertSame($persistentResource->getId(), $actual->getResourceId());
    }

    public function testCreateWithNotPersistentResource(): void
    {
        $this->expectException(InvalidArgumentException::class);

        // Arrange
        $service = $this->getService();
        $notPersistentResource = new FooResource();

        // Act
        $service->create($notPersistentResource, 'bar');
    }

    public function testGetMissingTokenConfiguration(): void
    {
        // Assert
        $this->expectException(RuntimeException::class);

        // Arrange
        $service = $this->getService();
        $resource = new FooResource();

        // Act
        $service->create($resource, 'baz');
    }

    public function testSaveAndRemove(): void
    {
        // Arrange
        $service = $this->getService();
        $entityManager = $this->getEntityManager();

        $token = new Token();
        $token->setType('bar');
        $token->setResourceType('a');
        $token->setResourceId(1);

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
        self::assertFalse($entityManager->contains($token));
        self::assertNull($service->find($id->toBase58()));
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

    public function testFindByResourceAndType(): void
    {
        // Arrange
        $service = $this->getService();
        $entityManager = $this->getEntityManager();

        $resource = new FooResource();
        $entityManager->persist($resource);
        $entityManager->flush();

        $service->create($resource, 'bar');

        $entityManager->clear();

        // Act
        $actual = $service->findByResourceAndType($resource, 'bar');

        // Assert
        self::assertNotNull($actual);
    }
}
