<?php

namespace SoureCode\Bundle\Token\Domain;

use SoureCode\Bundle\Token\Model\TokenInterface;
use SoureCode\Component\Common\Domain\CreatedAtTrait;
use Symfony\Component\Uid\Uuid;

class Token implements TokenInterface
{
    use CreatedAtTrait;

    protected ?Uuid $id = null;

    protected ?string $type = null;

    protected ?string $data = null;

    public function getId(): ?Uuid
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
}
