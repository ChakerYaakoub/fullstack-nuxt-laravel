<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('cart_items') && ! Schema::hasColumn('cart_items', 'quantity')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->unsignedInteger('quantity')->default(1)->after('product_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('cart_items') && Schema::hasColumn('cart_items', 'quantity')) {
            Schema::table('cart_items', function (Blueprint $table) {
                $table->dropColumn('quantity');
            });
        }
    }
};
