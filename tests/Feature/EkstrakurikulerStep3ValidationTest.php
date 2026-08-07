<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Sekolah;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EkstrakurikulerStep3ValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create([
            'role' => 'admin',
            'is_verified' => true,
        ]);
        \App\Models\Salesman::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_step3_validation_accepts_valid_enum_values()
    {
        $response = $this->actingAs($this->user)
            ->post(route('ekstrakurikuler.process-step'), [
                'current_step' => 3,
                'koneksi_internet' => 'ada',
                'keterangan_internet' => 'WiFi Sekolah Pass: 12345',
                'proyektor' => 'ada',
                'keterangan_proyektor' => 'HDMI port OK',
                'kabel_hdmi' => 'ada',
                'kabel_vga' => 'tidak_ada',
                'kabel_roll' => 'tidak_diketahui',
                'keterangan_kabel' => 'Adapter disiapkan',
            ]);

        $response->assertSessionHasNoErrors();
    }

    public function test_step3_validation_rejects_invalid_values()
    {
        $response = $this->actingAs($this->user)
            ->post(route('ekstrakurikuler.process-step'), [
                'current_step' => 3,
                'koneksi_internet' => 'invalid_value',
                'proyektor' => 'invalid_value',
                'kabel_hdmi' => 'invalid_value',
                'kabel_vga' => 'invalid_value',
                'kabel_roll' => 'invalid_value',
            ]);

        $response->assertSessionHasErrors([
            'koneksi_internet',
            'proyektor',
            'kabel_hdmi',
            'kabel_vga',
            'kabel_roll',
        ]);
    }
}
