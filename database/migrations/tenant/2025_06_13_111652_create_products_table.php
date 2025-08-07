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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('product_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price_1', 8, 2);
            $table->decimal('price_2', 8, 2)->nullable();
            $table->boolean('stock_movement')->default(false);
            $table->string('image')->nullable();
            $table->boolean('status')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
