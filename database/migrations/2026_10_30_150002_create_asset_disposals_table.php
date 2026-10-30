<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asset_disposals', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('asset_id')->constrained()->cascadeOnDelete();
            $table->date('disposal_date');
            $table->enum('disposal_method', ['Dimusnahkan', 'Dijual / Dilelang', 'Dihibahkan']);
            $table->bigInteger('disposal_amount')->default(0);
            $table->text('reason');
            $table->string('approved_by');
            $table->timestamps();

            $table->unique('asset_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_disposals');
    }
};
