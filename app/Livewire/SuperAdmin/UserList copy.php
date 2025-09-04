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

    public function render()
    {
        $query = User::query();

        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%')
                    ->orWhere('username', 'like', '%' . $this->search . '%');
            });
        }

        $users = $query->with('role')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.super-admin.user-list', [
            'users' => $users,
        ])->layout('layouts.app');
    }
}
