<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->string('image')->nullable()->after('brand');
            $table->string('tax_group')->nullable()->after('useful_life_years');
            $table->date('warranty_expiry_date')->nullable()->after('purchase_date');
            $table->date('next_maintenance_date')->nullable()->after('warranty_expiry_date');
        });
    }

    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'image',
                'tax_group',
                'warranty_expiry_date',
                'next_maintenance_date',
            ]);
        });
    }
};
