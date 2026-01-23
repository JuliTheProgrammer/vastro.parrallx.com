<?php

namespace App\Http\Controllers\Audit;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AuditController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $userId = $request->user()?->id;
        $auditLogs = DB::table('activity_log')
            ->where('causer_type', User::class)
            ->where('causer_id', $userId)
            ->latest()
            ->get();

        return Inertia::render('audits/index', compact(['auditLogs']));
    }
}
