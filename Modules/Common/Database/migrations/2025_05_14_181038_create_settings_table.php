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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->string('value');
            $table->string('type')->default('string');
            $table->timestamps();
        });

        DB::table('settings')->insert([
            ['key' => 'free_trial_months', 'value' => '3', 'type' => 'integer', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'free_trial_contacts_count', 'value' => '1', 'type' => 'integer', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
