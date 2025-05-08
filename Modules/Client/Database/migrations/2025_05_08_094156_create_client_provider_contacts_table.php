<?php

use Illuminate\Support\Facades\Schema;
use Modules\Service\App\Models\Service;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('client_provider_contacts', function (Blueprint $table) {
            $table->foreignId('client_id')->index()->constrained('users')->nullOnDelete();
            $table->foreignId('provider_id')->index()->constrained('users')->cascadeOnDelete();
            $table->foreignIdFor(Service::class)->index()->constrained()->cascadeOnDelete();
            $table->unique(['client_id', 'provider_id', 'service_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_provider_contacts');
    }
};
