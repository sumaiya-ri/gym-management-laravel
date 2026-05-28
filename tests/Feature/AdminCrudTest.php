<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Gym;
use App\Models\Trainer;
use App\Models\Service;

class AdminCrudTest extends TestCase
{
    use RefreshDatabase;

    protected $gymA;
    protected $gymB;
    protected $adminA;
    protected $adminB;
    protected $serviceA;
    protected $serviceB;
    protected $trainerA;
    protected $trainerB;

    protected function setUp(): void
    {
        parent::setUp();

        // Gym A Setup
        $this->gymA = Gym::create([
            'name' => 'Gym Alpha',
            'email' => 'alpha@gymsaas.com',
            'subscription_status' => 'active',
        ]);
        $this->adminA = User::create([
            'name' => 'Admin Alpha',
            'email' => 'admin.alpha@gymsaas.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'gym_id' => $this->gymA->id,
            'status' => 'active',
        ]);
        $this->serviceA = Service::create([
            'gym_id' => $this->gymA->id,
            'name' => 'Yoga Alpha',
            'duration' => 60,
            'status' => 'active',
        ]);
        $trainerUserA = User::create([
            'name' => 'Trainer Alpha',
            'email' => 'trainer.alpha@gymsaas.com',
            'password' => bcrypt('password'),
            'role' => 'trainer',
            'gym_id' => $this->gymA->id,
            'status' => 'active',
        ]);
        $this->trainerA = Trainer::create([
            'gym_id' => $this->gymA->id,
            'user_id' => $trainerUserA->id,
            'name' => 'Trainer Alpha',
            'specialization' => 'Yoga Specialist',
            'hourly_rate' => 45.00,
            'status' => 'active',
        ]);

        // Gym B Setup
        $this->gymB = Gym::create([
            'name' => 'Gym Beta',
            'email' => 'beta@gymsaas.com',
            'subscription_status' => 'active',
        ]);
        $this->adminB = User::create([
            'name' => 'Admin Beta',
            'email' => 'admin.beta@gymsaas.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'gym_id' => $this->gymB->id,
            'status' => 'active',
        ]);
        $this->serviceB = Service::create([
            'gym_id' => $this->gymB->id,
            'name' => 'Pilates Beta',
            'duration' => 45,
            'status' => 'active',
        ]);
        $trainerUserB = User::create([
            'name' => 'Trainer Beta',
            'email' => 'trainer.beta@gymsaas.com',
            'password' => bcrypt('password'),
            'role' => 'trainer',
            'gym_id' => $this->gymB->id,
            'status' => 'active',
        ]);
        $this->trainerB = Trainer::create([
            'gym_id' => $this->gymB->id,
            'user_id' => $trainerUserB->id,
            'name' => 'Trainer Beta',
            'specialization' => 'Pilates Instructor',
            'hourly_rate' => 50.00,
            'status' => 'active',
        ]);
    }

    /**
     * Test admin can view service edit page.
     */
    public function test_admin_can_view_service_edit_page(): void
    {
        $response = $this->actingAs($this->adminA)->get(route('admin.services.edit', $this->serviceA->id));

        $response->assertStatus(200);
        $response->assertSee('Yoga Alpha');
    }

    /**
     * Test admin can update a service.
     */
    public function test_admin_can_update_service(): void
    {
        $response = $this->actingAs($this->adminA)->put(route('admin.services.update', $this->serviceA->id), [
            'name' => 'Updated Yoga',
            'description' => 'Brand new description',
            'duration' => 75,
            'status' => 'inactive',
        ]);

        $response->assertRedirect(route('admin.services.index'));
        $this->serviceA->refresh();
        $this->assertEquals('Updated Yoga', $this->serviceA->name);
        $this->assertEquals('Brand new description', $this->serviceA->description);
        $this->assertEquals(75, $this->serviceA->duration);
        $this->assertEquals('inactive', $this->serviceA->status);
    }

