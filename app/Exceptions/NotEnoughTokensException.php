<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class NotEnoughTokensException extends Exception
{
    /**
     * Report the exception.
     */
    public function report(): void
    {
        //
    }

    /**
     * Render the exception as an HTTP response.
     */
    public function render(Request $request)
    {
        return back()->with(['error', $this->getMessage() ?: 'Not enough tokens.']);
    }
}
