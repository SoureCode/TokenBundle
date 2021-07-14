<?php

namespace SoureCode\Bundle\Token\Tests\App\Entity;

use Doctrine\ORM\Mapping as ORM;
use SoureCode\Bundle\Token\Domain\Token;
use SoureCode\Bundle\Token\Model\TokenInterface;

#[ORM\Entity]
class FooResource
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id = null;

    #[ORM\OneToOne(targetEntity: Token::class, cascade: ['persist'], orphanRemoval: true)]
    protected ?TokenInterface $activationToken = null;

    public function __construct(?int $id = null)
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActivationToken(): ?TokenInterface
    {
        return $this->activationToken;
    }

    public function setActivationToken(?TokenInterface $activationToken): self
    {
        $this->activationToken = $activationToken;

        return $this;
    }
}
