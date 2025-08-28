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
        Schema::table('follow_up_media', function (Blueprint $table) {
            // Kolom untuk menyimpan path thumbnail, hanya diisi jika file_type adalah video
            $table->string('thumbnail_path')->nullable()->after('file_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('follow_up_media', function (Blueprint $table) {
            //
        });
    }
};
