<?php

namespace App\Http\Controllers;

use App\Models\Establecimiento;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class AdminUserController extends Controller
{
    /**
     * Display the user administration page.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => User::query()
                ->with('establecimiento:id,codigo,nombre')
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'is_admin', 'establecimiento_id']),
            'establecimientos' => Establecimiento::query()
                ->orderBy('nombre')
                ->get(['id', 'codigo', 'nombre']),
        ]);
    }

    /**
     * Create a new user account.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::default(), 'confirmed'],
            'establecimiento_id' => ['required', 'integer', 'exists:establecimientos,id'],
        ]);

        $user = DB::transaction(function () use ($validated): User {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'establecimiento_id' => $validated['establecimiento_id'],
            ]);

            $user->ownedTeams()->save(Team::forceCreate([
                'user_id' => $user->id,
                'name' => explode(' ', $user->name, 2)[0]."'s Team",
                'personal_team' => true,
            ]));

            return $user;
        });

        return redirect()
            ->route('admin.users.index')
            ->with('success', "Usuario {$user->email} creado correctamente.");
    }
}
