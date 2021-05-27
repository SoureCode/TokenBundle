<?php

namespace SoureCode\Bundle\Token\Domain;

use SoureCode\Bundle\Token\Model\TokenInterface;
use SoureCode\Component\Common\Domain\CreatedAtTrait;
use Symfony\Component\Uid\UuidV4;

class Token implements TokenInterface
{
    use CreatedAtTrait;

    protected ?UuidV4 $id = null;

    protected ?string $type = null;

    protected ?string $data = null;

    protected ?string $resourceType = null;

    protected ?int $resourceId = null;

    public function getId(): ?UuidV4
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

    /**
     * {@inheritDoc}
     */
    public function setResourceId($id): void
    {
        $this->resourceId = $id;
    }
}
