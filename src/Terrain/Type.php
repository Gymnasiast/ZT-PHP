<?php

declare(strict_types=1);

namespace ZTPHP\Terrain;

enum Type: int
{
    case GRASS = 0;
    case SAVANNAH_GRASS = 1;
    case SAND = 2;
    case DIRT = 3;
    case RAINFOREST_FLOOR = 4;
    case BROWN_STONE = 5;
    case GRAY_STONE = 6;
    case GRAVEL = 7;
    case SNOW = 8;
    case FRESH_WATER = 9;
    case SALT_WATER = 10;
    case DECIDEOUS_FLOOR = 11;
    case WATERFALL = 12;
    case CONIFEROUS_FLOOR = 13;
    case CONCRETE = 14;
    case ASPHALT = 15;
    case TRAMPLED_TERRAIN = 16;
    case GUNITE = 17;

    public const DESCRIPTIONS = [
        'Grass',
        'Savannah grass',
        'Sand',
        'Dirt',
        'Rainforest floor',
        'Brown stone',
        'Gray stone',
        'Gravel',
        'Snow',
        'Fresh water',
        'Salt water',
        'Decideous floor',
        'Waterfall',
        'Coniferous floor',
        'Concrete',
        'Asphalt',
        'Trampled terrain',
        'Gunite',
    ];

    public function getDescription(): string
    {
        return self::DESCRIPTIONS[$this->value];
    }
}
