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
        Schema::table('report_follow_ups', function (Blueprint $table) {
            $table->dropForeign(['officer_id']); // Hapus foreign key constraint dulu
            $table->dropColumn('officer_id');    // Hapus kolomnya
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_follow_ups', function (Blueprint $table) {
            //
        });
    }
};
