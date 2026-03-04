<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('khotib_schedules', function (Blueprint $table) {
            $table->string('bilal')->default('Bp. Adi')->after('khotib_name');
        });

        DB::table('khotib_schedules')->update(['bilal' => 'Bp. Adi']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('khotib_schedules', function (Blueprint $table) {
            $table->dropColumn('bilal');
        });
    }
};
