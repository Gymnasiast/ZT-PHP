<?php

declare(strict_types=1);

namespace ZTPHP\Terrain;

final class Shape
{
    public const NEAR_XY_CORNER_UP = 0b00000001;
    public const NEAR_XY_CORNER_UP_DOUBLE = 0b00000010;
    public const FAR_Y_CORNER_UP = 0b00000100;
    public const FAR_Y_CORNER_UP_DOUBLE = 0b00001000;
    public const FAR_X_CORNER_UP = 0b00010000;
    public const FAR_X_CORNER_UP_DOUBLE = 0b00100000;
    public const FAR_XY_CORNER_UP = 0b01000000;
    public const FAR_XY_CORNER_UP_DOUBLE = 0b10000000;
}
