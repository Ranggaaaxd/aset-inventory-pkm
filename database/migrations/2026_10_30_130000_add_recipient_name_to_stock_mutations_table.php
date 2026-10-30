<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_mutations', function (Blueprint $table): void {
            $table->string('recipient_name')->nullable()->after('room_id');
        });
    }

    public function down(): void
    {
        Schema::table('stock_mutations', function (Blueprint $table): void {
            $table->dropColumn('recipient_name');
        });
    }
};
