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
        Schema::create('object_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('object_type_id')->constrained('object_type');
            $table->unsignedSmallInteger('object_id');
            $table->foreignId('object_document_type_id')->constrained('object_type');
            $table->string('name');
            $table->string('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('object_documents', function (Blueprint $table) {
            $table->dropForeign('object_documents_object_type_id_foreign');
            $table->dropForeign('object_documents_object_document_type_id_foreign');
        });
        Schema::dropIfExists('object_documents');
    }
};
