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
        Schema::create('user_logs', function (Blueprint $table) {
            $table->id();
            $table->string('action'); // Thêm mới, Cập nhật, Xoá
            $table->timestamps();
            $table->string('log_id'); // id object được log
            $table->string('log_type'); // loại object
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_logs', function (Blueprint $table) {
            $table->dropForeign('user_logs_user_id_foreign');
        });
        Schema::dropIfExists('user_logs');
    }
};
