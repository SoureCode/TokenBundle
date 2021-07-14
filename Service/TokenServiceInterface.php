<?php

namespace SoureCode\Bundle\Token\Service;

use DateInterval;
use DateTime;
use SoureCode\Bundle\Token\Model\TokenInterface;
use SoureCode\Bundle\Token\Repository\TokenRepository;
use Symfony\Component\Uid\Uuid;

interface TokenServiceInterface
{
    public function getRepository(): TokenRepository;

    public function create(string $type, ?string $data = null): TokenInterface;

    public function save(TokenInterface $token): void;

    public function remove(TokenInterface $token): void;

    public function find(string | Uuid $id): ?TokenInterface;

    public function isExpired(TokenInterface $token): bool;

    public function validate(?TokenInterface $token): void;

    public function getExpiresAt(TokenInterface $token): DateTime;

    public function getExpirationInterval(TokenInterface $token): DateInterval;
}
