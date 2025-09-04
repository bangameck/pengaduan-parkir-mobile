<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            // Kolom untuk menyimpan sumber laporan (whatsapp, facebook, dll)
            $table->string('source')->default('resident_app')->after('status');

            // Kolom untuk menyimpan detail kontak (nomor wa / username)
            $table->string('source_contact')->nullable()->after('source');
        });
    }

    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn(['source', 'source_contact']);
        });
    }
};
