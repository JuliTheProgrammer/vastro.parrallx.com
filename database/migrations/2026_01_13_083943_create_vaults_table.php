<?php

use App\Models\Location;
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
        Schema::create('vaults', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignIdFor(User::class)->constrained()->restrictOnDelete();
            $table->string('name')->unique();
            $table->string('aws_bucket_name');
            $table->string('aws_bucket_arn');
            $table->foreignIdFor(Location::class)->constrained()->restrictOnDelete();
            $table->boolean('worm_protection');
            $table->boolean('delete_protection');
            $table->boolean('kms_encryption')->default(false);
            $table->string('kms_arn')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vaults');
    }
};
