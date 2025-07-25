<?php


use Modules\User\App\Models\User;
use Illuminate\Support\Facades\Schema;
use Modules\Package\App\Models\Package;
use Illuminate\Database\Schema\Blueprint;
use Modules\Category\App\Models\SubCategory;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('providers', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->unique()->constrained()->cascadeOnDelete();
            $table->foreignIdFor(SubCategory::class)->nullable()->index()->constrained()->nullOnDelete();
            $table->string('card_number')->nullable();
            $table->string('card_image')->nullable();
            $table->string('address');
            $table->string('experience_years');
            $table->text('experience_description');
            $table->string('price')->nullable();
            $table->string('unit')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignIdFor(Package::class)->nullable()->index()->constrained()->nullOnDelete();
            $table->enum('status', ['free_trial', 'subscribed'])->default('free_trial');
            $table->boolean('is_active')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};
