<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('do_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_order_id')->constrained()->onDelete('cascade');
            $table->string('lot_id');
            $table->foreign('lot_id')->references('lot_id')->on('roll_items');
            $table->integer('qty_order');
            $table->integer('qty_actual')->nullable();
            $table->decimal('weight_kg', 10, 2)->nullable();
            $table->string('paper_type')->nullable();
            $table->string('gsm')->nullable();
            $table->string('width')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['delivery_order_id', 'lot_id']);
            $table->index(['lot_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('do_items');
    }
};