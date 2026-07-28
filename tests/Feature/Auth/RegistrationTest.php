<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'nama_lengkap' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'tanggal_lahir' => '1990-01-01',
            'no_telephone' => '08123456789',
            'agama' => 'Islam',
            'pend_terakhir' => 'S1',
            'kompetensi_1' => 'Software Engineering',
            'nama_bank' => 'BCA',
            'no_rekening' => '1234567890',
            'nik' => '1234567890123456',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}
