<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;
    public function test_login_page_contains_forgot_password_link(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Lupa Password?');
        $response->assertSee(route('password.request'));
    }

    public function test_forgot_password_page_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');
        $response->assertStatus(200);
        $response->assertSee('Pemulihan Kata Sandi');
        $response->assertSee('Nomor Induk Kependudukan');
    }

    public function test_user_can_verify_identity_and_reset_password_successfully(): void
    {
        $warga = User::where('role', 'Warga')->first();
        if (!$warga) {
            $warga = User::create([
                'nama' => 'Warga Lupa Password Test',
                'username' => 'warga.test.lupa',
                'email' => 'warga.lupa@example.com',
                'password' => bcrypt('PasswordLama123'),
                'role' => 'Warga',
                'nik' => '3275019900112233',
                'no_kk' => '3275019900112200',
                'rt' => '06',
            ]);
        }

        // 1. Submit form verifikasi identitas
        $response = $this->post('/forgot-password', [
            'identitas' => $warga->username,
            'nik' => $warga->nik,
            'no_kk' => $warga->no_kk,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('password_reset_tokens', [
            'email' => $warga->email,
        ]);

        // 2. Ambil token dari session redirect URL
        $redirectUrl = $response->headers->get('Location');
        $this->assertStringContainsString('/reset-password/', $redirectUrl);

        // Parse token dari URL
        preg_match('/reset-password\/([^?]+)/', $redirectUrl, $matches);
        $token = $matches[1] ?? null;
        $this->assertNotEmpty($token);

        // 3. Akses halaman reset password
        $responseResetPage = $this->get($redirectUrl);
        $responseResetPage->assertStatus(200);
        $responseResetPage->assertSee('Buat Kata Sandi Baru');

        // 4. Submit password baru
        $responseUpdate = $this->post('/reset-password', [
            'token' => $token,
            'email' => $warga->email,
            'password' => 'PasswordBaru999',
            'password_confirmation' => 'PasswordBaru999',
        ]);

        $responseUpdate->assertRedirect('/login');
        $responseUpdate->assertSessionHas('success');

        // 5. Pastikan password baru tersimpan dan bisa login
        $this->assertTrue(Hash::check('PasswordBaru999', $warga->fresh()->password));

        $loginResponse = $this->post('/login', [
            'login' => $warga->username,
            'password' => 'PasswordBaru999',
        ]);

        $loginResponse->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($warga);
    }
}
