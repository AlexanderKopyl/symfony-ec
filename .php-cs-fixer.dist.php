<?php

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__.'/src',
        __DIR__.'/tests',
        __DIR__.'/config',
    ])
    ->exclude([
        'var',
        'vendor',
        'public/bundles',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new PhpCsFixer\Config())
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setUsingCache(true)
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHP8x3Migration' => true,
        'declare_strict_types' => true,
        'strict_param' => true,
        'ordered_imports' => ['imports_order' => ['class', 'function', 'const']],
        'no_unused_imports' => true,
        'single_quote' => true,
        'binary_operator_spaces' => ['default' => 'align_single_space_minimal'],
        'array_syntax' => ['syntax' => 'short'],
        'yoda_style' => false,
        'native_function_invocation' => false,
        'phpdoc_to_comment' => false,
        'phpdoc_align' => ['align' => 'left'],
        'no_superfluous_phpdoc_tags' => false,
    ]);
