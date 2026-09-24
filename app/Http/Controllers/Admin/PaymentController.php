<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Payment;
use App\Models\Project;
use App\Models\MaintenanceSubscription;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['project.lead']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('jenis')) {
            $query->where('jenis', $request->jenis);
        }

        $payments = $query->latest('tanggal')->paginate(15)->withQueryString();

        $totalLunas = Payment::where('status', 'lunas')->sum('jumlah');
        $totalPending = Payment::where('status', 'pending')->sum('jumlah');

        return view('admin.payments.index', compact('payments', 'totalLunas', 'totalPending'));
    }

    /**
     * Store a newly created payment for a project.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'jenis' => 'required|in:dp,pelunasan,maintenance,lainnya',
            'jumlah' => 'required|numeric|min:1',
            'status' => 'required|in:pending,lunas',
            'tanggal' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        $payment = Payment::create($validated);
        $project = $payment->project;

        // Auto-advance maintenance due date if payment is lunas
        if ($payment->jenis === 'maintenance' && $payment->status === 'lunas' && $project) {
            $this->advanceMaintenanceDueDate($project);
        }


        ActivityLogger::log('payment_created', "Mencatat pembayaran {$payment->jenis_label} sebesar Rp " . number_format($payment->jumlah, 0, ',', '.') . " untuk project {$project?->nama_project}", 'Payment', $payment->id);

        return back()->with('success', "Pembayaran Rp " . number_format($payment->jumlah, 0, ',', '.') . " berhasil dicatat.");
    }

    /**
     * Update payment status (e.g. pending -> lunas).
     */
    public function updateStatus(Request $request, Payment $payment)
    {
        $request->validate([
            'status' => 'required|in:pending,lunas',
        ]);

        $oldStatus = $payment->status;
        $payment->update(['status' => $request->status]);
        $project = $payment->project;

        // Auto-advance maintenance due date if changed to lunas
        if ($payment->jenis === 'maintenance' && $payment->status === 'lunas' && $oldStatus !== 'lunas') {
            if ($project) {
                $this->advanceMaintenanceDueDate($project);
            }
        }


        ActivityLogger::log('payment_status_changed', "Status pembayaran ID #{$payment->id} diubah dari {$oldStatus} menjadi {$payment->status}", 'Payment', $payment->id);

        return back()->with('success', "Status pembayaran berhasil diubah menjadi: " . strtoupper($payment->status));
    }

    /**
     * Remove the specified payment.
     */
    public function destroy(Payment $payment)
    {
        $id = $payment->id;
        $jumlah = $payment->jumlah;
        $project = $payment->project;
        $payment->delete();


        ActivityLogger::log('payment_deleted', "Menghapus data pembayaran ID #{$id} senilai Rp " . number_format($jumlah, 0, ',', '.'), 'Payment', $id);

        return back()->with('success', "Data pembayaran berhasil dihapus.");
    }

    /**
     * Helper to advance maintenance subscription due date when paid.
     */
    private function advanceMaintenanceDueDate(Project $project): void
    {
        $subscription = $project->maintenanceSubscription 
            ?: MaintenanceSubscription::where('lead_id', $project->lead_id)->where('status', 'aktif')->first();

        if ($subscription) {
            $currentDue = $subscription->tanggal_jatuh_tempo_berikutnya 
                ? \Carbon\Carbon::parse($subscription->tanggal_jatuh_tempo_berikutnya) 
                : now();
            
            $newDue = $currentDue->isPast() ? now()->addMonth() : $currentDue->copy()->addMonth();

            $subscription->update([
                'tanggal_jatuh_tempo_berikutnya' => $newDue->toDateString(),
                'status' => 'aktif',
            ]);

            ActivityLogger::log('maintenance_renewed', "Jatuh tempo maintenance {$project->lead?->nama_usaha} diperpanjang otomatis hingga " . $newDue->translatedFormat('d F Y'), 'MaintenanceSubscription', $subscription->id);
        }
    }
}
