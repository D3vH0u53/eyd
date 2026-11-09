<?php

use App\Models\Comuna;
use App\Models\Establecimiento;
use App\Models\User;
use Inertia\Testing\AssertableInertia;

function createEstablecimientoForAdminTests(): Establecimiento
{
    $comuna = Comuna::create([
        'nombre' => 'Talca',
        'codigo' => '07101',
    ]);

    return Establecimiento::create([
        'nombre' => 'Hospital Talca',
        'codigo' => '0701',
        'direccion' => '1 Norte',
        'comuna_id' => $comuna->id,
    ]);
}

test('non admin users cannot access the user administration page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.users.index'))
        ->assertForbidden();
});

test('admin users can access the user administration page', function () {
    $user = User::factory()->create(['is_admin' => true]);

    $this->actingAs($user)
        ->get(route('admin.users.index'))
        ->assertSuccessful()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Admin/Users/Index')
            ->has('users')
            ->has('establecimientos'));
});

test('admin users can create new accounts', function () {
    $establecimiento = createEstablecimientoForAdminTests();
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->post(route('admin.users.store'), [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'establecimiento_id' => $establecimiento->id,
        ])
        ->assertRedirect(route('admin.users.index'));

    $user = User::where('email', 'nuevo@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->is_admin)->toBeFalse()
        ->and($user->establecimiento_id)->toBe($establecimiento->id)
        ->and($user->ownedTeams)->toHaveCount(1)
        ->and($user->ownedTeams->first()->personal_team)->toBeTrue();
});

test('non admin users cannot create accounts', function () {
    $establecimiento = createEstablecimientoForAdminTests();
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('admin.users.store'), [
            'name' => 'Nuevo Usuario',
            'email' => 'nuevo@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'establecimiento_id' => $establecimiento->id,
        ])
        ->assertForbidden();

    $this->assertDatabaseMissing('users', [
        'email' => 'nuevo@example.com',
    ]);
});
