<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('defect_items', function (Blueprint $table) {
            $table->id();
            $table->year('year');

            // Common fields
            $table->string('lot_id')->index();
            $table->string('rew_id')->nullable();
            $table->string('paper_type')->nullable()->index();
            $table->string('gsm')->nullable();
            $table->string('plybond')->nullable();
            $table->string('width')->nullable();

            // Reason & category
            $table->string('reason')->nullable()->index();
            $table->string('category')->nullable()->index();

            // Date tracking (stored as serial date from Excel)
            $table->date('defect_date')->nullable();

            // 2025 specific
            $table->string('month')->nullable();
            $table->string('tr_type')->nullable();
            $table->string('keterangan')->nullable();

            $table->timestamps();

            $table->index(['year', 'category']);
            $table->index(['year', 'reason']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('defect_items');
    }
};
