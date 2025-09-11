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
            $view->with('notificationCount', 0)->with('notifications', collect());
            return;
        }

        $user    = Auth::user();
        $count   = 0;
        $reports = collect();

        switch ($user->role->name) {
            case 'super-admin':
            case 'admin-officer':
                // ADMIN: notifikasi laporan pending
                $count   = Report::where('status', 'pending')->count();
                $reports = Report::where('status', 'pending')->latest()->take(5)->get();
                break;

            case 'field-officer':
                // PETUGAS LAPANGAN: notifikasi laporan verified
                $count   = Report::where('status', 'verified')->count();
                $reports = Report::where('status', 'verified')->latest()->take(5)->get();
                break;

            case 'resident':
                // RESIDENT: notifikasi laporan miliknya yang sudah berubah status
                $count = Report::where('resident_id', $user->id)
                    ->whereIn('status', ['verified', 'completed', 'rejected'])
                    ->count();

                $reports = Report::where('resident_id', $user->id)
                    ->whereIn('status', ['verified', 'completed', 'rejected'])
                    ->latest()
                    ->take(5)
                    ->get();
                break;
        }

        $view->with('notificationCount', $count)->with('notifications', $reports);
    }
}
