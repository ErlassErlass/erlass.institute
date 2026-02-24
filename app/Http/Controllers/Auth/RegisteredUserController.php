<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nama_lengkap' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'tanggal_lahir' => ['required', 'date'],
            'no_telephone' => ['required', 'string'],
            'agama' => ['required', 'string'],
            'pend_terakhir' => ['required', 'string'],
            'kompetensi_1' => ['required', 'string'],
            'kompetensi_2' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'tanggal_lahir' => $request->tanggal_lahir,
            'no_telephone' => $request->no_telephone,
            'status' => 'Aktif',
            'agama' => $request->agama,
            'pend_terakhir' => $request->pend_terakhir,
            'kompetensi_1' => $request->kompetensi_1,
            'kompetensi_2' => $request->kompetensi_2,
            'role' => 'instruktur',
            'is_verified' => false,
            'verification_status' => 'pending',
            'application_date' => now(),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
