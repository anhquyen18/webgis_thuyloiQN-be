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
        Schema::create('reservoir_drainage_safety', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->nullable();
            $table->string('description')->nullable();

            $table->foreignId('reservoir_drainage_id')->constrained('reservoir_drainage');
            $table->foreignId('reservoir_safety_id')->constrained('reservoir_safety');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservoir_drainage_safety', function (Blueprint $table) {
            $table->dropForeign('reservoir_drainage_safety_reservoir_drainage_id_foreign');
            $table->dropForeign('reservoir_drainage_safety_reservoir_safety_id_foreign');
        });
        Schema::dropIfExists('reservoir_drainage_safety');
    }
};
