<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class CBTSystemTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $this->markTestSkipped('Admin user not found in database.');
        }

        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);
    }

    public function test_guru_can_access_dashboard(): void
    {
        $guru = User::where('role', 'guru')->first();
        if (!$guru) {
            $this->markTestSkipped('Guru user not found in database.');
        }

        $response = $this->actingAs($guru)->get('/guru/dashboard');
        $response->assertStatus(200);
    }

    public function test_murid_can_access_dashboard(): void
    {
        $murid = User::where('role', 'murid')->first();
        if (!$murid) {
            $this->markTestSkipped('Murid user not found in database.');
        }

        $response = $this->actingAs($murid)->get('/dashboard');
        $response->assertStatus(200);
    }
    
    public function test_murid_cannot_access_admin_dashboard(): void
    {
        $murid = User::where('role', 'murid')->first();
        $response = $this->actingAs($murid)->get('/admin/dashboard');
        $response->assertStatus(403);
    }
    
    public function test_admin_audio_explorer(): void
    {
        $admin = User::where('role', 'admin')->first();
        $response = $this->actingAs($admin)->get('/admin/audio');
        $response->assertStatus(200);
    }
}
