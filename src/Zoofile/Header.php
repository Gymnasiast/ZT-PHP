<?php

declare(strict_types=1);

namespace ZTPHP\Zoofile;

final class Header
{
    public function __construct(
        public readonly int $mapSizeX,
        public readonly int $mapSizeY,
    ) {
    }
}
