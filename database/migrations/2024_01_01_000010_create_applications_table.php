<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('surname');
            $table->string('phone', 20)->index();
            $table->string('token', 64)->unique();
            $table->timestamp('token_expires_at')->nullable();
            $table->enum('status', ['pending', 'recorded', 'reviewed'])->default('pending');
            $table->string('video_path')->nullable();
            $table->string('video_disk')->default('public');
            $table->unsignedBigInteger('video_size')->nullable();
            $table->timestamp('video_recorded_at')->nullable();
            $table->timestamp('sms_sent_at')->nullable();
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
