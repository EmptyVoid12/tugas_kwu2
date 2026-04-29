<?php

namespace App\Http\Controllers;

use App\Models\Laundry;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function index()
    {
        return view('tracking.index');
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:50'],
        ]);

        $code = strtoupper(trim($validated['code']));

        $laundry = Laundry::query()
            ->where('kode_tracking', $code)
            ->first();

        if (! $laundry) {
            return redirect()
                ->route('home', ['code' => $code])
                ->withInput()
                ->with('tracking_error', 'Kode tracking tidak ditemukan. Periksa kembali kode yang Anda masukkan.');
        }

        return redirect()->route('tracking.code', $code);
    }

    public function showByCode(string $code)
    {
        $laundry = Laundry::query()
            ->where('kode_tracking', strtoupper(trim($code)))
            ->firstOrFail();

        return view('tracking.show', [
            'laundry' => $laundry,
        ]);
    }

    public function show(int $tenant, int $id)
    {
        $laundry = Laundry::query()
            ->where('tenant_id', $tenant)
            ->whereKey($id)
            ->firstOrFail();

        return view('tracking.show', [
            'laundry' => $laundry,
        ]);
    }
}
