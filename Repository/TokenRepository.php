<?php

namespace SoureCode\Bundle\Token\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SoureCode\Bundle\Token\Domain\Token;
use SoureCode\Bundle\Token\Model\TokenInterface;

/**
 * @template-extends ServiceEntityRepository<TokenInterface>
 */
class TokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Token::class);
    }
}
