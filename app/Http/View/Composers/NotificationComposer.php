<?php
namespace App\Http\View\Composers;

use App\Models\Report;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationComposer
{
    public function compose(View $view)
    {
        if (! Auth::check()) {
            $view->with('notificationCount', 0)->with('notifications', collect()); // Sediakan collection kosong
            return;
        }

        $user    = Auth::user();
        $count   = 0;
        $reports = collect(); // Inisialisasi sebagai collection kosong

        switch ($user->role->name) {
            case 'super-admin':
            case 'admin-officer':
                // Ambil jumlah total laporan 'pending' untuk badge
                $count = Report::where('status', 'pending')->count();
                // Ambil 5 laporan 'pending' terbaru untuk dropdown
                $reports = Report::where('status', 'pending')->latest()->take(5)->get();
                break;

            case 'field-officer':
                // Ambil jumlah total laporan 'verified' untuk badge
                $count = Report::where('status', 'verified')->count();
                // Ambil 5 laporan 'verified' terbaru untuk dropdown
                $reports = Report::where('status', 'verified')->latest()->take(5)->get();
                break;
        }

        // Kirim DUA variabel ke view: jumlahnya DAN datanya
        $view->with('notificationCount', $count)->with('notifications', $reports);
    }
}
