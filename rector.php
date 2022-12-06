<?php

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\LevelSetList;

return static function (RectorConfig $rectorConfig){
    $rectorConfig->paths([
        __DIR__ . '/demo',
        __DIR__ . '/src',
    ]);

    // define sets of rules
        $rectorConfig->sets([
            LevelSetList::UP_TO_PHP_81,
        ]);
};
