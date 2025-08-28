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
        Schema::create('report_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained('reports')->onDelete('cascade');
            // Siapa yang mengubah status? bisa Admin Officer, Field Officer, dll.
            $table->foreignId('user_id')->constrained('users');
            $table->string('status');          // Status baru yang ditetapkan, e.g., 'verified'
            $table->text('notes')->nullable(); // Catatan tambahan, misal alasan penolakan.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_status_histories');
    }
};
