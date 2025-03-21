<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id('id');
            $table->decimal('qauntity', 10, 2);
            $table->decimal('totale', 10, 2);
            $table->unsignedBigInteger('plant_id');
            $table->unsignedBigInteger('client_id');
            $table->enum('status', ['pending', 'processing','delivered', 'canceled'])->default("pending");
            $table->timestamps();

            $table->foreign('plant_id')->references('id')->on('palnts')->onDelete('CASCADE');
            $table->foreign('client_id')->references('id')->on('users')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("orders");
    }
};
