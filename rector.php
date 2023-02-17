<?php

declare(strict_types=1);

use Rector\CodeQuality\Rector\FuncCall\SimplifyRegexPatternRector;
use Rector\CodeQuality\Rector\If_\ExplicitBoolCompareRector;
use Rector\CodingStyle\Rector\FuncCall\ConsistentPregDelimiterRector;
use Rector\CodingStyle\Rector\FuncCall\CountArrayToEmptyArrayComparisonRector;
use Rector\CodingStyle\Rector\String_\SymplifyQuoteEscapeRector;
use Rector\Core\ValueObject\PhpVersion;
use Rector\Php74\Rector\LNumber\AddLiteralSeparatorToNumberRector;
use Rector\Php80\Rector\Catch_\RemoveUnusedVariableInCatchRector;
use Rector\Set\ValueObject\SetList;
use Rector\Config\RectorConfig;
use Ssch\TYPO3Rector\Configuration\Typo3Option;
use Ssch\TYPO3Rector\Set\Typo3LevelSetList;

// See https://github.com/sabbelasichon/typo3-rector/tree/main/docs
return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->sets([
        Typo3LevelSetList::UP_TO_TYPO3_11
    ]);

    $rectorConfig->import(SetList::CODING_STYLE);
    $rectorConfig->import(SetList::CODE_QUALITY);

    $rectorConfig->import(SetList::PHP_52);
    $rectorConfig->import(SetList::PHP_53);
    $rectorConfig->import(SetList::PHP_54);
    $rectorConfig->import(SetList::PHP_55);
    $rectorConfig->import(SetList::PHP_56);
    $rectorConfig->import(SetList::PHP_70);
    $rectorConfig->import(SetList::PHP_71);
    $rectorConfig->import(SetList::PHP_72);
    $rectorConfig->import(SetList::PHP_73);
    $rectorConfig->import(SetList::PHP_74);

    // Define your target version which you want to support
    $rectorConfig->phpVersion(PhpVersion::PHP_74);

    $rectorConfig->skip([
        ExplicitBoolCompareRector::class,
        SimplifyRegexPatternRector::class,
        ConsistentPregDelimiterRector::class,
        RemoveUnusedVariableInCatchRector::class,
        CountArrayToEmptyArrayComparisonRector::class,
        AddLiteralSeparatorToNumberRector::class,
        SymplifyQuoteEscapeRector::class,
    ]);

    // If you only want to process one/some TYPO3 extension(s), you can specify its path(s) here.
    // If you use the option --config change __DIR__ to getcwd()
    $rectorConfig->paths([
        __DIR__,
    ]);

    // In order to have a better analysis from phpstan we teach it here some more things
    $rectorConfig->phpstanConfig(Typo3Option::PHPSTAN_FOR_RECTOR_PATH);

    // FQN classes are not imported by default. If you don't do it manually after every Rector run, enable it by:
    $rectorConfig->importNames();

    // If you use importNames(), you should consider excluding some TYPO3 files.
    $rectorConfig->skip([
        __DIR__ . '/.github/*',
        __DIR__ . '/.Build/*',
    ]);
};
