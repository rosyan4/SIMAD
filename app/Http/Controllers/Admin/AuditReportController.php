<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditReport;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AuditReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin.utama']);
    }

    /**
     * Display audit reports with filters
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $perPage = $request->get('per_page', 20);
        $type = $request->get('type', 'all'); // all, follow-up, overdue
        
        $query = AuditReport::with(['asset', 'auditor']);
        
        if ($type === 'follow-up') {
            $query->where('status', AuditReport::STATUS_FOLLOW_UP);
        } elseif ($type === 'overdue') {
            $query->where('status', AuditReport::STATUS_FOLLOW_UP)
                  ->where('follow_up_deadline', '<', now()->toDateString());
        }
        
        if ($status && in_array($status, AuditReport::STATUSES)) {
            $query->where('status', $status);
        }
        
        $audits = $query->orderBy('audit_date', 'desc')->paginate($perPage);
        
        return view('admin.audits.index', [
            'title' => $type === 'follow-up' ? 'Audit Perlu Tindak Lanjut' : 
                     ($type === 'overdue' ? 'Audit Melewati Batas Waktu' : 'Laporan Audit'),
            'audits' => $audits,
            'status' => $status,
            'type' => $type,
            'statuses' => AuditReport::STATUSES,
        ]);
    }

    /**
     * Display audit report detail
     */
    public function show(Request $request, AuditReport $audit)
    {
        $tab = $request->get('tab', 'detail');
        $allowedTabs = ['detail', 'findings', 'timeline'];
        
        $audit->load(['asset.category', 'asset.opdUnit', 'auditor']);
        
        return view('admin.audits.show', [
            'title' => 'Detail Laporan Audit',
            'audit' => $audit,
            'tab' => $tab,
            'allowedTabs' => $allowedTabs,
        ]);
    }

    /**
     * Store audit report
     */
    public function store(Request $request)
    {
        $request->validate(AuditReport::rules());
        
        try {
            $data = $request->all();
            $data['auditor_id'] = auth()->id();
            
            if ($request->hasFile('report_file')) {
                $file = $request->file('report_file');
                $path = $file->store('audit-reports', 'public');
                $data['report_file_path'] = $path;
            }
            
            $audit = AuditReport::create($data);
            
            return redirect()
                ->route('admin.audits.show', $audit)
                ->with('success', 'Laporan audit berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Gagal membuat laporan audit: ' . $e->getMessage());
        }
    }

    /**
     * Update audit status
     */
    public function updateStatus(Request $request, AuditReport $audit)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', AuditReport::STATUSES),
            'follow_up' => 'nullable|string|max:1000',
            'follow_up_deadline' => 'nullable|date|after_or_equal:' . $audit->audit_date,
        ]);
        
        try {
            $data = [
                'status' => $request->status,
            ];
            
            if ($request->has('follow_up')) {
                $data['follow_up'] = $request->follow_up;
            }
            
            if ($request->has('follow_up_deadline')) {
                $data['follow_up_deadline'] = $request->follow_up_deadline;
            }
            
            $audit->update($data);
            
            $statusDisplay = $audit->status_display;
            
            return redirect()
                ->route('admin.audits.show', $audit)
                ->with('success', "Status audit berhasil diubah menjadi: {$statusDisplay}");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal mengubah status: ' . $e->getMessage());
        }
    }

    /**
     * Download audit file
     */
    public function downloadFile(AuditReport $audit)
    {
        if (!$audit->report_file_path || !Storage::disk('public')->exists($audit->report_file_path)) {
            return redirect()
                ->back()
                ->with('error', 'File laporan tidak ditemukan');
        }
        
        return Storage::disk('public')->download($audit->report_file_path);
    }
}