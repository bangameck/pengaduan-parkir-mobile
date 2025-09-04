<?php
namespace App\Livewire;

use App\Models\Report;
use Livewire\Component;

class PublicReportList extends Component
{
    public $reports;
    public $perPage = 5;
    public $page    = 1;
    public $hasMorePages;
    public $status;

    // 1. TAMBAHKAN PROPERTI BARU UNTUK MENAMPUNG KATA KUNCI PENCARIAN
    public string $search = '';

    public function mount($status = null)
    {
        $this->status = $status;
        $this->loadReports();
    }

    // 2. TAMBAHKAN LIFECYCLE HOOK INI
    // Method ini akan otomatis berjalan setiap kali nilai $search berubah
    public function updatedSearch(): void
    {
        $this->resetPage(); // Reset halaman ke 1 setiap kali ada pencarian baru
        $this->loadReports();
    }

    // Method untuk mereset state saat pencarian baru
    public function resetPage(): void
    {
        $this->page    = 1;
        $this->reports = collect(); // Kosongkan koleksi laporan yang ada
    }

    public function loadReports()
    {
        $query         = Report::query();
        $allowedStatus = ['completed', 'in_progress', 'verified', 'pending', 'rejected'];

        if ($this->status && in_array($this->status, $allowedStatus)) {
            $query->where('status', $this->status);
        } else {
            if (is_null($this->status)) {
                $query->whereIn('status', ['completed', 'in_progress', 'verified', 'pending', 'rejected']);
            }
        }

        // 3. TAMBAHKAN LOGIKA FILTER PENCARIAN DI SINI
        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('report_code', 'like', '%' . $this->search . '%')
                    ->orWhere('location_address', 'like', '%' . $this->search . '%');
            });
        }

        $reports = $query->with('resident', 'images', 'followUp')
            ->latest('updated_at')
            ->paginate($this->perPage, ['*'], 'page', $this->page);

        $this->hasMorePages = $reports->hasMorePages();

        $this->reports = $this->page > 1 ? $this->reports->concat($reports->items()) : collect($reports->items());
    }

    public function loadMore()
    {
        if ($this->hasMorePages) {
            $this->page++;
            $this->loadReports();
        }
    }

    public function render()
    {
        return view('livewire.public-report-list');
    }
}
