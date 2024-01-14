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
        Schema::create('cong_va_tran_ho', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ho_id')->constrained('ho_thuy_loi');
            $table->string('kich_thuoc_cong')->nullable();
            $table->string('hinh_thuc_cong')->nullable();
            $table->string('cao_trinh_nguong_tran')->nullable();
            $table->string('B_tran')->nullable();
            $table->string('hinh_thuc_tran')->nullable();
            $table->boolean('co_tran_su_co')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cong_va_tran_ho', function (Blueprint $table) {
            $table->dropForeign('cong_va_tran_ho_ho_id_foreign');
        });
        Schema::dropIfExists('cong_va_tran_ho');
    }
};
