<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InvalidBackupTypeException extends Exception
{
    public function render(Request $request): RedirectResponse
    {
        return back()->with('error', $this->getMessage() ?: 'AI cannot analyse this backup type.');
    }
}
