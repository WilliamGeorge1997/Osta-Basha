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
        Schema::table('shop_owners', function (Blueprint $table) {
            $table->string('shop_name')->nullable()->change();
            $table->text('products_description')->nullable()->change();
            $table->string('experience_years')->nullable()->change();
            $table->string('address')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shop_owners', function (Blueprint $table) {
            $table->string('shop_name')->change();
            $table->text('products_description')->change();
            $table->string('experience_years')->change();
            $table->string('address')->change();
        });
    }
};
