<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BackupTooLargeToAnalyseException extends Exception
{
    public function render(Request $request): RedirectResponse
    {
        return back()->with('error', $this->getMessage() ?: 'Backup too large to analyse.');
    }
}
