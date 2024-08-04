<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\ClassMethod\RemoveEmptyClassMethodRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPaths([
        __DIR__.'/src',
        __DIR__.'/tests/unit',
        __DIR__.'/tests/helper',
    ])
    ->withSets([
        SetList::DEAD_CODE,
        SetList::PRIVATIZATION,
        SetList::TYPE_DECLARATION,
        SetList::CODE_QUALITY,
        LevelSetList::UP_TO_PHP_82,
        PHPUnitSetList::PHPUNIT_100,
    ])
    ->withImportNames(importShortClasses: false)
    ->withPhpVersion(PhpVersion::PHP_82)
    ->withPHPStanConfigs([__DIR__.'/rector-phpstan.neon'])
    ->withCache(__DIR__.'/var/tmp/rector')
    ->withSkip([
        __DIR__.'/tests/unit/Test/Formatter/SimpleTestCaseFormatterTest.php',
        RemoveEmptyClassMethodRector::class => [
            __DIR__.'/tests/unit/Test/SimpleTestSuiteDocumentorTest.php',
        ],
    ]);
