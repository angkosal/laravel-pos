<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            $table->decimal('price', 14, 4)->change();
        });
        Schema::table('payments', function (Blueprint $table): void {
            $table->decimal('amount', 14, 4)->change();
        });
        Schema::table('products', function (Blueprint $table): void {
            $table->decimal('price', 14, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table): void {
            //
        });
        Schema::table('payments', function (Blueprint $table): void {
            //
        });
        Schema::table('products', function (Blueprint $table): void {
            //
        });
    }
};
