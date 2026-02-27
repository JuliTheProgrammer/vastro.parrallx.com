<?php

namespace App\Helper;

use App\Enums\BackupClassificationEnum;

class DataClassificationHelper
{
    public static function identifyClassificationEnum(int $dataClassificationScore)
    {

        // TODO make exception
        if ($dataClassificationScore < 0 || $dataClassificationScore > 100) {
            return null;
        }

        if ($dataClassificationScore <= 20) {
            return BackupClassificationEnum::PUBLIC_DATA;
        }

        if ($dataClassificationScore <= 40) {
            return BackupClassificationEnum::INTERNAL_DATA;
        }

        if ($dataClassificationScore <= 80) {
            return BackupClassificationEnum::CONFIDENT_DATA;
        }

        return BackupClassificationEnum::RESTRICTED_DATA;

    }
}
