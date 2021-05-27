<?php

$finder = PhpCsFixer\Finder::create()
    ->in(
        [
            '.',
        ]
    )
    ->exclude(['vendor', '.github'])
    ->name('*.php')
;

$config = new PhpCsFixer\Config();

return $config->setRules(
    [
        '@Symfony' => true,
        '@Symfony:risky' => true,
        'array_syntax' => ['syntax' => 'short'],
        'fopen_flags' => false,
        'protected_to_private' => false,
        'native_constant_invocation' => true,
        'combine_nested_dirname' => true,
        'fully_qualified_strict_types' => true,
        'global_namespace_import' => [
            'import_classes' => true,
            'import_constants' => true,
            'import_functions' => true,
        ],
    ]
)
    ->setRiskyAllowed(true)
    ->setFinder($finder);
