<?php

namespace App\Http\Controllers\StorageClass;

use App\Http\Controllers\Controller;
use App\Models\StorageClass;

class StorageClassController extends Controller
{
    public function __invoke()
    {
        return StorageClass::all();
    }
}
