<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Modules\Provider\App\Models\Provider;
use Modules\Category\App\Models\SubCategory;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('provider_sub_category', function (Blueprint $table) {
            $table->foreignIdFor(Provider::class)->index()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(SubCategory::class)->index()->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['provider_id', 'sub_category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('provider_sub_category');
    }
};
