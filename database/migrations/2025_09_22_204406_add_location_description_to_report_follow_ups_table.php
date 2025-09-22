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
            // Tambahkan kolom ini setelah 'proof_longitude'
            $table->string('location_description')->nullable()->after('proof_longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_follow_ups', function (Blueprint $table) {
            $table->dropColumn('location_description');
        });
    }
};
