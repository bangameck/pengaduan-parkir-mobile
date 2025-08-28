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
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_code')->unique();
            $table->foreignId('resident_id')->constrained('users');
            $table->foreignId('admin_officer_id')->nullable()->constrained('users');
            $table->foreignId('field_officer_id')->nullable()->constrained('users');
            $table->string('title');
            $table->text('description');
            $table->text('location_address');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('status')->default('pending'); // 'pending', 'verified', 'rejected', 'in_progress', 'completed'
            $table->text('rejection_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes(); // Tips Jangka Panjang: Agar data tidak benar-benar hilang saat dihapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
