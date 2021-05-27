<?php

namespace SoureCode\Bundle\Token\Model;

use SoureCode\Component\Common\Model\CreatedAtInterface;
use Symfony\Component\Uid\UuidV4;

interface TokenInterface extends CreatedAtInterface
{
    public function getId(): ?UuidV4;

    public function getType(): ?string;

    public function setType(?string $type): void;

    public function getData(): ?string;

    public function setData(?string $data): void;

    public function getResourceType(): ?string;

    public function setResourceType(?string $type): void;

    public function getResourceId(): ?int;

    /**
     * @param mixed|null $id
     */
    public function setResourceId($id): void;
}
