<?php

use Modules\User\App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Modules\Category\App\Models\SubCategory;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shop_owners', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->index()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(SubCategory::class)->nullable()->index()->constrained()->nullOnDelete();
            $table->string('card_number');
            $table->string('card_image');
            $table->string('address');
            $table->string('experience_years');
            $table->text('experience_description');
            $table->string('min_price');
            $table->string('max_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shop_owners');
    }
};
