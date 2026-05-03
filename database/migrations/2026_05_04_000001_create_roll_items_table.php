<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roll_items', function (Blueprint $table) {
            $table->id();
            $table->string('lot_id')->unique();
            $table->string('item_id')->nullable();
            $table->integer('end_qty')->default(0);
            $table->string('rew_id')->nullable();
            $table->date('tr_date')->nullable();
            $table->time('tr_time')->nullable();
            $table->text('description')->nullable();

            // Parsed from description
            $table->string('paper_type')->nullable()->index();
            $table->string('gsm')->nullable()->index();
            $table->string('plybond')->nullable();
            $table->string('width')->nullable()->index();

            // Raw fields
            $table->string('diameter')->nullable();
            $table->string('thickness')->nullable();
            $table->string('grade')->nullable();
            $table->text('comments')->nullable();
            $table->string('location_id')->nullable()->index();

            // SO & PIC tracking
            $table->string('so_september')->nullable();
            $table->string('pic_2025')->nullable();
            $table->string('lokasi_receiving')->nullable();
            $table->string('so_desember')->nullable();
            $table->string('receiving_2026')->nullable();
            $table->string('pic_2026')->nullable();
            $table->string('rcv_cnv_2026')->nullable();
            $table->string('so_maret_2026')->nullable();

            $table->string('status_barang')->nullable()->index();

            $table->timestamps();

            $table->index(['paper_type', 'gsm', 'width']);
            $table->index(['lot_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roll_items');
    }
};
