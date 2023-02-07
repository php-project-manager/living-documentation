<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\Config\RectorConfig;
use Rector\Core\ValueObject\PhpVersion;
use Rector\DeadCode\Rector\ClassMethod\RemoveEmptyClassMethodRector;
use Rector\DeadCode\Rector\MethodCall\RemoveEmptyMethodCallRector;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__.'/src',
        __DIR__.'/tests/unit',
        __DIR__.'/tests/helper',
    ]);

    $rectorConfig->importNames();
    $rectorConfig->importShortClasses(false);
    $rectorConfig->phpVersion(PhpVersion::PHP_82);
    $rectorConfig->phpstanConfig(__DIR__.'/phpstan.neon');

    $rectorConfig->sets([
        SetList::DEAD_CODE,
        SetList::PRIVATIZATION,
        SetList::TYPE_DECLARATION,
        SetList::CODE_QUALITY,
        LevelSetList::UP_TO_PHP_82,
        PHPUnitSetList::PHPUNIT_100,
    ]);

    $rectorConfig->skip([
        __DIR__.'/tests/unit/Test/Formatter/SimpleTestCaseFormatterTest.php',
        RemoveEmptyClassMethodRector::class => [
            __DIR__.'/tests/unit/Test/SimpleTestSuiteDocumentorTest.php',
        ],
    ]);

    $rectorConfig->cacheClass(FileCacheStorage::class);
    $rectorConfig->cacheDirectory(__DIR__.'/var/tmp/rector');
};
