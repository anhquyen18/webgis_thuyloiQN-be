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
        Schema::create('ho_thuy_loi', function (Blueprint $table) {
            $table->id();
            $table->string('ten');
            $table->string('vi_tri_xa')->nullable();
            $table->string('vi_tri_huyen');
            $table->double('f_tuoi_tk', 8, 2)->nullable();
            $table->double('f_tuoi_tt', 8, 2)->nullable();
            $table->double('f_lv', 8, 2)->nullable();
            $table->double('wmndb', 8, 2)->nullable();
            $table->double('mnc', 8, 2)->nullable();
            $table->double('mndbt', 8, 2)->nullable();
            $table->double('mnltk', 8, 2)->nullable();
            $table->double('mnlkt', 8, 2)->nullable();
            $table->double('cao_trinh_dinh_tcs', 8, 2)->nullable();
            $table->integer('so_dap_phu')->nullable();
            $table->string('nam_xd')->nullable();
            $table->string('don_vi_ql')->nullable();
            $table->boolean('co_quy_trinh_vh')->nullable();
            $table->integer('phan_loai');

            // $table->id();
            // $table->binary('ten')->unique();
            // $table->binary('vi_tri_xa')->nullable();
            // $table->binary('vi_tri_huyen');
            // $table->binary('f_tuoi_tk')->nullable();
            // $table->binary('f_tuoi_tt')->nullable();
            // $table->binary('f_lv')->nullable();
            // $table->binary('wmndb')->nullable();
            // $table->binary('mnc')->nullable();
            // $table->binary('mndbt')->nullable();
            // $table->binary('mnltk')->nullable();
            // $table->binary('mnlkt')->nullable();
            // $table->binary('cao_trinh_dinh_tcs')->nullable();
            // $table->binary('so_dap_phu')->nullable();
            // $table->binary('nam_xd')->nullable();
            // $table->binary('don_vi_ql')->nullable();
            // $table->binary('co_quy_trinh_vh')->nullable();
            // $table->binary('phan_loai');

            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ho_thuy_loi');
    }
};
