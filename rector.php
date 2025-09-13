<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__.'/app',
        __DIR__.'/config',
        __DIR__.'/routes',
    ]);

    // Skip files/directories
    $rectorConfig->skip([
        __DIR__.'/app/Console/Kernel.php',
        __DIR__.'/app/Exceptions/Handler.php',
        __DIR__.'/database/migrations',
    ]);

    // PHP version upgrade sets
    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_82,
        SetList::CODE_QUALITY,
        SetList::DEAD_CODE,
        SetList::EARLY_RETURN,
        SetList::TYPE_DECLARATION,
    ]);
};
