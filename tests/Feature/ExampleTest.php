<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test Landing Page sebelum login dapat diakses publik
     */
    public function test_guest_can_access_landing_page(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('E-RT RW BERSATU');
        $response->assertSee('Panduan Cara Memakai Aplikasi');
    }

    /**
     * Test Guest diarahkan ke login saat mengakses dashboard
     */
    public function test_guest_is_redirected_to_login_when_accessing_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Test Guest dapat mengakses halaman panduan aplikasi
     */
    public function test_guest_can_access_panduan_page(): void
    {
        $response = $this->get('/panduan');
        $response->assertStatus(200);
        $response->assertSee('Panduan Penggunaan');
    }
}
