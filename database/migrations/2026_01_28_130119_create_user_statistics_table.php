<?php

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
        Schema::create('user_statistics', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->bigInteger('total_stored_megaBytes')->default(0);
            $table->integer('backup_count')->default(0);
            $table->integer('max_api_tokens');
            $table->integer('used_api_tokens')->default(0);
            $table->integer('backup_analysis_count')->default(0);
            $table->integer('vault_count')->default(0);
            $table->boolean('account_blocked')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user__statistics');
    }
};
