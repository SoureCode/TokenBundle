<?php

namespace SoureCode\Bundle\Token\Service;

use SoureCode\Bundle\Token\Exception\InvalidArgumentException;
use function array_key_exists;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Doctrine\Persistence\ObjectManager;
use function get_class;
use SoureCode\Bundle\Token\Domain\Token;
use SoureCode\Bundle\Token\Exception\LogicException;
use SoureCode\Bundle\Token\Exception\RuntimeException;
use SoureCode\Bundle\Token\Model\TokenAwareInterface;
use SoureCode\Bundle\Token\Model\TokenInterface;
use SoureCode\Bundle\Token\Repository\TokenRepository;
use Symfony\Component\Uid\UuidV4;

class TokenService implements TokenServiceInterface
{
    protected TokenRepository $repository;

    protected ObjectManager $manager;

    protected array $tokenConfiguration;

    public function __construct(
        ObjectManager $manager,
        TokenRepository $repository,
        array $tokenConfiguration,
    ) {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->tokenConfiguration = $tokenConfiguration;
    }

    public function create(TokenAwareInterface $resource, string $type): TokenInterface
    {
        // Ensure this token type is configured
        $this->getTokenConfiguration($type);

        $resourceId = $resource->getObjectIdentifier();

        if (!$resourceId) {
            throw new InvalidArgumentException('Resource is not persisted.');
        }

        $token = new Token();
        $token->setType($type);
        $token->setResourceType(get_class($resource));
        $token->setResourceId($resourceId);

        $this->manager->persist($token);
        $this->manager->flush();

        return $token;
    }

    public function getTokenConfiguration(string $type): array
    {
        if (!array_key_exists($type, $this->tokenConfiguration)) {
            throw new RuntimeException(sprintf('Missing token configuration for type "%s"', $type));
        }

        return $this->tokenConfiguration[$type];
    }

    public function save(TokenInterface $token): void
    {
        $this->manager->persist($token);
        $this->manager->flush();
    }

    public function remove(TokenInterface $token): void
    {
        $this->manager->remove($token);
        $this->manager->flush();
    }

    public function validate(?TokenInterface $token): void
    {
        if (null === $token) {
            throw new InvalidArgumentException('Token is null.');
        }

        if ($this->isExpired($token)) {
            throw new RuntimeException('Token expired.');
        }
    }

    public function isExpired(TokenInterface $token): bool
    {
        $expireAt = $this->getExpiresAt($token);
        $now = new DateTime('now');

        return $expireAt < $now;
    }

    public function getExpiresAt(TokenInterface $token): DateTime
    {
        $createdAt = $token->getCreatedAt();

        if (null === $createdAt) {
            throw new InvalidArgumentException('CreatedAt timestamp missing for token, might be not persisted yet.');
        }

        $interval = $this->getExpirationInterval($token);
        $datetime = DateTime::createFromFormat(
            DateTimeInterface::ATOM,
            $createdAt->format(DateTimeInterface::ATOM)
        );

        if (!$datetime) {
            throw new RuntimeException('Could not clone datetime.');
        }

        return $datetime->add($interval);
    }

    public function getExpirationInterval(TokenInterface $token): DateInterval
    {
        $type = $token->getType();

        if (null === $type) {
            throw new InvalidArgumentException('Invalid token type.');
        }

        $config = $this->getTokenConfiguration($type);

        return new DateInterval($config['expiration']);
    }

    public function findByResourceAndType(TokenAwareInterface $resource, string $type): ?TokenInterface
    {
        return $this->repository->findByResourceAndType($resource, $type);
    }

    public function find(string $id): ?TokenInterface
    {
        $uuid = UuidV4::fromString($id);

        return $this->repository->find($uuid);
    }
}
