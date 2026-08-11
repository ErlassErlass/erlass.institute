<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create(['role' => 'admin_sistem']);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'nama_lengkap' => 'Test User',
                'email' => 'test@example.com',
                'tanggal_lahir' => '1990-01-01',
                'no_telephone' => '081234567890',
                'agama' => 'Lainnya',
                'pend_terakhir' => 'SMA/SMK Sederajat',
                'kompetensi_1' => 'Coding',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->nama_lengkap);
        $this->assertSame('test@example.com', $user->email);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create(['role' => 'admin_sistem']);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'nama_lengkap' => 'Test User',
                'email' => $user->email,
                'tanggal_lahir' => '1990-01-01',
                'no_telephone' => '081234567890',
                'agama' => 'Lainnya',
                'pend_terakhir' => 'SMA/SMK Sederajat',
                'kompetensi_1' => 'Coding',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertSame('Test User', $user->refresh()->nama_lengkap);
    }

    public function test_instructor_profile_can_be_updated(): void
    {
        $user = User::factory()->create(['role' => 'instruktur']);

        $response = $this
            ->actingAs($user)
            ->patch('/profile', [
                'nama_lengkap' => 'Instruktur Handal',
                'email' => 'instruktur.handal@example.com',
                'tanggal_lahir' => '1995-05-15',
                'no_telephone' => '081234567890',
                'agama' => 'Islam',
                'pend_terakhir' => 'D4/S1',
                'kompetensi_1' => 'Coding',
                'kompetensi_2' => 'Robotik',
                'gelar_depan' => 'Ir.',
                'gelar_belakang' => 'S.Kom',
                'nama_panggilan' => 'Handal',
                'no_hp_2' => '089876543210',
                'alamat_domisili' => 'Jl. Merdeka No 45',
                'kota_domisili' => 'Jakarta Selatan',
                'status_pernikahan' => 'Lajang',
                'pekerjaan_terakhir' => 'Pengajar IT',
                'jenjang_mengajar' => 'SD, SMP',
                'universitas_jurusan' => 'Universitas Indonesia - Teknik Informatika',
                'nama_bank' => 'Bank BCA',
                'no_rekening' => '1234567890',
                'no_npwp' => '123456789012345',
                'nik' => '3171012345678901',
                'tinggi_badan' => 170,
                'berat_badan' => 65,
                'riwayat_penyakit' => 'Tidak Ada',
                'mata_minus' => 'Normal',
                'alat_mengajar' => ['Laptop', 'Handphone'],
                'catatan_alat' => 'Laptop bertenaga tinggi',
                'kendaraan' => 'Pribadi',
                'jenis_kendaraan' => 'Motor',
                'waktu_mengajar' => [
                    'Senin' => ['08:00', '09:00'],
                    'Rabu' => ['13:00', '14:00']
                ],
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();
        $this->assertSame('Instruktur Handal', $user->nama_lengkap);
        $this->assertSame('instruktur.handal@example.com', $user->email);
        $this->assertNotNull($user->instructorProfile);
        $this->assertSame('Handal', $user->instructorProfile->nama_panggilan);
        $this->assertSame('Bank BCA', $user->instructorProfile->nama_bank);
        $this->assertSame('3171012345678901', $user->instructorProfile->nik);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }
}
