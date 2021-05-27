<?php

namespace SoureCode\Bundle\Token\Tests\App\Entity;

use Doctrine\ORM\Mapping as ORM;
use SoureCode\Bundle\Token\Model\TokenAwareInterface;

/**
 * @ORM\Entity()
 */
class FooResource implements TokenAwareInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    protected ?int $id;

    public function __construct(?int $id = null)
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getObjectIdentifier(): string
    {
        return (string) $this->id;
    }
}
