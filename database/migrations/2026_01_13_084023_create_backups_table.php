<?php

use App\Models\BackupAnalysis;
use App\Models\StorageClass;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignIdFor(StorageClass::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->foreignIdFor(BackupAnalysis::class)->nullable()->constrained()->restrictOnDelete();
            $table->morphs('backupable');
            $table->string('name');
            $table->string('path')->unique();
            $table->string('mime_type');
            $table->string('mime_type_readable');
            $table->integer('size_bytes');
            $table->string('data_classifications')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};
