<?php

namespace Tests\Feature;

use App\Models\Ekstrakurikuler;
use App\Models\EkstrakurikulerRombel;
use App\Models\EkstrakurikulerSession;
use App\Models\Sekolah;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TicketTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $instructor;
    protected User $otherInstructor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'nama_lengkap' => 'Admin QC Test',
            'email' => 'admin_qc@erlass.institute',
            'password' => bcrypt('password'),
            'role' => 'admin_sistem',
            'status' => 'Aktif',
            'tanggal_lahir' => '1990-01-01',
            'agama' => 'Lainnya',
            'pend_terakhir' => 'S1',
            'kompetensi_1' => 'General',
            'no_telephone' => '081234567890',
        ]);

        $this->instructor = User::create([
            'nama_lengkap' => 'Instruktur Budi',
            'email' => 'budi@erlass.institute',
            'password' => bcrypt('password'),
            'role' => 'instruktur',
            'status' => 'Aktif',
            'tanggal_lahir' => '1995-05-05',
            'agama' => 'Islam',
            'pend_terakhir' => 'S1',
            'kompetensi_1' => 'Robotik',
            'no_telephone' => '081298765432',
        ]);

        $this->otherInstructor = User::create([
            'nama_lengkap' => 'Instruktur Siti',
            'email' => 'siti@erlass.institute',
            'password' => bcrypt('password'),
            'role' => 'instruktur',
            'status' => 'Aktif',
            'tanggal_lahir' => '1996-06-06',
            'agama' => 'Islam',
            'pend_terakhir' => 'S1',
            'kompetensi_1' => 'Sains',
            'no_telephone' => '081311223344',
        ]);
    }

    public function test_guest_cannot_access_tickets()
    {
        $response = $this->get(route('tickets.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_instructor_can_view_ticket_index()
    {
        Ticket::create([
            'ticket_number' => 'TCK-202608-0001',
            'user_id' => $this->instructor->id,
            'kategori' => 'jadwal_honor',
            'judul' => 'Penyesuaian Tanggal Sesi',
            'deskripsi' => 'Mohon ubah tanggal sesi 3',
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->instructor)->get(route('tickets.index'));
        $response->assertStatus(200);
        $response->assertSee('Penyesuaian Tanggal Sesi');
        $response->assertSee('TCK-202608-0001');
    }

    public function test_instructor_can_create_ticket_with_categories()
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('screenshot.png');

        // Test Category: Jadwal / Honor
        $response = $this->actingAs($this->instructor)->post(route('tickets.store'), [
            'kategori' => 'jadwal_honor',
            'judul' => 'Klaim Transport SDN Pejaten',
            'deskripsi' => 'Transport pada sesi 2 belum terhitung di slip payroll',
            'prioritas' => 'high',
            'foto_lampiran' => $file,
        ]);

        $ticket = Ticket::first();
        $this->assertNotNull($ticket);
        $response->assertRedirect(route('tickets.show', $ticket->id));
        $this->assertEquals('jadwal_honor', $ticket->kategori);
        $this->assertEquals('Jadwal / Honor', $ticket->kategori_label);
        $this->assertEquals('open', $ticket->status);
        $this->assertStringStartsWith('TCK-', $ticket->ticket_number);
        Storage::disk('public')->assertExists($ticket->foto_lampiran);

        // Test Category: Keluhan Lain
        $response2 = $this->actingAs($this->instructor)->post(route('tickets.store'), [
            'kategori' => 'keluhan_lain',
            'judul' => 'Kekurangan Modul Robotik',
            'deskripsi' => 'Modul kit kurang 3 unit untuk kelas A',
        ]);
        $ticket2 = Ticket::latest('id')->first();
        $this->assertEquals('keluhan_lain', $ticket2->kategori);
        $this->assertEquals('Keluhan Lain', $ticket2->kategori_label);

        // Test Category: Teknis / Error
        $response3 = $this->actingAs($this->instructor)->post(route('tickets.store'), [
            'kategori' => 'teknis_error',
            'judul' => 'GPS Check-in Tidak Terdeteksi',
            'deskripsi' => 'Titik lokasi geser saat check-in',
        ]);
        $ticket3 = Ticket::latest('id')->first();
        $this->assertEquals('teknis_error', $ticket3->kategori);
        $this->assertEquals('Teknis / Error', $ticket3->kategori_label);
    }

    public function test_ticket_validation_rejects_invalid_category()
    {
        $response = $this->actingAs($this->instructor)->post(route('tickets.store'), [
            'kategori' => 'invalid_category',
            'judul' => 'Test Judul',
            'deskripsi' => 'Test Deskripsi',
        ]);

        $response->assertSessionHasErrors(['kategori']);
    }

    public function test_instructor_cannot_view_other_instructors_ticket()
    {
        $ticket = Ticket::create([
            'ticket_number' => 'TCK-202608-0099',
            'user_id' => $this->otherInstructor->id,
            'kategori' => 'keluhan_lain',
            'judul' => 'Tiket Rahasia Siti',
            'deskripsi' => 'Kendala pribadi',
            'status' => 'open',
        ]);

        // Instructor Budi tries to view Siti's ticket
        $response = $this->actingAs($this->instructor)->get(route('tickets.show', $ticket->id));
        $response->assertStatus(403);

        // Siti can view her own ticket
        $responseSiti = $this->actingAs($this->otherInstructor)->get(route('tickets.show', $ticket->id));
        $responseSiti->assertStatus(200);
        $responseSiti->assertSee('Tiket Rahasia Siti');
    }

    public function test_admin_can_view_all_tickets_and_update_status_and_staff()
    {
        $ticket = Ticket::create([
            'ticket_number' => 'TCK-202608-0055',
            'user_id' => $this->instructor->id,
            'kategori' => 'jadwal_honor',
            'judul' => 'Tiket Butuh Penanganan Admin',
            'deskripsi' => 'Deskripsi kendala',
            'status' => 'open',
        ]);

        // Admin can view
        $response = $this->actingAs($this->admin)->get(route('tickets.show', $ticket->id));
        $response->assertStatus(200);
        $response->assertSee('Kelola Status Tiket');

        // Admin updates status to in_progress and assigns staff
        $responseUpdate = $this->actingAs($this->admin)->patch(route('tickets.update-status', $ticket->id), [
            'status' => 'in_progress',
            'assigned_to' => $this->admin->id,
            'prioritas' => 'high',
        ]);

        $responseUpdate->assertRedirect(route('tickets.show', $ticket->id));

        $ticket->refresh();
        $this->assertEquals('in_progress', $ticket->status);
        $this->assertEquals($this->admin->id, $ticket->assigned_to);
        $this->assertEquals('high', $ticket->prioritas);

        // Admin resolves ticket
        $this->actingAs($this->admin)->patch(route('tickets.update-status', $ticket->id), [
            'status' => 'resolved',
        ]);

        $ticket->refresh();
        $this->assertEquals('resolved', $ticket->status);
        $this->assertNotNull($ticket->resolved_at);
    }

    public function test_ticket_reply_thread_and_unread_tracking()
    {
        Storage::fake('public');
        $replyFile = UploadedFile::fake()->create('panduan.pdf', 100, 'application/pdf');

        $ticket = Ticket::create([
            'ticket_number' => 'TCK-202608-0010',
            'user_id' => $this->instructor->id,
            'kategori' => 'teknis_error',
            'judul' => 'Kendala Upload Foto',
            'deskripsi' => 'Foto gagal terunggah',
            'status' => 'open',
            'has_unread_reply_for_admin' => true,
            'has_unread_reply_for_user' => false,
        ]);

        // 1. Admin views ticket -> admin unread flag cleared
        $this->actingAs($this->admin)->get(route('tickets.show', $ticket->id));
        $ticket->refresh();
        $this->assertFalse($ticket->has_unread_reply_for_admin);

        // 2. Admin replies -> staff reply created, auto in_progress, user unread flag set
        $responseReply = $this->actingAs($this->admin)->post(route('tickets.reply', $ticket->id), [
            'pesan' => 'Halo Budi, silakan coba kompres foto kegiatan atau gunakan format JPG.',
            'lampiran' => $replyFile,
        ]);

        $responseReply->assertRedirect(route('tickets.show', $ticket->id));

        $this->assertDatabaseHas('ticket_replies', [
            'ticket_id' => $ticket->id,
            'user_id' => $this->admin->id,
            'is_staff_reply' => true,
        ]);

        $ticket->refresh();
        $this->assertEquals('in_progress', $ticket->status);
        $this->assertTrue($ticket->has_unread_reply_for_user);
        $this->assertFalse($ticket->has_unread_reply_for_admin);

        // 3. User views ticket -> user unread flag cleared
        $this->actingAs($this->instructor)->get(route('tickets.show', $ticket->id));
        $ticket->refresh();
        $this->assertFalse($ticket->has_unread_reply_for_user);

        // 4. User replies back -> admin unread flag set
        $this->actingAs($this->instructor)->post(route('tickets.reply', $ticket->id), [
            'pesan' => 'Terima kasih Admin, sudah berhasil diunggah.',
        ]);

        $ticket->refresh();
        $this->assertTrue($ticket->has_unread_reply_for_admin);
        $this->assertEquals(2, $ticket->replies()->count());
    }
}
