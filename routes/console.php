<?php

use App\Models\User;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('users:promote-admin {email}', function (string $email) {
    $user = User::where('email', $email)->first();

    if (! $user) {
        $this->error("No existe un usuario con el correo {$email}.");

        return 1;
    }

    $user->forceFill(['is_admin' => true])->save();

    $this->info("El usuario {$email} ahora es administrador.");

    return 0;
})->purpose('Otorga permisos de administrador global a un usuario');
