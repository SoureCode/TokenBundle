<?php

namespace SoureCode\Bundle\Token\Service;

use DateInterval;
use DateTime;
use SoureCode\Bundle\Token\Model\TokenInterface;
use SoureCode\Component\Common\Model\ResourceInterface;

interface TokenServiceInterface
{
    public function create(ResourceInterface $resource, string $type): TokenInterface;

    public function save(TokenInterface $token): void;

    public function remove(TokenInterface $token): void;

    public function findByResourceAndType(ResourceInterface $resource, string $type): ?TokenInterface;

    public function findByValue(string $value): ?TokenInterface;

    public function isExpired(TokenInterface $token): bool;

    public function validate(?TokenInterface $token): void;

    public function getExpiresAt(TokenInterface $token): DateTime;

    public function getExpirationInterval(TokenInterface $token): DateInterval;
}
