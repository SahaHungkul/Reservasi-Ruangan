<?php
namespace App\Observers;

use App\Models\Reservations;
use App\Exports\ReservationsExport;
use Maatwebsite\Excel\Facades\Excel;

class ReservationObserver
{
    public function created(Reservations $reservation)
    {
        // Simpan file ke storage/app/public/exports/reservations.xlsx
        $filePath = 'exports/reservations.xlsx';
        Excel::store(new ReservationsExport(), $filePath, 'public');
        // opsi: Excel::store(new ReservationsExport(), $filePath, 'public', \Maatwebsite\Excel\Excel::XLSX);
    }

    // Opsional: update atau delete handler jika ingin regenerate juga
    public function updated(Reservations $reservation)
    {
        Excel::store(new ReservationsExport(), 'exports/reservations.xlsx', 'public');
    }

    public function deleted(Reservations $reservation)
    {
        Excel::store(new ReservationsExport(), 'exports/reservations.xlsx', 'public');
    }
}
