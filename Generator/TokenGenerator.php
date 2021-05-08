<?php

namespace SoureCode\Bundle\Token\Generator;

use SoureCode\Component\Common\Generator\RandomGeneratorInterface;

class TokenGenerator implements TokenGeneratorInterface
{
    protected RandomGeneratorInterface $randomGenerator;

    public function __construct(RandomGeneratorInterface $randomGenerator)
    {
        $this->randomGenerator = $randomGenerator;
    }

    public function generate(int $length): string
    {
        return $this->randomGenerator->generateString($length);
    }
}
