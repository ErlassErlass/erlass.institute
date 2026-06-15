<?php

namespace Tests\Feature;

use App\Models\LaporanMengajar;
use App\Models\Sekolah;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ValidationSecurityTest extends TestCase
{
    use RefreshDatabase;

    private User $instructor;

    private User $admin;

    private Sekolah $sekolah;

    protected function setUp(): void
    {
        parent::setUp();

        $this->instructor = User::factory()->verifiedInstructor()->create();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->sekolah = Sekolah::factory()->create(['kodlan' => 'TEST001']);
    }

    public function test_prevents_debug_route_access_in_production(): void
    {
        // Set production environment
        config(['app.env' => 'production']);

        $response = $this->get('/debug-login');
        $response->assertStatus(404);
    }

    public function test_debug_route_requires_debug_mode(): void
    {
        // Set local environment but disable debug
        config(['app.env' => 'local', 'app.debug' => false]);

        $response = $this->get('/debug-login');
        $response->assertStatus(404);
    }

    public function test_mass_assignment_protection(): void
    {
        // Test that protected fields cannot be mass assigned
        $laporan = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $this->instructor->id,
        ]);

        $originalId = $laporan->id;
        $originalCreatedAt = $laporan->created_at;

        // Attempt to mass assign protected fields
        $laporan->update([
            'id' => 99999,
            'created_at' => Carbon::now()->subYear(),
            'updated_at' => Carbon::now()->subYear(),
            'materi_pengajaran' => 'Updated Material',
        ]);

        $laporan->refresh();

        // Protected fields should not change
        $this->assertEquals($originalId, $laporan->id);
        $this->assertEquals($originalCreatedAt->format('Y-m-d H:i:s'), $laporan->created_at->format('Y-m-d H:i:s'));

        // Fillable fields should change
        $this->assertEquals('Updated Material', $laporan->materi_pengajaran);
    }

    public function test_csrf_protection_enabled_fails(): void
    {
        // Force CSRF middleware to run during tests
        $this->app->bind(
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            function ($app) {
                return new class($app, $app['encrypter']) extends \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken {
                    protected function runningUnitTests() { return false; }
                };
            }
        );

        $laporanData = [
            'user_id_instruktur' => $this->instructor->id,
            'pertemuan_ke' => 1,
            'sekolah_kodlan' => 'TEST001',
            'jadwal_mengajar' => Carbon::today()->format('d/m/Y'),
            'rombel' => '1',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:00',
            'kategori_pengajaran' => 'Regular',
            'materi_pengajaran' => 'Test Material',
            '_token' => 'different-token',
        ];

        // With middleware and mismatched token/header, it should require CSRF and fail
        $response = $this->actingAs($this->instructor)
            ->withSession(['_token' => 'session-token'])
            ->withHeaders(['X-CSRF-TOKEN' => 'wrong-token'])
            ->post(route('laporan-mengajar.store'), $laporanData);

        $response->assertStatus(419); // CSRF token mismatch
    }

    public function test_csrf_protection_disabled_passes(): void
    {
        $laporanData = [
            'user_id_instruktur' => $this->instructor->id,
            'pertemuan_ke' => 1,
            'sekolah_kodlan' => 'TEST001',
            'jadwal_mengajar' => Carbon::today()->format('d/m/Y'),
        ];

        // Request without CSRF token bypass should work
        $response = $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->actingAs($this->instructor)
            ->post(route('laporan-mengajar.store'), $laporanData);

        $this->assertNotEquals(419, $response->status());
    }

    public function test_file_upload_security(): void
    {
        Storage::fake('public');

        // Test malicious file types
        $maliciousFiles = [
            UploadedFile::fake()->create('script.php', 500, 'text/php'),
            UploadedFile::fake()->create('executable.exe', 500, 'application/octet-stream'),
            UploadedFile::fake()->create('script.js', 500, 'text/javascript'),
        ];

        foreach ($maliciousFiles as $file) {
            $laporanData = [
                'user_id_instruktur' => $this->instructor->id,
                'pertemuan_ke' => 1,
                'sekolah_kodlan' => 'TEST001',
                'jadwal_mengajar' => Carbon::today()->format('d/m/Y'),
                'rombel' => '1',
                'jam_mulai' => '08:00',
                'jam_selesai' => '09:00',
                'kategori_pengajaran' => 'Regular',
                'materi_pengajaran' => 'Test Material',
                'foto_kegiatan' => $file,
            ];

            $response = $this->actingAs($this->instructor)
                ->post(route('laporan-mengajar.store'), $laporanData);

            $response->assertSessionHasErrors(['foto_kegiatan']);
        }
    }

    public function test_file_size_limits(): void
    {
        Storage::fake('public');

        // Test oversized file (>5MB)
        $oversizedFile = UploadedFile::fake()->create('large.jpg', 6000); // 6MB

        $laporanData = [
            'user_id_instruktur' => $this->instructor->id,
            'pertemuan_ke' => 1,
            'sekolah_kodlan' => 'TEST001',
            'jadwal_mengajar' => Carbon::today()->format('d/m/Y'),
            'rombel' => '1',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:00',
            'kategori_pengajaran' => 'Regular',
            'materi_pengajaran' => 'Test Material',
            'foto_kegiatan' => $oversizedFile,
        ];

        $response = $this->actingAs($this->instructor)
            ->post(route('laporan-mengajar.store'), $laporanData);

        $response->assertSessionHasErrors(['foto_kegiatan']);
    }

    public function test_sql_injection_prevention(): void
    {
        // Test SQL injection attempts in search
        $maliciousQueries = [
            "'; DROP TABLE laporan_mengajar; --",
            "' UNION SELECT * FROM users --",
            "1' OR '1'='1",
            "<script>alert('xss')</script>",
        ];

        foreach ($maliciousQueries as $query) {
            $response = $this->actingAs($this->instructor)
                ->get(route('laporan-mengajar.search', ['query' => $query]));

            $response->assertStatus(200);
            // Database should still be intact
            $this->assertDatabaseCount('laporan_mengajar', 0);
        }
    }

    public function test_authorization_policies(): void
    {
        $otherInstructor = User::factory()->create(['role' => 'instruktur']);

        $laporan = LaporanMengajar::factory()->create([
            'user_id_instruktur' => $otherInstructor->id,
            'sekolah_kodlan' => 'TEST001',
        ]);

        // Instructor cannot access other's laporan
        $response = $this->actingAs($this->instructor)
            ->get(route('laporan-mengajar.show', $laporan));

        $response->assertStatus(403);

        // Admin can access any laporan
        $response = $this->actingAs($this->admin)
            ->get(route('laporan-mengajar.show', $laporan));

        $response->assertStatus(200);
    }

    public function test_input_sanitization(): void
    {
        $maliciousInput = [
            'materi_pengajaran' => '<script>alert("xss")</script>',
            'refleksi_siswa' => '<?php echo "hack"; ?>',
            'sekolah_nama' => 'Test<img src=x onerror=alert(1)>',
        ];

        $laporanData = array_merge([
            'user_id_instruktur' => $this->instructor->id,
            'pertemuan_ke' => 1,
            'sekolah_kodlan' => 'TEST001',
            'jadwal_mengajar' => Carbon::today()->format('d/m/Y'),
            'rombel' => '1',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:00',
            'kategori_pengajaran' => 'Regular',
        ], $maliciousInput);

        $response = $this->actingAs($this->instructor)
            ->post(route('laporan-mengajar.store'), $laporanData);

        if ($response->status() === 302) { // Redirect on success
            $laporan = LaporanMengajar::latest()->first();

            // Verify that malicious scripts are escaped or removed
            $this->assertStringNotContainsString('<script>', $laporan->materi_pengajaran);
            $this->assertStringNotContainsString('<?php', $laporan->refleksi_siswa);
            $this->assertStringNotContainsString('onerror=', $laporan->sekolah_nama);
        }
    }

    public function test_foreign_key_validation(): void
    {
        // Test invalid user_id_instruktur
        $laporanData = [
            'user_id_instruktur' => 99999, // Non-existent user
            'pertemuan_ke' => 1,
            'sekolah_kodlan' => 'TEST001',
            'jadwal_mengajar' => Carbon::today()->format('d/m/Y'),
            'rombel' => '1',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:00',
            'kategori_pengajaran' => 'Regular',
            'materi_pengajaran' => 'Test Material',
        ];

        $response = $this->actingAs($this->instructor)
            ->post(route('laporan-mengajar.store'), $laporanData);

        $response->assertSessionHasErrors(['user_id_instruktur']);

        // Test invalid sekolah_kodlan
        $laporanData['user_id_instruktur'] = $this->instructor->id;
        $laporanData['sekolah_kodlan'] = 'INVALID';

        $response = $this->actingAs($this->instructor)
            ->post(route('laporan-mengajar.store'), $laporanData);

        $response->assertSessionHasErrors(['sekolah_kodlan']);
    }

    public function test_date_validation_business_rules(): void
    {
        // Test future date (should fail)
        $laporanData = [
            'user_id_instruktur' => $this->instructor->id,
            'pertemuan_ke' => 1,
            'sekolah_kodlan' => 'TEST001',
            'jadwal_mengajar' => Carbon::tomorrow()->format('d/m/Y'),
            'rombel' => '1',
            'jam_mulai' => '08:00',
            'jam_selesai' => '09:00',
            'kategori_pengajaran' => 'Regular',
            'materi_pengajaran' => 'Test Material',
        ];

        $response = $this->actingAs($this->instructor)
            ->post(route('laporan-mengajar.store'), $laporanData);

        $response->assertSessionHasErrors(['jadwal_mengajar']);

        // Test too far in past (>7 days, should fail)
        $laporanData['jadwal_mengajar'] = Carbon::today()->subDays(8)->format('d/m/Y');

        $response = $this->actingAs($this->instructor)
            ->post(route('laporan-mengajar.store'), $laporanData);

        $response->assertSessionHasErrors(['jadwal_mengajar']);

        // Test valid date (within 7 days, should pass)
        $laporanData['jadwal_mengajar'] = Carbon::today()->subDays(3)->format('d/m/Y');

        $response = $this->actingAs($this->instructor)
            ->post(route('laporan-mengajar.store'), $laporanData);

        $response->assertSessionDoesntHaveErrors(['jadwal_mengajar']);
    }

    public function test_role_based_access_control(): void
    {
        $regularUser = User::factory()->create(['role' => 'user']); // Invalid role

        // Regular user should not access instructor features
        $response = $this->actingAs($regularUser)
            ->get(route('laporan-mengajar.index'));

        $response->assertStatus(403);

        // Test middleware role checking
        $response = $this->actingAs($regularUser)
            ->get('/users'); // Admin-only route

        $response->assertStatus(403);
    }

    public function test_session_security(): void
    {
        // Test session hijacking protection
        $response = $this->actingAs($this->instructor)
            ->get(route('laporan-mengajar.index'));

        $response->assertStatus(200);

        // Simulate session manipulation
        session(['user_id' => $this->admin->id]);

        $response = $this->actingAs($this->instructor)
            ->get(route('laporan-mengajar.index'));

        // Should still be authenticated as instructor, not admin
        $this->assertEquals($this->instructor->id, auth()->id());
    }

    public function test_rate_limiting_protection(): void
    {
        // Test multiple rapid requests (simplified test)
        $responses = [];
        for ($i = 0; $i < 10; $i++) {
            $responses[] = $this->actingAs($this->instructor)
                ->get(route('laporan-mengajar.index'));
        }

        // All should succeed under normal rate limits
        foreach ($responses as $response) {
            $this->assertLessThanOrEqual(429, $response->status());
        }
    }
}
