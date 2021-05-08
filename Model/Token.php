<?php

namespace SoureCode\Bundle\Token\Model;

use SoureCode\Component\Common\Model\TimestampableTrait;

class Token implements TokenInterface
{
    use TimestampableTrait;

    protected ?int $id = null;

    protected ?string $type = null;

    protected ?string $value = null;

    protected ?string $data = null;

    protected ?string $resourceType = null;

    protected ?int $resourceId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(?string $value): void
    {
        $this->value = $value;
    }

    public function getData(): ?string
    {
        return $this->data;
    }

    public function setData(?string $data): void
    {
        $this->data = $data;
    }

    public function getResourceType(): ?string
    {
        return $this->resourceType;
    }

    public function setResourceType(?string $type): void
    {
        $this->resourceType = $type;
    }

    public function getResourceId(): ?int
    {
        return $this->resourceId;
    }

    public function setResourceId(?int $id): void
    {
        $this->resourceId = $id;
    }
}
