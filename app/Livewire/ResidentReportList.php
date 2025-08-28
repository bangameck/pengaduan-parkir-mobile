<?php
namespace App\Livewire;

use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ResidentReportList extends Component
{
    public $reports;
    public $perPage = 5;
    public $page    = 1;
    public $hasMorePages;
    public string $search = '';

    public function mount()
    {$this->loadReports();}
    public function updatedSearch(): void
    {$this->resetPage();
        $this->loadReports();}
    public function resetPage(): void
    {$this->page = 1;
        $this->reports                         = collect();}

    public function loadReports()
    {
        // Query ini akan mengambil SEMUA laporan yang dimiliki oleh user yang sedang login
        $query = Report::query()->where('resident_id', Auth::id());

        // Logika pencarian tetap sama
        if (! empty($this->search)) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('report_code', 'like', '%' . $this->search . '%');
            });
        }

        // Urutkan berdasarkan yang paling baru dibuat
        $reports = $query->with('images')
            ->latest() // Menggunakan latest() akan mengurutkan berdasarkan created_at (DESC)
            ->paginate($this->perPage, ['*'], 'page', $this->page);

        $this->hasMorePages = $reports->hasMorePages();
        $this->reports      = $this->page > 1 ? $this->reports->concat($reports->items()) : collect($reports->items());
    }

    public function loadMore()
    {if ($this->hasMorePages) {$this->page++;
        $this->loadReports();}}
    public function render()
    {return view('livewire.resident-report-list');}
}
