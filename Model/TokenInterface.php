<?php

namespace SoureCode\Bundle\Token\Model;

use SoureCode\Component\Common\Model\TimestampableInterface;

interface TokenInterface extends TimestampableInterface
{
    public function getId(): ?int;

    public function getType(): ?string;

    public function setType(?string $type): void;

    public function getValue(): ?string;

    public function setValue(?string $value): void;

    public function getData(): ?string;

    public function setData(?string $data): void;

    public function getResourceType(): ?string;

    public function setResourceType(?string $type): void;

    public function getResourceId(): ?int;

    public function setResourceId(?int $id): void;
}
