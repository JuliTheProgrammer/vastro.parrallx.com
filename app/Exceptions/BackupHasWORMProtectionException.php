<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BackupHasWORMProtectionException extends Exception
{
    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request)
    {
        return redirect(route('backups.index'))->with([
            'error' => 'Cannot Delete Backup; protected.',
        ]);
    }
}
