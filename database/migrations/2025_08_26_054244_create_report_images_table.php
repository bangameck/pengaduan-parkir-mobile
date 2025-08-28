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
        Schema::create('report_images', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel 'reports'.
            // onDelete('cascade') artinya jika laporan dihapus, semua gambar terkait juga akan terhapus.
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            $table->string('file_path'); // Menyimpan path/lokasi file gambar di server
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_images');
    }
};
