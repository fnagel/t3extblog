<?php

$config = new PhpCsFixer\Config();
$config
    ->setRiskyAllowed(true)
    ->setRules([
        '@PSR2' => true,
        'no_unused_imports' => true,
        'array_syntax' => [
            'syntax' => 'short',
        ],
        // PHPdocs / type hinting
        'no_superfluous_phpdoc_tags' => true,
        'no_empty_phpdoc' => true,
        // Those are risky, check manually if enabled!
        // 'phpdoc_to_param_type' => true,
        // 'phpdoc_to_return_type' => true,
    ]);

return $config;
