<?php

namespace SoureCode\Bundle\Token\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use function get_class;
use SoureCode\Bundle\Token\Domain\Token;
use SoureCode\Bundle\Token\Model\TokenAwareInterface;
use SoureCode\Bundle\Token\Model\TokenInterface;

/**
 * @extends ServiceEntityRepository<TokenInterface>
 */
class TokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Token::class);
    }

    public function findByResourceAndType(TokenAwareInterface $resource, string $type): ?TokenInterface
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->where('t.resourceType = :resourceType')
            ->andWhere('t.resourceId = :resourceId')
            ->andWhere('t.type = :type')
            ->setParameter('resourceType', get_class($resource))
            ->setParameter('resourceId', $resource->getObjectIdentifier())
            ->setParameter('type', $type);

        $query = $queryBuilder->getQuery();

        return $query->getOneOrNullResult();
    }
}
