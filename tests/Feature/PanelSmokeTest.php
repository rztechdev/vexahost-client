<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\MaintenanceSubscription;
use App\Models\Payment;
use App\Models\Project;
use App\Models\ProjectSubscription;
use App\Models\Ticket;
use App\Models\User;
use App\Services\ProjectLifecycleService;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Membuka seluruh halaman GET Admin Panel & Client Panel dengan data contoh
 * untuk memastikan tidak ada halaman yang error setelah penggabungan.
 */
class PanelSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected array $ctx = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleAndPermissionSeeder::class);

        $lead = Lead::create(['nama_usaha' => 'Toko Maju', 'nama_kontak' => 'Sari', 'kontak_wa' => '081200000000', 'status' => 'deal']);
        $project = Project::create(['lead_id' => $lead->id, 'nama_project' => 'Website Toko Maju', 'paket' => 'toko_kasir', 'harga' => 1500000, 'status' => 'dikerjakan']);
        app(ProjectLifecycleService::class)->provisionClient($project, false);
        $project->refresh();

        $payment = Payment::create(['project_id' => $project->id, 'jenis' => 'dp', 'jumlah' => 500000, 'status' => 'lunas', 'tanggal' => now()->toDateString()]);
        $maintenance = MaintenanceSubscription::create(['lead_id' => $lead->id, 'project_id' => $project->id, 'tanggal_mulai' => now(), 'tanggal_jatuh_tempo_berikutnya' => now()->addMonth()]);
        ProjectSubscription::create(['project_id' => $project->id, 'lead_id' => $lead->id, 'harga' => 300000, 'tanggal_mulai' => now(), 'tanggal_expired' => now()->addDays(10), 'status' => 'akan_expired']);
        Ticket::create(['client_id' => $project->client_id, 'title' => 'Minta revisi banner', 'description' => 'Ganti banner promo.', 'priority' => 'medium', 'status' => 'open']);

        $this->ctx = [
            'lead' => $lead,
            'project' => $project,
            'payment' => $payment,
            'subscription' => $maintenance,
            'invoice' => $project->invoices()->first(),
            'task' => $project->tasks()->first(),
            'role' => \Spatie\Permission\Models\Role::where('name', 'sales')->first(),
            'user' => User::role('admin')->first(),
        ];
    }

    protected function getRoutesFor(array $names): array
    {
        $urls = [];
        foreach ($names as $name => $params) {
            $urls[$name] = route($name, array_map(fn ($key) => $this->ctx[$key], $params));
        }

        return $urls;
    }

    public function test_all_admin_pages_render(): void
    {
        $admin = User::role('admin')->first();

        $pages = $this->getRoutesFor([
            'admin.dashboard' => [], 'admin.operations' => [],
            'admin.leads.index' => [], 'admin.leads.show' => ['lead'],
            'admin.projects.index' => [], 'admin.projects.show' => ['project'],
            'admin.payments.index' => [], 'admin.maintenance.index' => [], 'admin.subscriptions.index' => [],
            'admin.messages.index' => [], 'admin.activity-logs.index' => [],
            'admin.settings.company.edit' => [], 'admin.tickets' => [],
            'admin.users.index' => [], 'admin.users.create' => [], 'admin.users.edit' => ['user'],
            'admin.roles.index' => [], 'admin.roles.create' => [], 'admin.roles.edit' => ['role'],
            'admin.invoices.project' => ['project'], 'admin.invoices.settlement' => ['project'],
            'admin.invoices.receipt' => ['payment'], 'admin.invoices.maintenance' => ['subscription'],
            'admin.export.leads' => [], 'admin.export.projects' => [], 'admin.export.payments' => [],
            'projects.index' => [], 'projects.show' => ['project'], 'projects.create' => [], 'projects.edit' => ['project'],
            'tasks.show' => ['task'], 'tasks.edit' => ['task'],
            'invoices.index' => [], 'invoices.show' => ['invoice'],
            'technician.tickets' => [], 'profile.edit' => [],
        ]);

        $pages['tasks.create'] = route('tasks.create', ['project_id' => $this->ctx['project']->id]);

        foreach ($pages as $name => $url) {
            $status = $this->actingAs($admin)->get($url)->getStatusCode();
            $this->assertSame(200, $status, "Halaman admin [{$name}] mengembalikan HTTP {$status}");
        }
    }

    public function test_all_client_pages_render(): void
    {
        $client = $this->ctx['project']->client;

        $pages = $this->getRoutesFor([
            'client.dashboard' => [], 'projects.index' => [], 'projects.show' => ['project'],
            'tasks.show' => ['task'], 'tickets.index' => [], 'tickets.create' => [],
            'invoices.index' => [], 'invoices.show' => ['invoice'], 'invoices.receipt' => ['invoice'],
            'profile.edit' => [],
        ]);

        foreach ($pages as $name => $url) {
            $response = $this->actingAs($client)->get($url);
            $this->assertSame(200, $response->getStatusCode(), "Halaman klien [{$name}] mengembalikan HTTP {$response->getStatusCode()}");
            $response->assertDontSee('RZ Digital');
        }
    }
}
