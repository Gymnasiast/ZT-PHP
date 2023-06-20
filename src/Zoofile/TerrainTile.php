<?php

declare(strict_types=1);

namespace ZTPHP\Zoofile;

use ZTPHP\Terrain\Type as TerrainType;

final class TerrainTile
{
    public function __construct(
        public readonly int $height,
        public readonly int $shape,
        public readonly TerrainType $type,
        public readonly int $unk,
    ) {
    }
}
