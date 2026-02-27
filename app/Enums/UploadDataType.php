<?php

namespace App\Enums;

enum UploadDataType
{
    case FILE;
    case IMAGE;
    case VIDEO;
    case AUDIO;
    case ARCHIVE;
    case CODE;
    case OTHER;
}
