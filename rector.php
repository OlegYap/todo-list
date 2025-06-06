<?php

use Rector\CodingStyle\Rector\Use_\SeparateMultiUseImportsRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\Closure\AddClosureVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\Function_\AddFunctionVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;
use RectorLaravel\Set\LaravelLevelSetList;
use RectorLaravel\Set\LaravelSetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__.'/app',
        __DIR__.'/config',
        __DIR__.'/database/factories',
        __DIR__.'/database/seeders',
        __DIR__.'/routes',
        __DIR__.'/tests/Feature',
    ]);

    $rectorConfig->skip([
        __DIR__.'/vendor',
        __DIR__.'/bootstrap/cache',
        __DIR__.'/storage',
        '*_ide_helper.php',
        '*/migrations/*',
        '*/stubs/*',
        'tests/Unit/*',
    ]);

    $rectorConfig->importNames();
    $rectorConfig->importShortClasses(false);
    $rectorConfig->removeUnusedImports();

    $rectorConfig->rules([
        TypedPropertyFromStrictConstructorRector::class,
        SeparateMultiUseImportsRector::class,
        AddVoidReturnTypeWhereNoReturnRector::class,
        AddClosureVoidReturnTypeWhereNoReturnRector::class,
        AddFunctionVoidReturnTypeWhereNoReturnRector::class,
    ]);

    $rectorConfig->sets([
        LaravelLevelSetList::UP_TO_LARAVEL_120,
        LaravelSetList::LARAVEL_CODE_QUALITY,
        LaravelSetList::LARAVEL_STATIC_TO_INJECTION,
        LaravelSetList::LARAVEL_ARRAY_STR_FUNCTION_TO_STATIC_CALL,
        LaravelSetList::LARAVEL_LEGACY_FACTORIES_TO_CLASSES,
        SetList::PHP_82,
        SetList::TYPE_DECLARATION,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::PRIVATIZATION,
        SetList::EARLY_RETURN,
    ]);

    $rectorConfig->parallel();
    $rectorConfig->indent(' ', 4);
    $rectorConfig->fileExtensions(['php']);
};
