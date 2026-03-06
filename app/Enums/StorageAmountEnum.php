<?php

namespace App\Enums;

enum StorageAmountEnum: int
{
    case MEGABYTE = 1048576;
    case GIGABYTE = 1073741824;
    case TERABYTE = 1099511627776;

}
