<?php

use App\Http\Controllers\Audit\AuditController;
use App\Http\Controllers\Backup\BackupController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\Folder\FolderController;
use App\Http\Controllers\Link\LinkCotroller;
use App\Http\Controllers\Vault\VaultController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\WorkOS\Http\Middleware\ValidateSessionWithWorkOS;
use Laravel\WorkOS\Http\Requests\AuthKitLoginRequest;

Route::get('/', function (AuthKitLoginRequest $request) {
    return $request->redirect();
})->name('home')->middleware('guest');

Route::middleware([
    'auth',
    ValidateSessionWithWorkOS::class,
])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('vaults/index');
    })->name('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('backups', [BackupController::class, 'index'])->name('backups.index');

    Route::get('backups/upload', [BackupController::class, 'create'])->name('backups.upload');
    Route::post('backups', [BackupController::class, 'store'])->name('backups.store');
    Route::post('backups/chunked-upload', [BackupController::class, 'uploadChunk'])->name('backups.chunked');
    Route::post('backups/finalize-upload', [BackupController::class, 'finalizeUpload'])->name('backups.finalize');
    Route::get('backups/{uuid}', [BackupController::class, 'show'])->name('backups.show');
    Route::get('backups/delete/{uuid}', [BackupController::class, 'destroy'])->name('backups.delete');

    Route::get('links', [LinkCotroller::class, 'index'])->name('links.index');
    Route::get('backups/share', function () {
        return Inertia::location(route('links.index'));
    })->name('backups.share');
    Route::get('backups/versions', function () {
        return Inertia::render('backups/versions');
    })->name('backups.versions');

    Route::get('fileupload', [FileUploadController::class, 'index'])->name('fileupload.index');
    Route::post('fileupload', [FileUploadController::class, 'store'])->name('fileupload.store');
    Route::post('fileupload/chunked-upload', [FileUploadController::class, 'uploadChunk'])->name('fileupload.chunked');
    Route::post('fileupload/finalize-upload', [FileUploadController::class, 'finalizeUpload'])->name('fileupload.finalize');

    Route::resource('vaults', VaultController::class);

    Route::get('ai', function () {
        return Inertia::render('ai/index');
    })->name('ai.index');

    Route::get('duplications', function () {
        return Inertia::render('duplications/index');
    })->name('duplications.index');

    Route::get('support/starting-guide', function () {
        return Inertia::render('support/starting-guide');
    })->name('support.starting-guide');

    Route::get('feedback', function () {
        return Inertia::render('feedback/index');
    })->name('feedback.index');

    Route::get('upgrade', function () {
        return Inertia::render('upgrade/index');
    })->name('upgrade.index');

    Route::get('audits', AuditController::class)->name('audits.index');

    Route::get('billing', function () {
        return Inertia::render('billing/index');
    })->name('billing.index');

    Route::resource('folder', FolderController::class);
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
