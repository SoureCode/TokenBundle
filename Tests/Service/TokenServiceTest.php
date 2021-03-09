<?php

namespace SoureCode\Bundle\Token\Tests\Service;

use DateInterval;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Registry;
use SoureCode\Bundle\Token\Exception\LogicException;
use SoureCode\Bundle\Token\Exception\RuntimeException;
use SoureCode\Bundle\Token\Service\TokenServiceInterface;
use SoureCode\Bundle\Token\Tests\AbstractTokenTestCase;
use SoureCode\Bundle\Token\Tests\Mock\Entity\ResourceMock;
use SoureCode\Component\Token\Model\Token;
use function strlen;

class TokenServiceTest extends AbstractTokenTestCase
{
    public function testTokenServiceRegistered(): void
    {
        $container = static::bootKernel()->getContainer();

        // Arrange and Act and Assert
        self::assertTrue($container->has('sourecode.token.service.token'), 'It should be registered by key');
        self::assertTrue($container->has(TokenServiceInterface::class), 'It should be registered by interface');
    }

    public function testCreateWithPersistentResource(): void
    {
        // Arrange
        $container = static::bootKernel()->getContainer();
        /**
         * @var TokenServiceInterface $service
         */
        $service = $container->get(TokenServiceInterface::class);
        $persistentResource = new ResourceMock();
        /**
         * @var Registry $doctrine
         */
        $doctrine = $container->get('doctrine');
        $manager = $doctrine->getManager();
        $manager->persist($persistentResource);
        $manager->flush();

        self::assertIsNumeric($persistentResource->getId());

        // Act
        $actual = $service->create($persistentResource, 'foo');

        // Assert
        self::assertIsNumeric($actual->getId());
        self::assertIsString($actual->getValue());
        self::assertSame(6, strlen($actual->getValue() ?? ''));
        self::assertSame(ResourceMock::class, $actual->getResourceType());
        self::assertSame($persistentResource->getId(), $actual->getResourceId());
    }

    public function testCreateWithNotPersistentResource(): void
    {
        // Arrange
        $container = static::bootKernel()->getContainer();
        /**
         * @var TokenServiceInterface $service
         */
        $service = $container->get(TokenServiceInterface::class);
        $notPersistentResource = new ResourceMock();

        // Act
        $actual = $service->create($notPersistentResource, 'bar');

        // Assert
        self::assertIsNumeric($actual->getId());
        self::assertIsString($actual->getValue());
        self::assertSame(10, strlen($actual->getValue() ?? ''));
        self::assertSame(ResourceMock::class, $actual->getResourceType());
        self::assertSame($notPersistentResource->getId(), $actual->getResourceId());
    }

    public function testFindByValue(): void
    {
        // Arrange
        $container = static::bootKernel()->getContainer();
        /**
         * @var TokenServiceInterface $service
         */
        $service = $container->get(TokenServiceInterface::class);
        /**
         * @var Registry $doctrine
         */
        $doctrine = $container->get('doctrine');
        $manager = $doctrine->getManager();
        $resource = new ResourceMock();
        $token = $service->create($resource, 'foo');

        $manager->clear();
        $value = $token->getValue();

        self::assertNotNull($value);

        // Act
        $actual = $service->findByValue($value);

        // Assert
        self::assertNotNull($actual);
        self::assertSame($token->getId(), $actual->getId());
        self::assertSame($token->getType(), $actual->getType());
        self::assertSame($token->getValue(), $actual->getValue());
        self::assertSame($token->getResourceType(), $actual->getResourceType());
        self::assertSame($token->getResourceId(), $actual->getResourceId());
    }

    public function testFindByResourceAndType(): void
    {
        // Arrange
        $container = static::bootKernel()->getContainer();
        /**
         * @var TokenServiceInterface $service
         */
        $service = $container->get(TokenServiceInterface::class);
        /**
         * @var Registry $doctrine
         */
        $doctrine = $container->get('doctrine');
        $manager = $doctrine->getManager();
        $resource = new ResourceMock();
        $token = $service->create($resource, 'bar');

        $manager->clear();

        // Act
        $actual = $service->findByResourceAndType($resource, 'bar');

        // Assert
        self::assertNotNull($actual);
        self::assertSame($token->getId(), $actual->getId());
        self::assertSame($token->getType(), $actual->getType());
        self::assertSame($token->getValue(), $actual->getValue());
        self::assertSame($token->getResourceType(), $actual->getResourceType());
        self::assertSame($token->getResourceId(), $actual->getResourceId());
    }

    public function testGetMissingTokenConfiguration(): void
    {
        // Assert
        $this->expectException(RuntimeException::class);

        // Arrange
        $container = static::bootKernel()->getContainer();
        /**
         * @var TokenServiceInterface $service
         */
        $service = $container->get(TokenServiceInterface::class);
        $resource = new ResourceMock();

        // Act
        $service->create($resource, 'baz');
    }

    public function testSaveAndRemove(): void
    {
        // Arrange
        $container = static::bootKernel()->getContainer();
        /**
         * @var TokenServiceInterface $service
         */
        $service = $container->get(TokenServiceInterface::class);
        /**
         * @var Registry $doctrine
         */
        $doctrine = $container->get('doctrine');
        $manager = $doctrine->getManager();
        $token = new Token();
        $token->setValue('foo');
        $token->setType('bar');
        $token->setResourceType('a');
        $token->setResourceId(1);

        // Act
        $service->save($token);

        // Assert
        self::assertTrue($manager->contains($token));
        self::assertNotNull($token->getId());
        self::assertNotNull($token->getCreatedAt());

        // Act
        $service->remove($token);

        // Assert
        self::assertFalse($manager->contains($token));
        self::assertNull($service->findByValue('foo'));
    }

    public function testValidateNull(): void
    {
        // Assert
        $this->expectException(RuntimeException::class);

        // Arrange
        $container = static::bootKernel()->getContainer();
        /**
         * @var TokenServiceInterface $service
         */
        $service = $container->get(TokenServiceInterface::class);

        // Act
        $service->validate(null);
    }

    public function testValidateExpired(): void
    {
        // Assert
        $this->expectException(RuntimeException::class);

        // Arrange
        $container = static::bootKernel()->getContainer();
        /**
         * @var TokenServiceInterface $service
         */
        $service = $container->get(TokenServiceInterface::class);

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
        $container = static::bootKernel()->getContainer();
        /**
         * @var TokenServiceInterface $service
         */
        $service = $container->get(TokenServiceInterface::class);

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
        $this->expectException(LogicException::class);

        // Arrange
        $container = static::bootKernel()->getContainer();
        /**
         * @var TokenServiceInterface $service
         */
        $service = $container->get(TokenServiceInterface::class);

        // Act
        $token = new Token();
        $token->setType('foo');

        $service->getExpiresAt($token);
    }

    public function testGetExpirationIntervalInvalidType(): void
    {
        // Assert
        $this->expectException(LogicException::class);

        // Arrange
        $container = static::bootKernel()->getContainer();
        /**
         * @var TokenServiceInterface $service
         */
        $service = $container->get(TokenServiceInterface::class);

        // Act
        $token = new Token();

        $service->getExpirationInterval($token);
    }
}
