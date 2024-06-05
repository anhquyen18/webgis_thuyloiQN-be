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
        Schema::create('user_reservoir_activity_diary', function (Blueprint $table) {
            $table->string('object_activity_id')->primary();
            $table->timestamps();

            $table->foreignId('user_id')->constrained('users')->onDelete('set null');
            $table->json('data')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_reservoir_activity_diary', function (Blueprint $table) {
            $table->dropForeign('user_reservoir_activity_diary_user_id_foreign');
        });
        Schema::dropIfExists('user_reservoir_activity_diary');
    }
};
