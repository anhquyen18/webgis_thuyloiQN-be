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
        Schema::create('object_activity_documents', function (Blueprint $table) {
            $table->string('object_activity_id')->primary();
            $table->string('name');
            $table->string('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('object_activity_documents', function (Blueprint $table) {
        });
        Schema::dropIfExists('object_activity_documents');
    }
};
