<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Project;
use App\Models\Payment;
use App\Models\MaintenanceSubscription;
use App\Models\MessageLog;
use App\Models\User;
use App\Services\WhatsApp\WhatsAppService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrmTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class);

        $this->admin = User::firstOrCreate(
            ['email' => 'vexahostcloudtech@gmail.com'],
            ['name' => 'Owner VexaHost', 'password' => bcrypt('password')]
        );
        $this->seed(\Database\Seeders\RoleAndPermissionSeeder::class);
        $this->admin->assignRole('admin');
    }

    public function test_dashboard_renders_with_pipeline_metrics(): void
    {
        $lead = Lead::create([
            'nama_usaha' => 'Toko Sepatu Bagus',
            'kontak_wa' => '080000000001',
            'status' => 'sudah_chat',
            'paket_diminati' => 'landing_page', // 499.000
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Potensi Pipeline');
        $response->assertSee('499.000');
    }

    public function test_dashboard_closingan_separates_real_cash_received_from_unpaid_dp(): void
    {
        $lead = Lead::create([
            'nama_usaha' => 'Client Proyek DP',
            'kontak_wa' => '080000000099',
            'status' => 'deal',
            'paket_diminati' => 'custom',
        ]);

        $project = Project::create([
            'lead_id' => $lead->id,
            'nama_project' => 'Sistem Kustom Client DP',
            'paket' => 'custom',
            'harga' => 2000000,
            'status' => 'dikerjakan',
        ]);

        // Client only pays DP of Rp 500.000
        Payment::create([
            'project_id' => $project->id,
            'jenis' => 'dp',
            'jumlah' => 500000,
            'status' => 'lunas',
            'tanggal' => now()->toDateString(),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));

        $response->assertStatus(200);
        // Should see real cash received (500.000), not the full 2.000.000 as received revenue
        $response->assertSee('500.000');
        // Should show total deal and remaining balance (1.500.000)
        $response->assertSee('2.000.000');
        $response->assertSee('1.500.000');
        $response->assertSee('Sisa Piutang (Masih DP)');
    }

    public function test_lead_can_be_created_and_marked_as_deal(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.leads.store'), [
            'nama_usaha' => 'Warung Bakso Enak',
            'nama_kontak' => 'Pak Joko',
            'kontak_wa' => '080000000002',
            'sumber' => 'referral',
            'status' => 'nego',
            'paket_diminati' => 'company_profile',
            'follow_up_date' => now()->addDay()->toDateString(),
        ]);

        $lead = Lead::where('nama_usaha', 'Warung Bakso Enak')->first();
        $this->assertNotNull($lead);
        $response->assertRedirect(route('admin.leads.show', $lead));

        // Test Convert to Deal
        $convertResponse = $this->actingAs($this->admin)->post(route('admin.leads.convert-deal', $lead), [
            'nama_project' => 'Website Warung Bakso Enak',
            'harga' => 999000,
        ]);

        $lead->refresh();
        $this->assertEquals('deal', $lead->status);
        $this->assertCount(1, $lead->projects);
        $project = $lead->projects->first();
        $this->assertEquals(999000, $project->harga);
        $convertResponse->assertRedirect(route('admin.projects.show', $project));
    }

    public function test_anti_spam_guardrail_blocks_automated_wa_to_cold_leads(): void
    {
        $coldLead = Lead::create([
            'nama_usaha' => 'Cold Outreach Target',
            'kontak_wa' => '080000000003',
            'status' => 'belum_dihubungi', // Non-deal lead
            'paket_diminati' => 'landing_page',
        ]);

        $waService = app(WhatsAppService::class);

        // Attempt automated send
        $result = $waService->sendWhatsApp(
            to: $coldLead->kontak_wa,
            message: 'Pesan promosi otomatis',
            lead: $coldLead,
            tipePesan: 'promosi',
            isAutomated: true // Automated trigger
        );

        $this->assertFalse($result['success']);
        $this->assertEquals('blocked_by_guardrail', $result['status']);

        // Verify that MessageLog recorded the blocked action
        $log = MessageLog::where('lead_id', $coldLead->id)->first();
        $this->assertNotNull($log);
        $this->assertEquals('blocked_by_guardrail', $log->status_kirim);
    }

    public function test_project_dp_status_update_creates_payment_and_logs_wa(): void
    {
        $lead = Lead::create([
            'nama_usaha' => 'Klien Deal Sukses',
            'kontak_wa' => '080000000004',
            'status' => 'deal',
            'paket_diminati' => 'company_profile',
        ]);

        $project = Project::create([
            'lead_id' => $lead->id,
            'nama_project' => 'Website Klien Deal',
            'paket' => 'company_profile',
            'harga' => 1000000,
            'status' => 'draft',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.projects.update-status', $project), [
            'status' => 'dp_diterima',
            'dp_amount' => 500000,
            'send_wa' => 1,
        ]);

        $response->assertSessionHas('success');
        $project->refresh();

        $this->assertEquals('dp_diterima', $project->status);
        $this->assertEquals(500000, $project->total_paid);

        // Verify MessageLog created
        $log = MessageLog::where('lead_id', $lead->id)->where('tipe_pesan', 'invoice_dp')->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString('DP untuk project', $log->isi_pesan);
    }

    public function test_project_selesai_creates_maintenance_and_logs_wa(): void
    {
        $lead = Lead::create([
            'nama_usaha' => 'Klien Selesai Web',
            'kontak_wa' => '080000000005',
            'status' => 'deal',
            'paket_diminati' => 'landing_page',
        ]);

        $project = Project::create([
            'lead_id' => $lead->id,
            'nama_project' => 'Landing Page Klien Selesai',
            'paket' => 'landing_page',
            'harga' => 499000,
            'status' => 'review',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.projects.update-status', $project), [
            'status' => 'selesai',
            'link_website' => 'https://klienselesai.com',
            'create_maintenance' => 1,
            'send_wa' => 1,
        ]);

        $response->assertSessionHas('success');
        $project->refresh();

        $this->assertEquals('selesai', $project->status);
        $this->assertEquals('https://klienselesai.com', $project->link_website);

        // Verify Maintenance created
        $maintenance = MaintenanceSubscription::where('lead_id', $lead->id)->first();
        $this->assertNotNull($maintenance);
        $this->assertEquals('aktif', $maintenance->status);

        // Verify MessageLog created
        $log = MessageLog::where('lead_id', $lead->id)->where('tipe_pesan', 'project_selesai')->first();
        $this->assertNotNull($log);
        $this->assertStringContainsString('https://klienselesai.com', $log->isi_pesan);
    }

    public function test_project_selesai_without_create_maintenance_does_not_create_subscription(): void
    {
        $lead = Lead::create([
            'nama_usaha' => 'Klien Selesai Tanpa Maintenance',
            'kontak_wa' => '080000000088',
            'status' => 'deal',
            'paket_diminati' => 'landing_page',
        ]);

        $project = Project::create([
            'lead_id' => $lead->id,
            'nama_project' => 'Landing Page Tanpa Mnt',
            'paket' => 'landing_page',
            'harga' => 499000,
            'status' => 'review',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.projects.update-status', $project), [
            'status' => 'selesai',
            'link_website' => 'https://tanpamaintenance.com',
            'create_maintenance' => 0,
            'send_wa' => 0,
        ]);

        $response->assertSessionHas('success');
        $project->refresh();
        $this->assertEquals('selesai', $project->status);

        // Verify NO Maintenance was created
        $maintenance = MaintenanceSubscription::where('lead_id', $lead->id)->first();
        $this->assertNull($maintenance);
    }

    public function test_manual_maintenance_store_and_destroy(): void
    {
        $lead = Lead::create([
            'nama_usaha' => 'Klien Deal Maintenance Manual',
            'kontak_wa' => '080000000089',
            'status' => 'deal',
            'paket_diminati' => 'company_profile',
        ]);

        $project = Project::create([
            'lead_id' => $lead->id,
            'nama_project' => 'Website Company Profile Mnt',
            'paket' => 'company_profile',
            'harga' => 999000,
            'status' => 'selesai',
        ]);

        // 1. Admin manually creates maintenance deal
        $storeResponse = $this->actingAs($this->admin)->post(route('admin.maintenance.store'), [
            'lead_id' => $lead->id,
            'project_id' => $project->id,
            'harga_bulanan' => 150000,
            'status' => 'aktif',
            'tanggal_mulai' => now()->toDateString(),
            'tanggal_jatuh_tempo_berikutnya' => now()->addMonth()->toDateString(),
            'catatan' => 'Deal maintenance rutin atas permintaan klien',
        ]);

        $storeResponse->assertSessionHas('success');

        $sub = MaintenanceSubscription::where('lead_id', $lead->id)->first();
        $this->assertNotNull($sub);
        $this->assertEquals(150000, $sub->harga_bulanan);
        $this->assertEquals('aktif', $sub->status);

        // 2. Admin deletes maintenance subscription
        $destroyResponse = $this->actingAs($this->admin)->delete(route('admin.maintenance.destroy', $sub));
        $destroyResponse->assertSessionHas('success');

        $this->assertNull(MaintenanceSubscription::find($sub->id));
    }

    public function test_whatsapp_webhook_receives_incoming_reply(): void
    {
        $lead = Lead::create([
            'nama_usaha' => 'Klien Chat Webhook',
            'kontak_wa' => '080000000006',
            'status' => 'deal',
            'paket_diminati' => 'landing_page',
        ]);

        $payload = [
            'from' => '6280000000006',
            'message' => 'Halo mas, websitenya mantap banget!',
        ];

        $response = $this->postJson(route('webhook.whatsapp'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['success' => true, 'lead_matched' => true]);

        // Check incoming message in MessageLog
        $log = MessageLog::where('lead_id', $lead->id)->where('arah', 'masuk')->first();
        $this->assertNotNull($log);
        $this->assertEquals('Halo mas, websitenya mantap banget!', $log->isi_pesan);
        $this->assertEquals('received', $log->status_kirim);
    }

    public function test_maintenance_reminder_command_executes_successfully(): void
    {
        $lead = Lead::create([
            'nama_usaha' => 'Klien Langganan Maintenance',
            'kontak_wa' => '080000000007',
            'status' => 'deal',
            'paket_diminati' => 'company_profile',
        ]);

        $sub = MaintenanceSubscription::create([
            'lead_id' => $lead->id,
            'harga_bulanan' => 150000,
            'status' => 'aktif',
            'tanggal_mulai' => now()->subMonth(),
            'tanggal_jatuh_tempo_berikutnya' => now()->addDays(2), // H-2 (within H-3 window)
        ]);

        $this->artisan('crm:send-maintenance-reminders')
            ->expectsOutputToContain('Klien Langganan Maintenance')
            ->assertExitCode(0);

        $sub->refresh();
        $this->assertNotNull($sub->terakhir_diingatkan_at);
    }

    public function test_csv_exports_function_properly(): void
    {
        Lead::create([
            'nama_usaha' => 'Export Lead UMKM',
            'kontak_wa' => '080000000008',
            'status' => 'belum_dihubungi',
            'paket_diminati' => 'landing_page',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.export.leads'));
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Export Lead UMKM', $response->streamedContent());
    }

    public function test_invoices_and_receipts_render_properly(): void
    {
        $lead = Lead::create([
            'nama_usaha' => 'Klien Invoice Test',
            'kontak_wa' => '080000000009',
            'status' => 'deal',
            'paket_diminati' => 'landing_page',
        ]);

        $project = Project::create([
            'lead_id' => $lead->id,
            'nama_project' => 'Website Klien Invoice',
            'paket' => 'landing_page',
            'harga' => 499000,
            'status' => 'dikerjakan',
        ]);

        $payment = Payment::create([
            'project_id' => $project->id,
            'jenis' => 'dp',
            'jumlah' => 250000,
            'status' => 'lunas',
            'tanggal' => now()->toDateString(),
        ]);

        // 1. Test Project Invoice view
        $invResponse = $this->actingAs($this->admin)->get(route('admin.invoices.project', $project));
        $invResponse->assertStatus(200);
        $invResponse->assertSee('INVOICE TAGIHAN');
        $invResponse->assertSee('Klien Invoice Test');
        $invResponse->assertSee('499.000');

        // 2. Test Kwitansi view with Terbilang
        $receiptResponse = $this->actingAs($this->admin)->get(route('admin.invoices.receipt', $payment));
        $receiptResponse->assertStatus(200);
        $receiptResponse->assertSee('KWITANSI RESMI');
        $receiptResponse->assertSee('Dua Ratus Lima Puluh Ribu Rupiah');
    }

    public function test_kanban_ajax_status_update_and_quick_snooze(): void
    {
        $lead = Lead::create([
            'nama_usaha' => 'Lead Kanban Test',
            'kontak_wa' => '080000000010',
            'status' => 'belum_dihubungi',
            'paket_diminati' => 'company_profile',
        ]);

        // AJAX update status to 'nego'
        $response = $this->actingAs($this->admin)->postJson(route('admin.leads.kanban-status', $lead), [
            'status' => 'nego',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $lead->refresh();
        $this->assertEquals('nego', $lead->status);

        // Quick Snooze +3 days
        $snoozeResponse = $this->actingAs($this->admin)->postJson(route('admin.leads.quick-followup', $lead), [
            'days' => '3',
        ]);
        $snoozeResponse->assertStatus(200);
        $lead->refresh();
        $this->assertEquals(now()->addDays(3)->toDateString(), $lead->follow_up_date->toDateString());
    }

    public function test_activity_logs_are_recorded_and_viewable(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.activity-logs.index'));
        $response->assertStatus(200);
        $response->assertSee('Audit Trail &amp; Activity Log', false);
    }
}
