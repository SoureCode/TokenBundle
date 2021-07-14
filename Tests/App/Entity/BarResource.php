<?php

namespace SoureCode\Bundle\Token\Tests\App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use SoureCode\Bundle\Token\Domain\TokenAwareTrait;
use SoureCode\Bundle\Token\Model\TokenAwareInterface;

#[ORM\Entity]
class BarResource implements TokenAwareInterface
{
    use TokenAwareTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id = null;

    public function __construct(?int $id = null)
    {
        $this->id = $id;
        $this->tokens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }
}
