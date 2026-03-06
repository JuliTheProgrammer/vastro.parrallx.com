<?php

use App\Models\Organization;
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
        Schema::create('organization_statistics', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignIdFor(Organization::class)->constrained()->restrictOnDelete();
            $table->bigInteger('total_stored_bytes')->default(0);
            $table->bigInteger('max_allowed_bytes');
            $table->integer('backup_count')->default(0);
            $table->integer('max_api_tokens');
            $table->integer('used_api_tokens')->default(0);
            $table->integer('backup_analysis_count')->default(0);
            $table->integer('vault_count')->default(0);
            $table->boolean('account_blocked')->default(false);
            $table->string('current_ai_model')->nullable();
            $table->integer('total_users')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_statistics');
    }
};