    /**
     * Test admin can delete a service.
     */
    public function test_admin_can_delete_service(): void
    {
        $response = $this->actingAs($this->adminA)->delete(route('admin.services.destroy', $this->serviceA->id));

        $response->assertRedirect(route('admin.services.index'));
        $this->assertDatabaseMissing('services', ['id' => $this->serviceA->id]);
    }

    /**
     * Test admin can view trainer edit page.
     */
    public function test_admin_can_view_trainer_edit_page(): void
    {
        $response = $this->actingAs($this->adminA)->get(route('admin.trainers.edit', $this->trainerA->id));

        $response->assertStatus(200);
        $response->assertSee('Trainer Alpha');
    }

    /**
     * Test admin can update trainer details (both User and Trainer tables).
     */
    public function test_admin_can_update_trainer(): void
    {
        $response = $this->actingAs($this->adminA)->put(route('admin.trainers.update', $this->trainerA->id), [
            'name' => 'Updated Trainer Name',
            'email' => 'trainer.updated@gymsaas.com',
            'specialization' => 'Cardio Specialist',
            'hourly_rate' => 60.00,
            'status' => 'inactive',
        ]);

        $response->assertRedirect(route('admin.trainers.index'));

        // Assert User updated
        $user = $this->trainerA->user;
        $user->refresh();
        $this->assertEquals('Updated Trainer Name', $user->name);
        $this->assertEquals('trainer.updated@gymsaas.com', $user->email);
        $this->assertEquals('inactive', $user->status);

        // Assert Trainer updated
        $this->trainerA->refresh();
        $this->assertEquals('Updated Trainer Name', $this->trainerA->name);
        $this->assertEquals('Cardio Specialist', $this->trainerA->specialization);
        $this->assertEquals(60.00, $this->trainerA->hourly_rate);
        $this->assertEquals('inactive', $this->trainerA->status);
    }

    /**
     * Test admin can delete a trainer (removes both User and Trainer records).
     */
    public function test_admin_can_delete_trainer(): void
    {
        $trainerUserId = $this->trainerA->user_id;

        $response = $this->actingAs($this->adminA)->delete(route('admin.trainers.destroy', $this->trainerA->id));

        $response->assertRedirect(route('admin.trainers.index'));
        $this->assertDatabaseMissing('trainers', ['id' => $this->trainerA->id]);
        $this->assertDatabaseMissing('users', ['id' => $trainerUserId]);
    }

    /**
     * Test gym isolation: admin cannot edit or delete another gym's services.
     */
    public function test_gym_isolation_enforced_on_services(): void
    {
        // Edit attempt on another gym's service
        $response = $this->actingAs($this->adminA)->get(route('admin.services.edit', $this->serviceB->id));
        $response->assertStatus(403);

        // Update attempt on another gym's service
        $response = $this->actingAs($this->adminA)->put(route('admin.services.update', $this->serviceB->id), [
            'name' => 'Hacked Name',
            'duration' => 60,
            'status' => 'active',
        ]);
        $response->assertStatus(403);

        // Delete attempt on another gym's service
        $response = $this->actingAs($this->adminA)->delete(route('admin.services.destroy', $this->serviceB->id));
        $response->assertStatus(403);
    }

    /**
     * Test gym isolation: admin cannot edit or delete another gym's trainers.
     */
    public function test_gym_isolation_enforced_on_trainers(): void
    {
        // Edit attempt on another gym's trainer
        $response = $this->actingAs($this->adminA)->get(route('admin.trainers.edit', $this->trainerB->id));
        $response->assertStatus(403);

        // Update attempt on another gym's trainer
        $response = $this->actingAs($this->adminA)->put(route('admin.trainers.update', $this->trainerB->id), [
            'name' => 'Hacked Trainer',
            'email' => 'hacked@beta.com',
            'status' => 'active',
        ]);
        $response->assertStatus(403);

        // Delete attempt on another gym's trainer
        $response = $this->actingAs($this->adminA)->delete(route('admin.trainers.destroy', $this->trainerB->id));
        $response->assertStatus(403);
    }
}
