<?php

namespace SoureCode\Bundle\Token\Domain;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use SoureCode\Bundle\Token\Model\TokenInterface;

trait TokenAwareTrait
{
    #[ORM\ManyToMany(targetEntity: Token::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\JoinTable()]
    #[ORM\JoinColumn(name: 'joined_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'token_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Collection $tokens;

    public function addToken(TokenInterface $token): self
    {
        if (!$this->tokens->contains($token)) {
            $this->tokens->add($token);
        }

        return $this;
    }

    public function removeToken(TokenInterface $token): self
    {
        if ($this->tokens->contains($token)) {
            $this->tokens->removeElement($token);
        }

        return $this;
    }

    /**
     * @return Collection<int, TokenInterface>|TokenInterface[]
     */
    public function getTokens(): Collection
    {
        return $this->tokens;
    }
}
