<?php

declare(strict_types=1);

namespace ZTPHP\Zoofile;

class Exhibit
{
    public function __construct(
        public readonly string $name,
        public readonly int $tankHeight,
        public readonly bool $tankFilled,
    ) {
    }
}
