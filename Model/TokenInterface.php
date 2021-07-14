<?php

namespace SoureCode\Bundle\Token\Model;

use SoureCode\Component\Common\Model\CreatedAtInterface;
use Symfony\Component\Uid\Uuid;

interface TokenInterface extends CreatedAtInterface
{
    public function getId(): ?Uuid;

    public function getType(): ?string;

    public function setType(?string $type): void;

    public function getData(): ?string;

    public function setData(?string $data): void;
}
