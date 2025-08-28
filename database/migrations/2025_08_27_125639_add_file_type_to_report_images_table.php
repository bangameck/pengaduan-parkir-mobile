<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_images', function (Blueprint $table) {
            // Menambahkan kolom baru untuk menyimpan tipe file
            $table->enum('file_type', ['image', 'video'])->default('image')->after('file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('report_images', function (Blueprint $table) {
            //
        });
    }
};
