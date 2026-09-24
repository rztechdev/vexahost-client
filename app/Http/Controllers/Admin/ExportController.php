<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Lead;
use App\Models\Project;
use App\Models\Payment;
use App\Models\MaintenanceSubscription;
use App\Models\CompanySetting;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class ExportController extends Controller
{
    /**
     * Export Leads to CSV, PDF, or Word (.doc).
     */
    public function exportLeads(Request $request)
    {
        $format = strtolower($request->get('format', 'csv'));

        $query = Lead::query();
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('sumber')) {
            $query->where('sumber', $request->sumber);
        }

        $settings = CompanySetting::get();
        $dateStr = date('Ymd-His');

        if ($format === 'pdf') {
            ActivityLogger::log('export_leads_pdf', 'Mengekspor data Leads ke file PDF');
            $leads = $query->latest()->get();
            $data = [
                'leads' => $leads,
                'settings' => $settings,
                'logoBase64' => $settings->logo_base64,
                'signatureBase64' => $settings->signature_base64,
                'isPdf' => true,
            ];
            $pdf = Pdf::loadView('admin.exports.leads-pdf', $data)->setPaper('a4', 'landscape');
            return $pdf->download("Laporan-Database-Leads-{$dateStr}.pdf");
        }

        if ($format === 'word' || $format === 'doc') {
            ActivityLogger::log('export_leads_word', 'Mengekspor data Leads ke file Word (.doc)');
            $leads = $query->latest()->get();
            $data = [
                'leads' => $leads,
                'settings' => $settings,
                'logoBase64' => $settings->logo_base64,
                'signatureBase64' => $settings->signature_base64,
                'isPdf' => true,
            ];
            $html = view('admin.exports.leads-pdf', $data)->render();
            $fileName = "Laporan-Database-Leads-{$dateStr}.doc";
            return response($html, 200, [
                'Content-Type' => 'application/msword; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        }

        // Default: CSV Stream
        ActivityLogger::log('export_leads', 'Mengekspor data Leads ke file CSV/Excel');
        $fileName = "Laporan-Database-Leads-{$dateStr}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'ID',
                'Nama Usaha',
                'Nama Kontak',
                'Nomor WhatsApp',
                'Sumber',
                'Paket Diminati',
                'Estimasi Nilai (Rp)',
                'Status',
                'Jadwal Follow-up',
                'Catatan',
                'Tanggal Dibuat',
            ]);

            $query->chunk(100, function ($leads) use ($handle) {
                foreach ($leads as $lead) {
                    fputcsv($handle, [
                        $lead->id,
                        $lead->nama_usaha,
                        $lead->nama_kontak ?? '-',
                        $lead->kontak_wa,
                        $lead->sumber_label,
                        $lead->paket_label,
                        $lead->getDefaultPackagePrice(),
                        $lead->status_label,
                        $lead->follow_up_date ? $lead->follow_up_date->format('Y-m-d') : '-',
                        $lead->catatan ?? '-',
                        $lead->created_at->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export Projects to CSV, PDF, or Word (.doc).
     */
    public function exportProjects(Request $request)
    {
        $format = strtolower($request->get('format', 'csv'));

        $query = Project::with('lead', 'payments');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $settings = CompanySetting::get();
        $dateStr = date('Ymd-His');

        if ($format === 'pdf') {
            ActivityLogger::log('export_projects_pdf', 'Mengekspor data Projects ke file PDF');
            $projects = $query->latest()->get();
            $data = [
                'projects' => $projects,
                'settings' => $settings,
                'logoBase64' => $settings->logo_base64,
                'signatureBase64' => $settings->signature_base64,
                'isPdf' => true,
            ];
            $pdf = Pdf::loadView('admin.exports.projects-pdf', $data)->setPaper('a4', 'landscape');
            return $pdf->download("Laporan-Rekapitulasi-Proyek-{$dateStr}.pdf");
        }

        if ($format === 'word' || $format === 'doc') {
            ActivityLogger::log('export_projects_word', 'Mengekspor data Projects ke file Word (.doc)');
            $projects = $query->latest()->get();
            $data = [
                'projects' => $projects,
                'settings' => $settings,
                'logoBase64' => $settings->logo_base64,
                'signatureBase64' => $settings->signature_base64,
                'isPdf' => true,
            ];
            $html = view('admin.exports.projects-pdf', $data)->render();
            $fileName = "Laporan-Rekapitulasi-Proyek-{$dateStr}.doc";
            return response($html, 200, [
                'Content-Type' => 'application/msword; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        }

        // Default: CSV Stream
        ActivityLogger::log('export_projects', 'Mengekspor data Projects ke file CSV/Excel');
        $fileName = "Laporan-Rekapitulasi-Proyek-{$dateStr}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'ID Project',
                'Nama Project',
                'Nama Klien / Usaha',
                'Kontak WA',
                'Paket',
                'Nilai Project (Rp)',
                'Total Terbayar (Rp)',
                'Sisa Tagihan (Rp)',
                'Status Project',
                'Link Website',
                'Tanggal Mulai',
                'Tanggal Selesai',
                'Tanggal Dibuat',
            ]);

            $query->chunk(100, function ($projects) use ($handle) {
                foreach ($projects as $project) {
                    fputcsv($handle, [
                        $project->id,
                        $project->nama_project,
                        $project->lead?->nama_usaha ?? '-',
                        $project->lead?->kontak_wa ?? '-',
                        $project->paket_label,
                        $project->harga,
                        $project->total_terbayar,
                        $project->sisa_tagihan,
                        $project->status_label,
                        $project->link_website ?? '-',
                        $project->tanggal_mulai ? $project->tanggal_mulai->format('Y-m-d') : '-',
                        $project->tanggal_selesai ? $project->tanggal_selesai->format('Y-m-d') : '-',
                        $project->created_at->format('Y-m-d H:i'),
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Export Payments to CSV, PDF, or Word (.doc).
     */
    public function exportPayments(Request $request)
    {
        $format = strtolower($request->get('format', 'csv'));

        $query = Payment::with('project.lead');
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $settings = CompanySetting::get();
        $dateStr = date('Ymd-His');

        if ($format === 'pdf') {
            ActivityLogger::log('export_payments_pdf', 'Mengekspor data Pembayaran ke file PDF');
            $payments = $query->latest('tanggal')->get();
            $data = [
                'payments' => $payments,
                'settings' => $settings,
                'logoBase64' => $settings->logo_base64,
                'signatureBase64' => $settings->signature_base64,
                'isPdf' => true,
            ];
            $pdf = Pdf::loadView('admin.exports.payments-pdf', $data)->setPaper('a4', 'landscape');
            return $pdf->download("Laporan-Transaksi-Pembayaran-{$dateStr}.pdf");
        }

        if ($format === 'word' || $format === 'doc') {
            ActivityLogger::log('export_payments_word', 'Mengekspor data Pembayaran ke file Word (.doc)');
            $payments = $query->latest('tanggal')->get();
            $data = [
                'payments' => $payments,
                'settings' => $settings,
                'logoBase64' => $settings->logo_base64,
                'signatureBase64' => $settings->signature_base64,
                'isPdf' => true,
            ];
            $html = view('admin.exports.payments-pdf', $data)->render();
            $fileName = "Laporan-Transaksi-Pembayaran-{$dateStr}.doc";
            return response($html, 200, [
                'Content-Type' => 'application/msword; charset=UTF-8',
                'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
        }

        // Default: CSV Stream
        ActivityLogger::log('export_payments', 'Mengekspor data Pembayaran ke file CSV/Excel');
        $fileName = "Laporan-Transaksi-Pembayaran-{$dateStr}.csv";

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, [
                'ID Transaksi',
                'Nama Project',
                'Klien / Usaha',
                'Jenis Pembayaran',
                'Nominal (Rp)',
                'Status',
                'Tanggal Transaksi',
                'Catatan',
            ]);

            $query->chunk(100, function ($payments) use ($handle) {
                foreach ($payments as $payment) {
                    fputcsv($handle, [
                        $payment->id,
                        $payment->project?->nama_project ?? '-',
                        $payment->project?->lead?->nama_usaha ?? '-',
                        $payment->jenis_label,
                        $payment->jumlah,
                        $payment->status_label,
                        $payment->tanggal ? $payment->tanggal->format('Y-m-d') : '-',
                        $payment->catatan ?? '-',
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
