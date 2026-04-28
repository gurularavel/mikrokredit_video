<?php

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
        Schema::table('applications', function (Blueprint $table) {
            $table->string('token', 120)->change();
            // merchant_id already exists (partial run) — add FK constraint only
            $table->foreign('merchant_id')->references('id')->on('merchants')->nullOnDelete();
            $table->string('app_id')->nullable()->after('merchant_id');
            $table->decimal('amount', 12, 2)->nullable()->after('phone');
            $table->string('webhook_url')->nullable()->after('amount');
            $table->string('merchant_redirect_url')->nullable()->after('webhook_url');
            $table->string('lang', 10)->nullable()->after('merchant_redirect_url');
            $table->string('city')->nullable()->after('lang');
            $table->string('address')->nullable()->after('city');
            $table->decimal('salary', 15, 2)->nullable()->after('address');
            $table->string('access_token', 120)->nullable()->unique()->after('token');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['merchant_id']);
            $table->dropColumn([
                'merchant_id', 'app_id', 'amount', 'webhook_url',
                'merchant_redirect_url', 'lang', 'city', 'address',
                'salary', 'access_token',
            ]);
            $table->string('token', 64)->change();
        });
    }
};
