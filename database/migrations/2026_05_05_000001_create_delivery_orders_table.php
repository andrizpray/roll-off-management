<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_orders', function (Blueprint $table) {
            $table->id();
            $table->string('do_number', 20)->unique();
            $table->string('recipient_name');
            $table->string('recipient_address')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('destination')->nullable();
            $table->string('status', 20)->default('draft')->index();
            $table->string('notes')->nullable();
            $table->integer('created_by')->nullable()->index();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['do_number', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_orders');
    }
};