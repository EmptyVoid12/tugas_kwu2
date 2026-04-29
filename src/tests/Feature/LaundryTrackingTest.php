<?php

use App\Models\Laundry;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeTenant(array $attributes = []): Tenant
{
    return Tenant::create(array_merge([
        'nama_laundry' => 'Laundry Test',
        'email' => fake()->unique()->safeEmail(),
        'password' => 'password',
        'alamat' => 'Jl. Test No. 1',
    ], $attributes));
}

function makeLaundry(Tenant $tenant, array $attributes = []): Laundry
{
    return $tenant->laundries()->create(array_merge([
        'nama_pelanggan' => 'Budi Santoso',
        'no_hp' => '081234567890',
        'alamat' => 'Jl. Pelanggan No. 2',
        'layanan' => Laundry::LAYANAN_REGULER,
        'tanggal_masuk' => now()->toDateString(),
        'estimasi_selesai' => now()->addDays(3)->toDateString(),
        'status' => Laundry::STATUS_DITERIMA,
    ], $attributes));
}

function makeSuperadmin(array $attributes = []): User
{
    return User::create(array_merge([
        'name' => 'Super Admin Test',
        'email' => fake()->unique()->safeEmail(),
        'password' => 'password',
    ], $attributes));
}

it('shows the public tracking page for a matching tenant and order', function () {
    $tenant = makeTenant(['nama_laundry' => 'Fresh Clean Laundry']);
    $laundry = makeLaundry($tenant, ['nama_pelanggan' => 'Siti Aminah']);

    $this->get(route('tracking.show', ['tenant' => $tenant->id, 'id' => $laundry->id]))
        ->assertOk()
        ->assertSee('Fresh Clean Laundry')
        ->assertSee('Siti Aminah')
        ->assertSee('081234567890');
});

it('shows the public tracking landing page on the root url', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSeeText('Cek Status')
        ->assertDontSee('/admin/login', false)
        ->assertDontSee('/superadmin/login', false);
});

it('allows customers to find an order by tracking code', function () {
    $tenant = makeTenant(['nama_laundry' => 'Fresh Clean Laundry']);
    $laundry = makeLaundry($tenant, ['nama_pelanggan' => 'Siti Aminah']);

    $this->get(route('tracking.search', ['code' => $laundry->kode_tracking]))
        ->assertRedirect(route('tracking.code', $laundry->kode_tracking));

    $this->get(route('tracking.code', $laundry->kode_tracking))
        ->assertOk()
        ->assertSee($laundry->kode_tracking)
        ->assertSee('Siti Aminah');
});

it('returns to the landing page when the tracking code is invalid', function () {
    $this->get(route('tracking.search', ['code' => 'LDR-TIDAKADA']))
        ->assertRedirect(route('home', ['code' => 'LDR-TIDAKADA']));

    $this->followingRedirects()
        ->get(route('tracking.search', ['code' => 'LDR-TIDAKADA']))
        ->assertOk()
        ->assertSee('Kode tracking tidak ditemukan');
});

it('allows a tenant to open a printable receipt for their own order', function () {
    $tenant = makeTenant(['nama_laundry' => 'Fresh Clean Laundry']);
    $laundry = makeLaundry($tenant, ['nama_pelanggan' => 'Siti Aminah']);

    $this->actingAs($tenant, 'tenant')
        ->get(route('tenant.laundries.print', $laundry))
        ->assertOk()
        ->assertSee('Fresh Clean Laundry')
        ->assertSee('Siti Aminah')
        ->assertSee($laundry->kode_tracking);
});

it('returns 404 when the tenant does not match the order owner', function () {
    $tenantA = makeTenant();
    $tenantB = makeTenant();
    $laundry = makeLaundry($tenantA);

    $this->get(route('tracking.show', ['tenant' => $tenantB->id, 'id' => $laundry->id]))
        ->assertNotFound();
});

it('scopes laundry queries to the active tenant id', function () {
    $tenantA = makeTenant(['nama_laundry' => 'Tenant A']);
    $tenantB = makeTenant(['nama_laundry' => 'Tenant B']);
    $laundryA = makeLaundry($tenantA, ['nama_pelanggan' => 'Order A']);
    makeLaundry($tenantB, ['nama_pelanggan' => 'Order B']);

    $scopedIds = Laundry::query()->forTenant($tenantA->id)->pluck('id');

    expect($scopedIds)->toHaveCount(1)
        ->and($scopedIds->first())->toBe($laundryA->id);
});

it('allows a tenant to download their monthly report as csv', function () {
    $tenant = makeTenant(['nama_laundry' => 'Fresh Clean Laundry']);
    $otherTenant = makeTenant(['nama_laundry' => 'Tenant Lain']);

    makeLaundry($tenant, [
        'nama_pelanggan' => 'Siti April',
        'tanggal_masuk' => '2026-04-10',
        'estimasi_selesai' => '2026-04-13',
    ]);

    makeLaundry($tenant, [
        'nama_pelanggan' => 'Bulan Lain',
        'tanggal_masuk' => '2026-03-28',
        'estimasi_selesai' => '2026-03-31',
    ]);

    makeLaundry($otherTenant, [
        'nama_pelanggan' => 'Tenant Lain April',
        'tanggal_masuk' => '2026-04-11',
        'estimasi_selesai' => '2026-04-14',
    ]);

    $response = $this->actingAs($tenant, 'tenant')
        ->get(route('tenant.reports.monthly', [
            'month' => 4,
            'year' => 2026,
        ]));

    $response->assertOk()
        ->assertDownload('laporan-laundry-fresh-clean-laundry-2026-04.csv');

    $content = $response->streamedContent();

    expect($content)
        ->toContain('Fresh Clean Laundry')
        ->toContain('04/2026')
        ->toContain('Siti April')
        ->not->toContain('Bulan Lain')
        ->not->toContain('Tenant Lain April');
});

it('allows a superadmin to download a tenant monthly report as csv', function () {
    $tenant = makeTenant(['nama_laundry' => 'Kilat Wash Express']);
    $otherTenant = makeTenant(['nama_laundry' => 'Fresh Clean Laundry']);
    $superadmin = makeSuperadmin();

    makeLaundry($tenant, [
        'nama_pelanggan' => 'Order Tenant Target',
        'tanggal_masuk' => '2026-04-15',
        'estimasi_selesai' => '2026-04-18',
    ]);

    makeLaundry($otherTenant, [
        'nama_pelanggan' => 'Order Tenant Lain',
        'tanggal_masuk' => '2026-04-16',
        'estimasi_selesai' => '2026-04-19',
    ]);

    $response = $this->actingAs($superadmin, 'superadmin')
        ->get(route('admin.tenants.reports.monthly', [
            'tenant' => $tenant,
            'month' => 4,
            'year' => 2026,
        ]));

    $response->assertOk()
        ->assertDownload('laporan-laundry-kilat-wash-express-2026-04.csv');

    $content = $response->streamedContent();

    expect($content)
        ->toContain('Kilat Wash Express')
        ->toContain('Order Tenant Target')
        ->not->toContain('Order Tenant Lain');
});
