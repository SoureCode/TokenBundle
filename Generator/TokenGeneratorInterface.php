<?php

namespace SoureCode\Bundle\Token\Generator;

interface TokenGeneratorInterface
{
    public function generate(int $length): string;
}
