<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('do_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_order_id')->constrained()->onDelete('cascade');
            $table->string('mobil_id', 20);
            $table->string('driver_name');
            $table->string('status_before', 20);
            $table->date('assigned_date');
            $table->time('departure_time')->nullable();
            $table->time('arrival_time')->nullable();
            $table->integer('assigned_by')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['mobil_id', 'assigned_date']);
            $table->index(['delivery_order_id', 'mobil_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('do_assignments');
    }
};