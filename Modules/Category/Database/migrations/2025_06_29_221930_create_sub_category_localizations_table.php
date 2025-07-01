<?php

use Illuminate\Support\Facades\Schema;
use Modules\Country\App\Models\Country;
use Illuminate\Database\Schema\Blueprint;
use Modules\Category\App\Models\SubCategory;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sub_category_localizations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SubCategory::class)->index()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Country::class)->index()->constrained()->cascadeOnDelete();
            $table->string('title_ar');
            $table->string('title_en');
            $table->unique(['sub_category_id', 'country_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_category_localizations');
    }
};
