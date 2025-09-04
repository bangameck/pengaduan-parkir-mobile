<?php
namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Report; // <-- Import Role
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

// <-- Import Hash

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Ambil semua user beserta relasi rolenya, urutkan berdasarkan nama
        // $users = User::with('role')
        //     ->orderBy('name')
        //     ->paginate(15); // Kita batasi 15 user per halaman

        // return view('super-admin.users.index', ['users' => $users]);
    }

    /**
     * Menampilkan form untuk membuat pengguna baru.
     */
    public function create()
    {
        // Ambil semua role KECUALI 'resident', karena admin tidak seharusnya membuat akun warga
        $roles = Role::where('name', '!=', 'resident')->get();

        return view('super-admin.users.create', ['roles' => $roles]);
    }

    /**
     * Menyimpan pengguna baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validationRules = [
            'name'         => ['required', 'string', 'max:255'],
            'username'     => ['required', 'string', 'max:255', 'unique:users'],
            'email'        => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['nullable', 'string', 'max:20', 'unique:users', 'regex:/^62\d+$/'],
            'password'     => ['required', 'string', 'min:8', 'confirmed'],
            'role_id'      => ['required', 'exists:roles,id'],
        ];
        $validationMessages = [
            // ... pesan error lain ...
            'phone_number.regex' => 'Format nomor telepon tidak valid. Harus diawali dengan 62 (contoh: 628123456789).',
        ];

        $validated = $request->validate($validationRules, $validationMessages);

        // Buat user baru
        User::create([
            'name'         => $request->name,
            'username'     => $request->username,
            'email'        => $request->email,
            'phone_number' => $request->phone_number,
            'password'     => Hash::make($request->password), // Enkripsi password
            'role_id'      => $request->role_id,
        ]);

        return redirect()->route('super-admin.users.index')->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $user->load('role');

        // Inisialisasi variabel dengan nilai default untuk MENCEGAH SEMUA ERROR
        $relatedReports = collect();
        $chartData      = ['labels' => [], 'values' => []];
        $reportCounts   = ['today' => 0, 'this_month' => 0, 'this_year' => 0, 'total' => 0];

        // Ambil data spesifik hanya jika role-nya relevan
        if (in_array($user->role->name, ['resident', 'admin-officer', 'field-officer'])) {
            $query = null;

            switch ($user->role->name) {
                case 'resident':
                    $query = $user->reports();
                    break;

                case 'admin-officer':
                    $query = Report::where('admin_officer_id', $user->id);
                    break;

                case 'field-officer':
                    // ==========================================================
                    // == PERBAIKAN LOGIKA QUERY ADA DI SINI ==
                    // ==========================================================
                    $query = Report::whereHas('followUp', function ($q) use ($user) {
                        // Gunakan 'officer_id' sesuai relasi 'officer()' di model Anda
                        $q->where('officer_id', $user->id);
                    });
                    break;
            }

            if ($query && $query->exists()) {
                $relatedReports = $query->clone()->with('images')->latest()->paginate(5, ['*'], 'reportsPage');
                $reportCounts   = $this->getReportCounts($query);
                $chartData      = $this->getChartData($query);
            }
        }

        return view('super-admin.users.show', compact(
            'user',
            'relatedReports',
            'chartData',
            'reportCounts'
        ));
    }

    /**
     * Helper function untuk mengambil data count laporan.
     */
    private function getReportCounts($query)
    {
        return [
            'today'      => $query->clone()->whereDate('created_at', today())->count(),
            'this_month' => $query->clone()->whereMonth('created_at', today()->month)->whereYear('created_at', today()->year)->count(),
            'this_year'  => $query->clone()->whereYear('created_at', today()->year)->count(),
            'total'      => $query->clone()->count(),
        ];
    }

    /**
     * Helper function untuk menyiapkan data grafik.
     */
    private function getChartData($query)
    {
        $data = $query->clone()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        return [
            'labels' => $data->pluck('date')->map(function ($date) {
                return \Carbon\Carbon::parse($date)->format('d M');
            }),
            'values' => $data->pluck('count'),
        ];
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        // LOGIKA PENTING: Jika user yang akan diedit adalah 'resident', tolak akses.
        if ($user->role->name === 'resident') {
            return redirect()->route('super-admin.users.index')
                ->with('error', 'Akun masyarakat (resident) tidak dapat diubah melalui menu ini.');
        }

        // Ambil semua role KECUALI 'resident'
        $roles = Role::where('name', '!=', 'resident')->get();

        return view('super-admin.users.edit', [
            'user'  => $user,
            'roles' => $roles,
        ]);
    }

    /**
     * Memperbarui data pengguna di database.
     */
    public function update(Request $request, User $user)
    {
        // Validasi input
        $request->validate([
            'name'         => ['required', 'string', 'max:255'],
            'username'     => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'email'        => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'max:20', Rule::unique('users')->ignore($user->id), 'regex:/^62\d+$/'],
            'role_id'      => ['required', 'exists:roles,id'],
            'password'     => ['nullable', 'string', 'min:8', 'confirmed'], // Password sekarang opsional
        ]);

        // Siapkan data untuk di-update
        $data = $request->only('name', 'username', 'email', 'phone_number', 'role_id');

        // Jika ada password baru yang diinput, enkripsi dan tambahkan ke data
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Update data user
        $user->update($data);

        return redirect()->route('super-admin.users.index')->with('success', "Data pengguna '{$user->name}' berhasil diperbarui.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
