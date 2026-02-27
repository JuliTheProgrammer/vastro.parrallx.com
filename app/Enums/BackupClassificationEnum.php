<?php

namespace App\Enums;

enum BackupClassificationEnum: string
{
    case PUBLIC_DATA = 'public';
    case INTERNAL_DATA = 'internal';
    case CONFIDENT_DATA = 'confidential';
    case RESTRICTED_DATA = 'restricted';

    public function label(): string
    {
        return match ($this) {
            self::PUBLIC_DATA => 'Public Data',
            self::INTERNAL_DATA => 'Internal Data',
            self::CONFIDENT_DATA => 'Confidential Data',
            self::RESTRICTED_DATA => 'Restricted Data',
        };
    }
}
