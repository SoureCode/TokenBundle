<?php

namespace SoureCode\Bundle\Token\Model;

interface TokenAwareInterface
{
    public function getObjectIdentifier(): string;
}
