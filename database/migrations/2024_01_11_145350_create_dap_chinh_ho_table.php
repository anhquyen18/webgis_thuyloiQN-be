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
        Schema::create('dap_chinh_ho', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ho_id')->constrained('ho_thuy_loi');
            $table->string('cao_trinh_dinh_dap')->nullable();
            $table->double('H_max', 8, 1);
            $table->double('length', 8, 1);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dap_chinh_ho', function (Blueprint $table) {
            $table->dropForeign('dap_chinh_ho_ho_id_foreign');
        });
        Schema::dropIfExists('dap_chinh_ho');
    }
};
