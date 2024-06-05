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
        // Chưa sử dụng
        // Đang sử dụng tạm thời bảng ho_thuy_loi
        Schema::create('reservoirs', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservoirs');
    }
};
