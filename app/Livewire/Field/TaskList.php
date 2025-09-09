<?php
namespace App\Livewire\Field;

use App\Models\Report;
use Livewire\Component;
use Livewire\WithPagination;

class TaskList extends Component
{
    use WithPagination;

    public string $search = '';

    // Setiap kali properti $search diubah (karena wire:model.live),
    // method ini akan otomatis berjalan untuk mereset paginasi.
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        // Query dasar: hanya ambil laporan yang sudah diverifikasi dan siap ditindaklanjuti.
        $query = Report::whereIn('status', ['verified', 'in_progress']);

        // Terapkan filter pencarian jika ada
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('report_code', 'like', '%' . $this->search . '%')
                    ->orWhere('title', 'like', '%' . $this->search . '%')
                    ->orWhere('location_address', 'like', '%' . $this->search . '%');
            });
        }

        // Ambil data, urutkan dari yang paling baru diverifikasi
        $reports = $query->with('resident', 'images')
            ->latest('verified_at')
            ->paginate(10);

        return view('livewire.field.task-list', [
            'reports' => $reports,
        ])->layout('layouts.app'); // <-- Beritahu Livewire untuk pakai layout utama kita
    }
}
