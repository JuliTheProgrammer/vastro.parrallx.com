<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackupAnalysis extends Model
{
    public function backup()
    {
        return $this->belongsTo(Backup::class);
    }

    protected $casts = [
        'tags' => 'array',
    ];
}
