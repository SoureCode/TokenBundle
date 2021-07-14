<?php

namespace SoureCode\Bundle\Token\Service;

use function array_key_exists;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Doctrine\Persistence\ObjectManager;
use function is_string;
use SoureCode\Bundle\Token\Domain\Token;
use SoureCode\Bundle\Token\Exception\InvalidArgumentException;
use SoureCode\Bundle\Token\Exception\RuntimeException;
use SoureCode\Bundle\Token\Model\TokenInterface;
use SoureCode\Bundle\Token\Repository\TokenRepository;
use Symfony\Component\Uid\Uuid;

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

    public function create(string $type, ?string $data = null): TokenInterface
    {
        // Ensure this token type is configured
        $this->getTokenConfiguration($type);

        $token = new Token();
        $token->setType($type);
        $token->setData($data);

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

    public function find(string | Uuid $id): ?TokenInterface
    {
        if (is_string($id)) {
            $id = Uuid::fromString($id);
        }

        return $this->repository->find($id);
    }

    public function getRepository(): TokenRepository
    {
        return $this->repository;
    }
}
