<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->unsignedInteger('useful_life_years')->nullable()->default(4)->after('price');
            $table->unsignedBigInteger('salvage_value')->nullable()->default(0)->after('useful_life_years');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn(['useful_life_years', 'salvage_value']);
        });
    }
};
