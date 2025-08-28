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
        Schema::create('report_follow_ups', function (Blueprint $table) {
            $table->id();
            // Menggunakan unique() agar satu laporan hanya bisa punya satu follow-up.
            // Jika ingin bisa berkali-kali, hapus ->unique().
            $table->foreignId('report_id')->unique()->constrained('reports')->onDelete('cascade');
            // Menghubungkan ke petugas yang menyelesaikan laporan
            $table->foreignId('officer_id')->constrained('users');
            $table->text('notes');                    // Catatan dari petugas
            $table->string('proof_image_path');       // Path gambar bukti penyelesaian
            $table->decimal('proof_latitude', 10, 8); // Koordinat saat foto bukti diambil
            $table->decimal('proof_longitude', 11, 8);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_follow_ups');
    }
};
