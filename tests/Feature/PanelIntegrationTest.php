<?php

namespace Tests\Feature;

use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Project;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Alur gabungan Admin Panel (CRM) ↔ Client Panel dalam satu aplikasi.
 */
class PanelIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);
        $this->seed(RoleAndPermissionSeeder::class);
        $this->admin = User::role('admin')->firstOrFail();
    }

    protected function makeDealProject(int $harga = 1000000): Project
    {
        $lead = Lead::create([
            'nama_usaha' => 'Kopi Senja',
            'nama_kontak' => 'Budi',
            'kontak_wa' => '081234567890',
            'status' => 'deal',
        ]);

        return Project::create([
            'lead_id' => $lead->id,
            'nama_project' => 'Website Kopi Senja',
            'paket' => 'company_profile',
            'harga' => $harga,
            'status' => 'draft',
        ]);
    }

    public function test_home_shows_login_with_landing_page(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Masuk Portal')
            ->assertSee('Fitur Utama')
            ->assertSee('All rights reserved. Created by vexahostcloud.')
            ->assertSee('wa.vexahostcloud.my.id')
            ->assertSee('build.vexahostcloud.my.id')
            ->assertDontSee('instagram.com', false)
            ->assertDontSee('mailto:', false)
            ->assertDontSee('RZ Digital');
    }

    public function test_register_is_closed(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_staff_and_client_are_sent_to_their_own_panel(): void
    {
        $this->actingAs($this->admin)->get(route('dashboard'))->assertRedirect(route('admin.dashboard'));

        $client = User::factory()->create();
        $client->assignRole('client');

        $this->actingAs($client)->get(route('dashboard'))->assertRedirect(route('client.dashboard'));
        $this->actingAs($client)->get(route('client.dashboard'))->assertOk();
        $this->actingAs($client)->get(route('admin.leads.index'))->assertRedirect(route('client.dashboard'));
    }

    public function test_provisioning_creates_client_account_task_and_invoice(): void
    {
        $project = $this->makeDealProject();

        $this->actingAs($this->admin)
            ->post(route('admin.projects.provision-client', $project), ['send_wa' => 0])
            ->assertRedirect();

        $project->refresh();
        $this->assertNotNull($project->client_id);
        $this->assertTrue($project->client->hasRole('client'));
        $this->assertStringEndsWith('@client.vexahostcloud.my.id', $project->client->email);
        $this->assertSame(1, $project->tasks()->count());

        $invoice = $project->invoices()->first();
        $this->assertNotNull($invoice);
        $this->assertEquals(1000000, (float) $invoice->amount);
        $this->assertSame('unpaid', $invoice->status);
    }

    public function test_crm_payment_updates_client_invoice(): void
    {
        $project = $this->makeDealProject();
        app(\App\Services\ProjectLifecycleService::class)->provisionClient($project, false);

        Payment::create([
            'project_id' => $project->id,
            'jenis' => 'dp',
            'jumlah' => 400000,
            'status' => 'lunas',
            'tanggal' => now()->toDateString(),
        ]);

        $invoice = Invoice::where('project_id', $project->id)->first();
        $this->assertEquals(400000, (float) $invoice->paid_amount);
        $this->assertEquals(600000, (float) $invoice->balance_due);
        $this->assertSame('partially_paid', $invoice->status);
    }

    public function test_client_invoice_verification_records_crm_payment(): void
    {
        $project = $this->makeDealProject();
        app(\App\Services\ProjectLifecycleService::class)->provisionClient($project, false);
        $invoice = Invoice::where('project_id', $project->id)->first();

        $this->actingAs($this->admin)
            ->post(route('invoices.verify', $invoice), ['action' => 'approve_dp', 'dp_amount' => 500000])
            ->assertRedirect();

        $this->assertSame(500000, $project->fresh()->total_paid);
        $this->assertSame('dp_diterima', $project->fresh()->status);
        $this->assertSame('partially_paid', $invoice->fresh()->status);
    }

    public function test_kanban_move_updates_crm_project_status(): void
    {
        $project = $this->makeDealProject();
        app(\App\Services\ProjectLifecycleService::class)->provisionClient($project, false);
        $task = $project->tasks()->first();

        $this->actingAs($this->admin)
            ->patchJson(route('tasks.progress', $task), ['status' => 'in_progress', 'send_wa' => false])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertSame('dikerjakan', $project->fresh()->status);
        $this->assertSame('active', $project->fresh()->work_status);
    }
}
