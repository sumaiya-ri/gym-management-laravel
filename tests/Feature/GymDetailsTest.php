<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Gym;

class GymDetailsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a gym and admin user
        $this->gym = Gym::create([
            'name' => 'Sana Pilates',
            'email' => 'sana@gmail.com',
            'phone' => '123456789',
            'address' => 'Colombo',
        ]);

        $this->admin = User::create([
            'name' => 'Sana',
            'email' => 'sana@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'gym_id' => $this->gym->id,
        ]);
    }

    public function test_admin_can_view_gym_details(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.gym.show'));

        $response->assertStatus(200);
        $response->assertSee('Sana Pilates');
        $response->assertSee('sana@gmail.com');
    }

    public function test_admin_can_edit_gym_details(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.gym.edit'));

        $response->assertStatus(200);
        $response->assertSee('Sana Pilates');
    }

    public function test_admin_can_update_gym_details(): void
    {
        $response = $this->actingAs($this->admin)->put(route('admin.gym.update'), [
            'name' => 'Sana Pilates Revised',
            'email' => 'revised@gmail.com',
            'phone' => '987654321',
            'address' => 'Kandy',
        ]);

        $response->assertRedirect(route('admin.gym.show'));
        $this->gym->refresh();
        $this->assertEquals('Sana Pilates Revised', $this->gym->name);
        $this->assertEquals('revised@gmail.com', $this->gym->email);
    }

    public function test_guest_cannot_access_gym_details(): void
    {
        $response = $this->get(route('admin.gym.show'));
        $response->assertRedirect('/login');
    }
}
