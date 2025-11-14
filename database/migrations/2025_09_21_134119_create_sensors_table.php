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
        Schema::create('sensors', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('organic_volume');
            $table->unsignedTinyInteger('anorganic_volume');
            $table->foreignId('bin_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        DB::statement('ALTER TABLE sensors ADD CONSTRAINT chk_organic_volume CHECK (organic_volume BETWEEN 1 AND 100)');
        DB::statement('ALTER TABLE sensors ADD CONSTRAINT chk_anorganic_volume CHECK (anorganic_volume BETWEEN 1 AND 100)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sensors');
    }
};
