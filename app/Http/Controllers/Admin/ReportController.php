<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use App\Models\OpdUnit;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private ReportService $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->middleware(['auth', 'admin.utama']);
        $this->reportService = $reportService;
    }

    /**
     * Display all report types
     */
    public function index()
    {
        $reports = [
            ['type' => 'asset_summary', 'name' => 'Laporan Aset', 'icon' => 'fas fa-cubes'],
            ['type' => 'mutation', 'name' => 'Laporan Mutasi', 'icon' => 'fas fa-exchange-alt'],
            ['type' => 'deletion', 'name' => 'Laporan Penghapusan', 'icon' => 'fas fa-trash'],
            ['type' => 'audit', 'name' => 'Laporan Audit', 'icon' => 'fas fa-clipboard-check'],
        ];
        
        return view('admin.reports.index', [
            'title' => 'Laporan Sistem',
            'reports' => $reports,
        ]);
    }

    /**
     * Display report generation form
     */
    public function generate(Request $request, $type)
    {
        $allowedTypes = ['asset_summary', 'mutation', 'deletion', 'audit'];
        
        if (!in_array($type, $allowedTypes)) {
            abort(404);
        }
        
        $opdUnits = OpdUnit::orderBy('kode_opd_numeric')->get();
        
        $title = match($type) {
            'asset_summary' => 'Laporan Aset',
            'mutation' => 'Laporan Mutasi',
            'deletion' => 'Laporan Penghapusan',
            'audit' => 'Laporan Audit',
            default => 'Generate Laporan'
        };
        
        return view('admin.reports.generate', [
            'title' => $title,
            'type' => $type,
            'opdUnits' => $opdUnits,
        ]);
    }

    /**
     * Generate and display/download report
     */
    public function process(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:asset_summary,mutation,deletion,audit',
            'year' => 'required_if:report_type,asset_summary,audit|numeric|min:2020|max:' . date('Y'),
            'start_date' => 'required_if:report_type,mutation,deletion|date',
            'end_date' => 'required_if:report_type,mutation,deletion|date|after_or_equal:start_date',
            'opd_unit_id' => 'nullable|exists:opd_units,opd_unit_id',
            'format' => 'required|in:pdf,excel,view',
        ]);

        $reportType = $request->report_type;
        $format = $request->format;
        
        switch ($reportType) {
            case 'asset_summary':
                $data = $this->reportService->generateAssetSummaryReport(
                    $request->opd_unit_id,
                    $request->year
                );
                $view = 'admin.reports.show-asset-summary';
                $filename = 'Laporan_Aset_' . ($request->year ?? date('Y')) . '.pdf';
                break;
                
            case 'mutation':
                $data = $this->reportService->generateMutationReport(
                    $request->start_date,
                    $request->end_date
                );
                $view = 'admin.reports.show-mutation';
                $filename = 'Laporan_Mutasi_' . $request->start_date . '_' . $request->end_date . '.pdf';
                break;
                
            case 'deletion':
                $data = $this->reportService->generateDeletionReport(
                    $request->start_date,
                    $request->end_date
                );
                $view = 'admin.reports.show-deletion';
                $filename = 'Laporan_Penghapusan_' . $request->start_date . '_' . $request->end_date . '.pdf';
                break;
                
            case 'audit':
                $data = $this->reportService->generateAuditReport($request->year);
                $view = 'admin.reports.show-audit';
                $filename = 'Laporan_Audit_' . ($request->year ?? date('Y')) . '.pdf';
                break;
        }

        if ($format === 'view') {
            return view($view, [
                'title' => 'Laporan ' . ucfirst(str_replace('_', ' ', $reportType)),
                'reportData' => $data,
                'format' => $format,
            ]);
        }

        // For PDF generation (need to install dompdf)
        if ($format === 'pdf') {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, [
                'reportData' => $data,
                'title' => 'Laporan ' . ucfirst(str_replace('_', ' ', $reportType)),
                'format' => 'pdf',
            ]);
            
            return $pdf->download($filename);
        }

        // For Excel export (need to install maatwebsite/excel)
        if ($format === 'excel') {
            // Implementation for Excel export
            return redirect()->back()->with('info', 'Export Excel akan segera tersedia');
        }

        return redirect()->back()->with('error', 'Format laporan tidak valid');
    }
}