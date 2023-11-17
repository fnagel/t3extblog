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
        'no_empty_phpdoc' => true,
        // Those are risky, check manually if enabled!
        // 'phpdoc_to_param_type' => true,
        // 'phpdoc_to_return_type' => true,
        // 'phpdoc_to_property_type' => true,
        // 'phpdoc_trim_consecutive_blank_line_separation' => true,
        // Removes @var annotations from Extbase models which is needed for TYPO3 v11 but seems almost ok for v12
        'no_superfluous_phpdoc_tags' => false,
    ]);

return $config;
