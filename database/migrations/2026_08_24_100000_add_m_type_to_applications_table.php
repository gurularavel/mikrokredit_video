<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Hansı video mətn şablonunun oxunacağını təyin edir: 1 → page_record_script,
            // 2 → page_record_script_2. API-də `m_type` sahəsi ilə göndərilir.
            $table->unsignedTinyInteger('m_type')->default(1)->after('lang');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('m_type');
        });
    }
};
