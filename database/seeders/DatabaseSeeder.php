<?php

namespace Database\Seeders;

use App\Models\Establecimiento;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ComunaSeederClass::class,
            EstablecimientoSeederClass::class,
        ]);

        $admin = User::firstOrCreate(
            ['email' => 'superadmin@ssmaule.cl'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('passwordAdmin.26'),
                'email_verified_at' => now(),
                'is_admin' => true,
            ],
        );

        $admin->forceFill([
            'is_admin' => true,
            'establecimiento_id' => Establecimiento::query()->where('codigo', '01000')->value('id'),
        ])->save();

        if (! $admin->personalTeam()) {
            $admin->ownedTeams()->save(Team::forceCreate([
                'user_id' => $admin->id,
                'name' => explode(' ', $admin->name, 2)[0]."'s Team",
                'personal_team' => true,
            ]));
        }
    }
}
