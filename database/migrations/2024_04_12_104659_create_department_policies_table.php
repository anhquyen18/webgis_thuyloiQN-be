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
        Schema::create('department_policies', function (Blueprint $table) {
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->foreignId('policy_id')->constrained('policies')->onDelete('cascade');
            $table->primary(['department_id', 'policy_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('department_policies', function (Blueprint $table) {
            $table->dropForeign('department_policies_department_id_foreign');
            $table->dropForeign('department_policies_policy_id_foreign');
        });
        Schema::dropIfExists('department_policies');
    }
};
