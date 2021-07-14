<?php

namespace SoureCode\Bundle\Token\Model;

use Doctrine\Common\Collections\Collection;

interface TokenAwareInterface
{
    public function addToken(TokenInterface $token): self;

    public function removeToken(TokenInterface $token): self;

    /**
     * @return Collection<int, TokenInterface>
     */
    public function getTokens(): Collection;
}
