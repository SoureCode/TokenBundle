<?php

namespace SoureCode\Bundle\Token\Service;

use DateInterval;
use DateTime;
use SoureCode\Bundle\Token\Model\TokenAwareInterface;
use SoureCode\Bundle\Token\Model\TokenInterface;

interface TokenServiceInterface
{
    public function create(TokenAwareInterface $resource, string $type): TokenInterface;

    public function save(TokenInterface $token): void;

    public function remove(TokenInterface $token): void;

    public function findByResourceAndType(TokenAwareInterface $resource, string $type): ?TokenInterface;

    public function find(string $id): ?TokenInterface;

    public function isExpired(TokenInterface $token): bool;

    public function validate(?TokenInterface $token): void;

    public function getExpiresAt(TokenInterface $token): DateTime;

    public function getExpirationInterval(TokenInterface $token): DateInterval;
}
