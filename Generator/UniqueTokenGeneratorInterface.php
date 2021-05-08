<?php

namespace SoureCode\Bundle\Token\Generator;

interface UniqueTokenGeneratorInterface
{
    public function generate(int $length): string;
}
