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
        Schema::create('reservoir_safety', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
            $table->boolean('finished_status');
            $table->dateTime('date_finished')->nullable();


            $table->foreignId('reservoir_id')->constrained('reservoirs');
            $table->foreignId('user_id')->constrained('users');

            $table->boolean('main_dam_status')->nullable();
            $table->string('main_dam_description')->nullable();
            $table->boolean('spillway_status')->nullable();
            $table->string('spillway_description')->nullable();
            $table->boolean('monitor_system_status')->nullable();
            $table->string('monitor_system_description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservoir_safety', function (Blueprint $table) {
            $table->dropForeign('reservoir_safety_reservoir_id_foreign');
            $table->dropForeign('reservoir_safety_user_id_foreign');
        });
        Schema::dropIfExists('reservoir_safety');
    }
};
