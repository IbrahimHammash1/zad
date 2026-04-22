<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_baskets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('basket_id')->constrained()->restrictOnDelete();
            $table->foreignId('store_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->string('basket_name');
            $table->decimal('basket_price', 12, 2);
            $table->string('store_name');
            $table->timestamps();

            $table->index(['order_id', 'basket_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_baskets');
    }
};
