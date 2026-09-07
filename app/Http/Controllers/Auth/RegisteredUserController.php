<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\InstructorProfile;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
            'agama' => ['required', 'string', 'in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu,Lainnya'],
            'pend_terakhir' => ['required', 'string', 'in:SMA/SMK Sederajat,D3,D4/S1,S2,S3'],
            'kompetensi_1' => ['required', 'string', 'in:Coding,Robotik,Desain,IoT,Data Science,Bahasa Inggris'],
            'kompetensi_2' => ['nullable', 'string', 'in:Coding,Robotik,Desain,IoT,Data Science,Bahasa Inggris'],
            
            // Financial & Legal Info (Mandatory)
            'nama_bank' => ['required', 'string', \Illuminate\Validation\Rule::in(\App\Models\InstructorProfile::listNamaBank())],
            'no_rekening' => ['required', 'string', 'regex:/^[0-9]+$/', 'min:5', 'max:30'],
            'nik' => ['required', 'string', 'min:16', 'max:16'],
            'no_npwp' => ['nullable', 'string', 'max:20'],
        ], [
            'nama_bank.required' => 'Nama bank wajib dipilih.',
            'nama_bank.in' => 'Pilihan nama bank harus sesuai dengan daftar bank yang tersedia.',
            'no_rekening.required' => 'Nomor rekening bank wajib diisi.',
            'no_rekening.regex' => 'Nomor rekening hanya boleh berisi angka tanpa spasi atau tanda hubung.',
            'no_rekening.min' => 'Nomor rekening minimal 5 digit angka.',
            'no_rekening.max' => 'Nomor rekening maksimal 30 digit angka.',
            'nik.required' => 'Nomor NIK KTP wajib diisi.',
            'nik.min' => 'Nomor NIK KTP harus tepat 16 digit angka.',
            'nik.max' => 'Nomor NIK KTP harus tepat 16 digit angka.',
        ]);

        $user = DB::transaction(function () use ($request) {
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

            InstructorProfile::create([
                'user_id' => $user->id,
                'nama_panggilan' => explode(' ', trim($request->nama_lengkap))[0],
                'nama_bank' => $request->nama_bank,
                'no_rekening' => $request->no_rekening,
                'nik' => $request->nik,
                'no_npwp' => $request->no_npwp,
                'level' => 'junior',
            ]);

            return $user;
        });

        event(new Registered($user));
        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
