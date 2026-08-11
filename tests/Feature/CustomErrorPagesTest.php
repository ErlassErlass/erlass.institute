<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\View;
use Tests\TestCase;

class CustomErrorPagesTest extends TestCase
{
    public function test_error_404_view_renders_correctly(): void
    {
        $this->assertTrue(View::exists('errors.404'));
        $content = view('errors.404')->render();
        
        $this->assertStringContainsString('Halaman Nyasar', $content);
        $this->assertStringContainsString('kucing virtual kami', strtolower($content));
    }

    public function test_error_500_view_renders_correctly(): void
    {
        $this->assertTrue(View::exists('errors.500'));
        $content = view('errors.500')->render();

        $this->assertStringContainsString('Mesinnya Batuk', $content);
        $this->assertStringContainsString('robot peladen kami', strtolower($content));
    }

    public function test_error_403_view_renders_correctly(): void
    {
        $this->assertTrue(View::exists('errors.403'));
        $content = view('errors.403')->render();

        $this->assertStringContainsString('Hayo, Mau Ke Mana?', $content);
    }

    public function test_error_503_maintenance_view_renders_correctly(): void
    {
        $this->assertTrue(View::exists('errors.503'));
        $content = view('errors.503')->render();

        $this->assertStringContainsString('Pemeliharaan Berkala', $content);
        $this->assertStringContainsString('Erlass Institute', $content);
    }

    public function test_error_419_page_expired_view_renders_correctly(): void
    {
        $this->assertTrue(View::exists('errors.419'));
        $content = view('errors.419')->render();

        $this->assertStringContainsString('Waktu Sesi Telah Berakhir', $content);
        $this->assertStringContainsString('CSRF Token', $content);
    }

    public function test_error_429_too_many_requests_view_renders_correctly(): void
    {
        $this->assertTrue(View::exists('errors.429'));
        $content = view('errors.429')->render();

        $this->assertStringContainsString('Pelan-Pelan Ya!', $content);
        $this->assertStringContainsString('terlalu banyak permintaan', strtolower($content));
    }

    public function test_error_401_unauthorized_view_renders_correctly(): void
    {
        $this->assertTrue(View::exists('errors.401'));
        $content = view('errors.401')->render();

        $this->assertStringContainsString('Silakan Login Terlebih Dahulu', $content);
        $this->assertStringContainsString('Ke Halaman Login', $content);
    }
}
