<?php

namespace SoureCode\Bundle\Token\Checker;

interface TokenUniqueCheckerInterface
{
    public function isUnique(string $value): bool;
}
