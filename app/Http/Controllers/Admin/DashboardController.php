<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use App\Services\ReportService;
use App\Models\OpdUnit;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private DashboardService $dashboardService;
    private ReportService $reportService;

    public function __construct(
        DashboardService $dashboardService,
        ReportService $reportService
    ) {
        $this->middleware(['auth', 'admin.utama']);
        $this->dashboardService = $dashboardService;
        $this->reportService = $reportService;
    }

    /**
     * Display admin dashboard
     */
    public function index(Request $request)
    {
        $opdUnitId = $request->get('opd_unit_id');
        $period = $request->get('period', 'monthly'); // monthly, quarterly, yearly
        
        $dashboardData = $this->dashboardService->getDashboardData($opdUnitId);
        
        // Get OPD units for filter dropdown
        $opdUnits = OpdUnit::orderBy('kode_opd_numeric')->get();
        
        // Get recent activities
        $recentActivities = $this->dashboardService->getRecentActivities($opdUnitId);
        
        return view('admin.dashboard.index', [
            'title' => $opdUnitId 
                ? 'Dashboard ' . OpdUnit::find($opdUnitId)->nama_opd 
                : 'Dashboard Admin Utama',
            'dashboardData' => $dashboardData,
            'opdUnits' => $opdUnits,
            'selectedOpdUnitId' => $opdUnitId,
            'recentActivities' => $recentActivities,
            'period' => $period,
        ]);
    }

    /**
     * Display asset statistics
     */
    public function statistics(Request $request)
    {
        $type = $request->get('type', 'assets'); // assets, mutations, deletions, maintenance, audit
        $opdUnitId = $request->get('opd_unit_id');
        
        $statistics = [];
        $title = 'Statistik ';
        
        switch ($type) {
            case 'mutations':
                $statistics = app(\App\Services\AssetMutationService::class)->getStatistics($opdUnitId);
                $title .= 'Mutasi Aset';
                break;
                
            case 'deletions':
                $statistics = app(\App\Services\AssetDeletionService::class)->getStatistics($opdUnitId);
                $title .= 'Penghapusan Aset';
                break;
                
            case 'assets':
            default:
                $statistics = app(\App\Services\AssetService::class)->getStatistics($opdUnitId);
                $title .= 'Aset';
                break;
        }
        
        $opdUnits = OpdUnit::orderBy('kode_opd_numeric')->get();
        
        return view('admin.dashboard.statistics', [
            'title' => $title,
            'statistics' => $statistics,
            'type' => $type,
            'opdUnits' => $opdUnits,
            'selectedOpdUnitId' => $opdUnitId,
        ]);
    }

    /**
     * Display asset distribution by category
     */
    public function assetDistribution(Request $request)
    {
        $opdUnitId = $request->get('opd_unit_id');
        $distribution = $this->dashboardService->getAssetDistributionByCategory($opdUnitId);
        
        $opdUnits = OpdUnit::orderBy('kode_opd_numeric')->get();
        
        return view('admin.dashboard.asset-distribution', [
            'title' => 'Distribusi Aset per Kategori',
            'distribution' => $distribution,
            'opdUnits' => $opdUnits,
            'selectedOpdUnitId' => $opdUnitId,
        ]);
    }

    /**
     * Display asset condition summary
     */
    public function assetCondition(Request $request)
    {
        $opdUnitId = $request->get('opd_unit_id');
        $conditionSummary = $this->dashboardService->getAssetConditionSummary($opdUnitId);
        
        // Calculate percentages
        $total = array_sum(array_column($conditionSummary, 'count'));
        foreach ($conditionSummary as &$condition) {
            $condition['percentage'] = $total > 0 ? round(($condition['count'] / $total) * 100, 2) : 0;
        }
        
        $opdUnits = OpdUnit::orderBy('kode_opd_numeric')->get();
        
        return view('admin.dashboard.asset-condition', [
            'title' => 'Kondisi Aset',
            'conditionSummary' => $conditionSummary,
            'opdUnits' => $opdUnits,
            'selectedOpdUnitId' => $opdUnitId,
        ]);
    }
}