<?php
namespace App\Livewire\SuperAdmin;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

// <-- Kunci perbaikannya ada di sini

class UserList extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage   = 8;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[On('delete-user')] // Atribut ini sekarang akan dikenali dengan benar
    public function deleteUser($userId)
    {
        $user = User::find($userId);
        if (! $user) {
            $this->dispatch('delete-failed', message: 'Pengguna tidak ditemukan.');
            return;
        }

        if ($user->id === Auth::id()) {
            $this->dispatch('delete-failed', message: 'Anda tidak bisa menghapus akun Anda sendiri.');
            return;
        }

        $userName = $user->name;
        $user->delete();

        $this->dispatch('user-deleted', message: "Pengguna '{$userName}' berhasil dihapus.");
    }

    // Fungsi baru untuk memuat lebih banyak data
    public function loadMore()
    {
        $this->perPage += 4; // Tambah 4 kartu setiap kali diklik
    }

    public function render()
    {
        $query = User::query()->with('role');

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('username', 'like', '%' . $this->search . '%');
            });
        }

        // Hitung total user yang cocok SEBELUM di-limit
        $totalUserCount = $query->clone()->count();

        // Ambil data sesuai jumlah $perPage
        $users = $query->orderBy('name')->take($this->perPage)->get();

        return view('livewire.super-admin.user-list', [
            'users'        => $users,
            'hasMorePages' => $this->perPage < $totalUserCount, // Cek apakah masih ada data untuk dimuat
        ])->layout('layouts.app');

    }
}
