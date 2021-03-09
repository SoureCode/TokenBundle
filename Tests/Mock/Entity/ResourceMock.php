<?php

namespace SoureCode\Bundle\Token\Tests\Mock\Entity;

use Doctrine\ORM\Mapping as ORM;
use SoureCode\Component\Common\Model\ResourceInterface;

/**
 * @ORM\Entity()
 */
class ResourceMock implements ResourceInterface
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
}
