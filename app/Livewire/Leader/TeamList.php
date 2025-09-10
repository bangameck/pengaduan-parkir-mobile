<?php
namespace App\Livewire\Leader;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class TeamList extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage   = 8;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    // Fungsi baru untuk memuat lebih banyak data
    public function loadMore()
    {
        $this->perPage += 4; // Tambah 4 kartu setiap kali diklik
    }

    public function render()
    {
        $query = User::query()->with('role');

        // filter hanya user dengan role tertentu
        $query->whereHas('role', function ($q) {
            $q->whereIn('name', ['admin-officer', 'field-officer']);
        });

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('username', 'like', '%' . $this->search . '%');
            });
        }

        // Hitung total user yang cocok SEBELUM di-limit
        $totalUserCount = (clone $query)->count();

        // Ambil data sesuai jumlah $perPage
        $users = $query->orderBy('name')->take($this->perPage)->get();

        return view('livewire.leader.team-list', [
            'users'          => $users,
            'hasMorePages'   => $this->perPage < $totalUserCount,
            'totalUserCount' => $totalUserCount,
        ])->layout('layouts.app');

    }
}
