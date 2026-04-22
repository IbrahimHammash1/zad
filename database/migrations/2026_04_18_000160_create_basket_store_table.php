<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('basket_store', function (Blueprint $table) {
            $table->foreignId('basket_id')->constrained()->cascadeOnDelete();
            $table->foreignId('store_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['basket_id', 'store_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('basket_store');
    }
};
