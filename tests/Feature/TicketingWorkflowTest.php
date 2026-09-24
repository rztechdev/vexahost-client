<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Ticket;
use App\Models\Project;
use App\Models\Task;
use App\Notifications\ProjectCreatedNotification;
use App\Notifications\TaskAssignedNotification;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TicketingWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Run roles and permissions seeder
        $this->seed(RoleAndPermissionSeeder::class);

        // Satu pengguna contoh untuk setiap role
        foreach (['client', 'technician', 'ceo'] as $role) {
            User::factory()->create(['email' => $role . '@example.test'])->assignRole($role);
        }
    }

    public function test_client_can_submit_ticket_and_cannot_create_project(): void
    {
        $client = User::role('client')->first();

        // 1. Client creates a ticket
        $response = $this->actingAs($client)
            ->post(route('tickets.store'), [
                'title' => 'Koneksi Internet Lambat',
                'description' => 'Sudah 3 hari koneksi internet sangat lambat.',
                'priority' => 'medium',
            ]);

        $response->assertRedirect(route('tickets.index'));
        $this->assertDatabaseHas('tickets', [
            'title' => 'Koneksi Internet Lambat',
            'client_id' => $client->id,
        ]);

        // 2. Client attempts to access project creation page (should be forbidden)
        $response = $this->actingAs($client)
            ->get(route('projects.create'));
        $response->assertStatus(403);

        // 3. Client attempts to store a project (should be forbidden)
        $response = $this->actingAs($client)
            ->post(route('projects.store'), [
                'name' => 'Proyek Ilegal',
                'client_id' => $client->id,
                'status' => 'active',
            ]);
        $response->assertStatus(403);
    }

    public function test_admin_can_view_ticket_and_create_project_from_ticket(): void
    {
        $admin = User::role('admin')->first();
        $client = User::role('client')->first();

        // Create a ticket for testing
        $ticket = Ticket::create([
            'client_id' => $client->id,
            'title' => 'Server Down',
            'description' => 'Server database mati mendadak.',
            'priority' => 'high',
            'status' => 'open',
        ]);

        // 1. Admin views the admin tickets index
        $response = $this->actingAs($admin)
            ->get(route('admin.tickets'));
        $response->assertOk();
        $response->assertSee('Server Down');

        // 2. Admin creates a project associated with the ticket
        $response = $this->actingAs($admin)
            ->post(route('projects.store'), [
                'ticket_id' => $ticket->id,
                'name' => 'Perbaikan Server Database',
                'description' => 'Investigasi dan perbaikan server database.',
                'work_status' => 'active',
                'client_id' => $client->id,
                'start_date' => now()->format('Y-m-d'),
                'end_date' => now()->addDays(7)->format('Y-m-d'),
            ]);

        $response->assertRedirect();
        
        $this->assertDatabaseHas('projects', [
            'name' => 'Perbaikan Server Database',
            'ticket_id' => $ticket->id,
            'client_id' => $client->id,
        ]);

        // Verify association
        $project = Project::where('ticket_id', $ticket->id)->first();
        $this->assertNotNull($project);
        $this->assertEquals($ticket->id, $project->ticket_id);

        $this->assertEquals('pending', $ticket->fresh()->status);
    }

    public function test_creating_project_from_ticket_notifies_client_and_syncs_technician(): void
    {
        Notification::fake();

        $admin = User::role('admin')->first();
        $client = User::role('client')->first();
        $tech = User::role('technician')->first();

        $ticket = Ticket::create([
            'client_id' => $client->id,
            'title' => 'Email tidak masuk',
            'description' => 'Mailbox error.',
            'priority' => 'medium',
            'status' => 'open',
        ]);

        $this->actingAs($admin)
            ->post(route('projects.store'), [
                'ticket_id' => $ticket->id,
                'name' => 'Perbaikan Email',
                'description' => 'Investigasi mailbox.',
                'work_status' => 'active',
                'client_id' => $client->id,
                'manager_id' => $tech->id,
                'start_date' => now()->format('Y-m-d'),
            ])
            ->assertRedirect();

        $ticket->refresh();
        $this->assertEquals('pending', $ticket->status);
        $this->assertEquals($tech->id, $ticket->technician_id);
        $this->assertNotNull($ticket->first_response_at);

        Notification::assertSentTo($client, ProjectCreatedNotification::class);
        Notification::assertSentTo($tech, ProjectCreatedNotification::class);
    }

    public function test_assigning_task_notifies_technician_and_updates_ticket_pic(): void
    {
        Notification::fake();

        $admin = User::role('admin')->first();
        $client = User::role('client')->first();
        $tech = User::role('technician')->first();

        $ticket = Ticket::create([
            'client_id' => $client->id,
            'title' => 'VPN putus',
            'description' => 'Koneksi VPN sering drop.',
            'priority' => 'high',
            'status' => 'pending',
        ]);

        $project = Project::create([
            'name' => 'Stabilkan VPN',
            'client_id' => $client->id,
            'ticket_id' => $ticket->id,
            'work_status' => 'active',
        ]);

        $this->actingAs($admin)
            ->post(route('tasks.store'), [
                'project_id' => $project->id,
                'name' => 'Cek konfigurasi VPN',
                'status' => 'todo',
                'priority' => 'high',
                'assignee_id' => $tech->id,
            ])
            ->assertRedirect();

        $this->assertEquals($tech->id, $ticket->fresh()->technician_id);
        Notification::assertSentTo($tech, TaskAssignedNotification::class);
    }

    public function test_completing_project_resolves_linked_ticket(): void
    {
        Notification::fake();

        $admin = User::role('admin')->first();
        $client = User::role('client')->first();

        $ticket = Ticket::create([
            'client_id' => $client->id,
            'title' => 'Printer Rusak',
            'description' => 'Printer kantor tidak bisa print.',
            'priority' => 'medium',
            'status' => 'open',
        ]);

        $project = Project::create([
            'name' => 'Perbaikan Printer',
            'description' => 'Ganti cartridge dan driver.',
            'work_status' => 'active',
            'client_id' => $client->id,
            'ticket_id' => $ticket->id,
        ]);

        $ticket->update(['status' => 'pending']);

        $response = $this->actingAs($admin)
            ->put(route('projects.update', $project), [
                'name' => $project->name,
                'description' => $project->description,
                'work_status' => 'completed',
                'client_id' => $client->id,
            ]);

        $response->assertRedirect();

        $ticket->refresh();
        $this->assertEquals('resolved', $ticket->status);
        $this->assertNotNull($ticket->resolved_at);

        Notification::assertSentTo($client, \App\Notifications\TicketStatusUpdatedNotification::class);
    }

    public function test_technician_can_only_view_assigned_projects_and_update_assigned_tasks_progress(): void
    {
        $admin = User::role('admin')->first();
        $tech = User::role('technician')->first();
        $client = User::role('client')->first();

        // 1. Create a project
        $project = Project::create([
            'name' => 'Upgrade Jaringan',
            'description' => 'Upgrade router dan switch.',
            'work_status' => 'active',
            'client_id' => $client->id,
        ]);

        // 2. Create a task assigned to tech
        $assignedTask = Task::create([
            'project_id' => $project->id,
            'name' => 'Config Router',
            'description' => 'Konfigurasi router utama.',
            'priority' => 'high',
            'status' => 'todo',
            'assignee_id' => $tech->id,
            'due_date' => now()->addDays(2)->format('Y-m-d'),
        ]);

        // Create a task not assigned to tech
        $unassignedTask = Task::create([
            'project_id' => $project->id,
            'name' => 'Dokumentasi Kabel',
            'description' => 'Rapikan dokumentasi kabel.',
            'priority' => 'low',
            'status' => 'todo',
            'assignee_id' => null,
        ]);

        // 3. Technician views project detail
        // Technician should see the project since they have an assigned task in it.
        $response = $this->actingAs($tech)
            ->get(route('projects.show', $project));
        $response->assertOk();
        $response->assertSee('Config Router');

        // 4. Technician attempts to update the project itself (should be forbidden)
        $response = $this->actingAs($tech)
            ->put(route('projects.update', $project), [
                'name' => 'Nama Baru Proyek',
                'client_id' => $client->id,
                'status' => 'completed',
            ]);
        $response->assertStatus(403);

        // 5. Technician updates their assigned task's progress
        $response = $this->actingAs($tech)
            ->patch(route('tasks.progress', $assignedTask), [
                'status' => 'in_progress',
            ]);
        $response->assertRedirect();
        $this->assertEquals('in_progress', $assignedTask->fresh()->status);

        // 6. Technician attempts to update unassigned task's progress (should be forbidden)
        $response = $this->actingAs($tech)
            ->patch(route('tasks.progress', $unassignedTask), [
                'status' => 'in_progress',
            ]);
        $response->assertStatus(403);
    }

    public function test_ceo_can_view_dashboard_and_projects_but_cannot_manage(): void
    {
        $ceo = User::role('ceo')->first();

        $this->actingAs($ceo)
            ->get(route('admin.operations'))
            ->assertOk();

        $this->actingAs($ceo)
            ->get(route('api.kpi-data'))
            ->assertOk();

        $this->actingAs($ceo)
            ->get(route('projects.index'))
            ->assertOk();

        $this->actingAs($ceo)
            ->get(route('projects.create'))
            ->assertStatus(403);

        $this->actingAs($ceo)
            ->get(route('admin.tickets'))
            ->assertStatus(403);
    }
}
