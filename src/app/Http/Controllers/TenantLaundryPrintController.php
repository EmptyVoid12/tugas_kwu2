<?php

namespace App\Http\Controllers;

use App\Models\Laundry;

class TenantLaundryPrintController extends Controller
{
    public function __invoke(int $laundry)
    {
        $record = Laundry::query()
            ->forTenant(auth('tenant')->id())
            ->whereKey($laundry)
            ->firstOrFail();

        return view('tracking.print', [
            'laundry' => $record,
        ]);
    }
}
