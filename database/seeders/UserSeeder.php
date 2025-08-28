<?php
namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan foreign key check untuk truncate
        Schema::disableForeignKeyConstraints();
        User::truncate();
        Schema::enableForeignKeyConstraints();

        // Ambil ID dari setiap role
        $superAdminRole   = Role::where('name', 'super-admin')->first();
        $adminOfficerRole = Role::where('name', 'admin-officer')->first();
        $fieldOfficerRole = Role::where('name', 'field-officer')->first();
        $residentRole     = Role::where('name', 'resident')->first();
        $leaderRole       = Role::where('name', 'leader')->first();

        // Buat data users
        User::create([
            'name'         => 'Super Admin User',
            'email'        => 'superadmin@parkir.pku',
            'phone_number' => '6281200000001',
            'password'     => Hash::make('password'),
            'role_id'      => $superAdminRole->id,
        ]);

        User::create([
            'name'         => 'Admin Officer User',
            'email'        => 'adminofficer@parkir.pku',
            'phone_number' => '6281200000002',
            'password'     => Hash::make('password'),
            'role_id'      => $adminOfficerRole->id,
        ]);

        User::create([
            'name'         => 'Field Officer User',
            'email'        => 'fieldofficer@parkir.pku',
            'phone_number' => '6281200000003',
            'password'     => Hash::make('password'),
            'role_id'      => $fieldOfficerRole->id,
        ]);

        User::create([
            'name'         => 'Resident User',
            'email'        => 'resident@parkir.pku',
            'phone_number' => '6282288445265', // No HP ini yg akan terima notif WA
            'password'     => Hash::make('password'),
            'role_id'      => $residentRole->id,
        ]);

        User::create([
            'name'         => 'Leader User',
            'email'        => 'leader@parkir.pku',
            'phone_number' => '6281200000005',
            'password'     => Hash::make('password'),
            'role_id'      => $leaderRole->id,
        ]);
    }
}
