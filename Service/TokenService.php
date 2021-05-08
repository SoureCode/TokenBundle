<?php

namespace SoureCode\Bundle\Token\Service;

use function array_key_exists;
use DateInterval;
use DateTime;
use DateTimeInterface;
use Doctrine\Persistence\ObjectManager;
use function get_class;
use SoureCode\Bundle\Token\Exception\LogicException;
use SoureCode\Bundle\Token\Exception\RuntimeException;
use SoureCode\Bundle\Token\Generator\UniqueTokenGeneratorInterface;
use SoureCode\Bundle\Token\Model\Token;
use SoureCode\Bundle\Token\Model\TokenInterface;
use SoureCode\Bundle\Token\Repository\TokenRepository;
use SoureCode\Component\Common\Model\ResourceInterface;

class TokenService implements TokenServiceInterface
{
    protected TokenRepository $repository;

    protected UniqueTokenGeneratorInterface $generator;

    protected ObjectManager $manager;

    protected array $tokenConfiguration;

    public function __construct(
        ObjectManager $manager,
        TokenRepository $repository,
        UniqueTokenGeneratorInterface $generator,
        array $tokenConfiguration,
    ) {
        $this->repository = $repository;
        $this->generator = $generator;
        $this->manager = $manager;
        $this->tokenConfiguration = $tokenConfiguration;
    }

    public function create(ResourceInterface $resource, string $type): TokenInterface
    {
        $config = $this->getTokenConfiguration($type);
        $value = $this->generator->generate($config['length']);

        if (!$this->manager->contains($resource)) {
            $this->manager->persist($resource);
        }

        $token = new Token();
        $token->setType($type);
        $token->setValue($value);
        $token->setResourceType(get_class($resource));
        $token->setResourceId($resource->getId());

        $this->manager->persist($token);
        $this->manager->flush();

        return $token;
    }

    protected function getTokenConfiguration(string $type): array
    {
        if (!array_key_exists($type, $this->tokenConfiguration)) {
            throw new RuntimeException(sprintf('Missing token configuration for type "%s"', $type));
        }

        return $this->tokenConfiguration[$type];
    }

    public function findByResourceAndType(ResourceInterface $resource, string $type): ?TokenInterface
    {
        $queryBuilder = $this->repository->createQueryBuilder('t')
            ->where('t.resourceType = :resourceType')
            ->andWhere('t.resourceId = :resourceId')
            ->andWhere('t.type = :type')
            ->setParameter('resourceType', get_class($resource))
            ->setParameter('resourceId', $resource->getId())
            ->setParameter('type', $type);

        $query = $queryBuilder->getQuery();

        return $query->getOneOrNullResult();
    }

    public function findByValue(string $value): ?TokenInterface
    {
        return $this->repository->findOneBy(['value' => $value]);
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
            throw new RuntimeException('Token not found.');
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
            throw new LogicException('CreatedAt timestamp missing for token, might be not persisted yet.');
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
            throw new LogicException('Invalid token type.');
        }

        $config = $this->getTokenConfiguration($type);

        return new DateInterval($config['expiration']);
    }
}
