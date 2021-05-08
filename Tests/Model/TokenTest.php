<?php

namespace SoureCode\Bundle\Token\Tests\Model;

use PHPUnit\Framework\TestCase;
use ReflectionClass;
use SoureCode\Bundle\Token\Model\Token;

class TokenTest extends TestCase
{
    public function testGetId(): void
    {
        // Arrange
        $token = new Token();

        // Act and Assert
        self::assertNull($token->getId());

        $reflectionClass = new ReflectionClass($token);
        $idProperty = $reflectionClass->getProperty('id');
        $idProperty->setAccessible(true);
        $idProperty->setValue($token, 8);

        self::assertSame(8, $token->getId());
    }

    public function testGetSetType(): void
    {
        // Arrange
        $token = new Token();

        // Act and Assert
        self::assertNull($token->getType());
        $token->setType('foo');
        self::assertSame('foo', $token->getType());
    }

    public function testGetSetValue(): void
    {
        // Arrange
        $token = new Token();

        // Act and Assert
        self::assertNull($token->getValue());
        $token->setValue('foo');
        self::assertSame('foo', $token->getValue());
    }

    public function testGetSetData(): void
    {
        // Arrange
        $token = new Token();

        // Act and Assert
        self::assertNull($token->getData());
        $token->setData('bar');
        self::assertSame('bar', $token->getData());
    }

    public function testGetSetResourceType(): void
    {
        // Arrange
        $token = new Token();

        // Act and Assert
        self::assertNull($token->getResourceType());
        $token->setResourceType('foo');
        self::assertSame('foo', $token->getResourceType());
    }

    public function testGetSetResourceId(): void
    {
        // Arrange
        $token = new Token();

        // Act and Assert
        self::assertNull($token->getResourceId());
        $token->setResourceId(5);
        self::assertSame(5, $token->getResourceId());
    }
}
