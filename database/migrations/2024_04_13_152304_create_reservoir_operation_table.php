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
        Schema::create('reservoir_operation', function (Blueprint $table) {
            $table->string('id')->primary();

            $table->string('name');
            $table->timestamps();
            $table->dateTime('date_finished')->nullable();
            $table->double('available_water_supply')->nullable();
            $table->double('zfv')->nullable();
            $table->double('water_level')->nullable();

            // $table->foreignId('reservoir_id')->constrained('reservoirs');
            $table->string('reservoir_id');
            $table->foreign('reservoir_id')->references('id')->on('reservoirs')->onDelete('restrict');

            $table->foreignId('user_id')->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservoir_operation', function (Blueprint $table) {
            $table->dropForeign('reservoir_operation_reservoir_id_foreign');
            $table->dropForeign('reservoir_operation_user_id_foreign');
        });
        Schema::dropIfExists('reservoir_operation');
    }
};
