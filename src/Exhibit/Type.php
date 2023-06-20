<?php

declare(strict_types=1);

namespace ZTPHP\Exhibit;

enum Type: int
{
    case REGULAR = 0;
    case TANK = 0x10000;
    case SHOW_TANK = 0x1010000;
}
