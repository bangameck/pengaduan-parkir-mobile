<?php
namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents; // Baris ini tidak wajib
use Illuminate\Support\Facades\Schema;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan foreign key check untuk truncate
        Schema::disableForeignKeyConstraints();
        Role::truncate();
        Schema::enableForeignKeyConstraints();

        // Buat data roles
        Role::create(['name' => 'super-admin', 'display_name' => 'Super Admin']);
        Role::create(['name' => 'admin-officer', 'display_name' => 'Admin Officer']);
        Role::create(['name' => 'field-officer', 'display_name' => 'Field Officer']);
        Role::create(['name' => 'resident', 'display_name' => 'Resident']);
        Role::create(['name' => 'leader', 'display_name' => 'Leader']);
    }
}
