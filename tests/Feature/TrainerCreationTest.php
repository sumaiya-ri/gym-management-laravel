<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Gym;
use App\Models\Trainer;

class TrainerCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->gym = Gym::create([
            'name' => 'Sana Pilates',
            'email' => 'sana@gmail.com',
        ]);

        $this->admin = User::create([
            'name' => 'Sana',
            'email' => 'sana@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'gym_id' => $this->gym->id,
        ]);
    }

    public function test_admin_can_view_trainer_creation_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.trainers.create'));
        $response->assertStatus(200);
        $response->assertSee('Trainer Email');
    }

    public function test_admin_can_create_trainer_with_user_linkage(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.trainers.store'), [
            'name' => 'John Doe',
            'email' => 'johndoe@gymsaas.com',
            'specialization' => 'Yoga Specialist',
            'hourly_rate' => 50,
            'status' => 'active',
        ]);

        $response->assertRedirect(route('admin.trainers.index'));

        // Assert user was created
        $user = User::where('email', 'johndoe@gymsaas.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('trainer', $user->role);
        $this->assertEquals($this->gym->id, $user->gym_id);

        // Assert trainer was created and linked
        $trainer = Trainer::where('user_id', $user->id)->first();
        $this->assertNotNull($trainer);
        $this->assertEquals('John Doe', $trainer->name);
        $this->assertEquals('Yoga Specialist', $trainer->specialization);
        $this->assertEquals(50, $trainer->hourly_rate);
    }
}
