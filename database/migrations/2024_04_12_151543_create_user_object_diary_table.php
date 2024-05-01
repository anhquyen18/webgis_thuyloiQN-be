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
        Schema::create('user_object_diary', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('user_id')->constrained('users')->onDelete('set null');
            $table->foreignId('user_action_type_id')->constrained('user_action_type');
            $table->foreignId('object_type_id')->constrained('object_type')->onDelete('set null');
            $table->unsignedBigInteger('object_id');
            $table->json('data')->nullable();
            // $table->foreign('object_id')->references('id')->on('reservoirs')->onDelete('cascade');
            // // Cái này chỉ tham chiếu tạm ít bữa phải xoá và chỉ dùng bảng Reservoirs
            // $table->foreign('object_id')->references('id')->on('ho_thuy_loi')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_object_diary', function (Blueprint $table) {
            $table->dropForeign('user_object_diary_user_id_foreign');
            $table->dropForeign('user_object_diary_user_action_type_id_foreign');
            $table->dropForeign('user_object_diary_object_type_id_foreign');
        });
        Schema::dropIfExists('user_object_diary');
    }
};
