<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prayer_times', function (Blueprint $table) {
            $table->string('imsak')->nullable();
            $table->string('subuh')->nullable();
            $table->string('zuhur')->nullable();
            $table->string('ashar')->nullable();
            $table->string('maghrib')->nullable();
            $table->string('isya')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('prayer_times', function (Blueprint $table) {
            $table->dropColumn([
                'imsak',
                'subuh',
                'zuhur',
                'ashar',
                'maghrib',
                'isya',
            ]);
        });
    }
};
