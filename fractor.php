<?php

use a9f\Fractor\Configuration\FractorConfiguration;
use a9f\Fractor\ValueObject\Indent;
use a9f\FractorXliff\Configuration\XliffProcessorOption;
use a9f\FractorXml\Configuration\XmlProcessorOption;
use a9f\Typo3Fractor\Set\Typo3LevelSetList;

// See https://github.com/andreaswolf/fractor-typo3-fractor/blob/main/docs/typo3-fractor-rules.md
return FractorConfiguration::configure()
    ->withPaths([__DIR__])
    ->withSkip([
        __DIR__ . '/.github',
        __DIR__ . '/.Build',
        __DIR__ . '/var',
    ])
    ->withSets([
        Typo3LevelSetList::UP_TO_TYPO3_14
    ])
    ->withOptions([
        XmlProcessorOption::INDENT_CHARACTER => Indent::STYLE_TAB,
        XmlProcessorOption::INDENT_SIZE => 1,
        XliffProcessorOption::INDENT_CHARACTER => Indent::STYLE_TAB,
        XliffProcessorOption::INDENT_SIZE => 1,
    ]);
