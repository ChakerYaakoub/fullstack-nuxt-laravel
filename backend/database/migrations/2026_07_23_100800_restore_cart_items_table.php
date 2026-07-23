<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('favorites') && ! Schema::hasTable('cart_items')) {
            Schema::rename('favorites', 'cart_items');
        }

        if (! Schema::hasTable('cart_items')) {
            Schema::create('cart_items', function (Blueprint $table) {
                $table->id();
                $table->string('session_id')->index();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->unsignedInteger('quantity')->default(1);
                $table->timestamps();

                $table->unique(['session_id', 'product_id']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('cart_items') && ! Schema::hasTable('favorites')) {
            Schema::rename('cart_items', 'favorites');
        }
    }
};
