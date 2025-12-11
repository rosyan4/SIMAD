<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\OpdUnit;
use App\Models\AssetDeletion;
use App\Models\AssetMutation;
use App\Models\Maintenance;
use App\Models\AuditReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    private AssetService $assetService;
    private AssetDeletionService $deletionService;
    private AssetMutationService $mutationService;

    public function __construct(
        AssetService $assetService,
        AssetDeletionService $deletionService,
        AssetMutationService $mutationService
    ) {
        $this->assetService = $assetService;
        $this->deletionService = $deletionService;
        $this->mutationService = $mutationService;
    }

    /**
     * Get comprehensive dashboard data
     */
    public function getDashboardData(?int $opdUnitId = null): array
    {
        $cacheKey = $opdUnitId ? "dashboard_opd_{$opdUnitId}" : 'dashboard_global';
        
        return Cache::remember($cacheKey, 300, function () use ($opdUnitId) {
            return [
                'asset_statistics' => $this->assetService->getStatistics($opdUnitId),
                'deletion_statistics' => $this->deletionService->getStatistics($opdUnitId),
                'mutation_statistics' => $this->mutationService->getStatistics($opdUnitId),
                'maintenance_statistics' => $this->getMaintenanceStatistics($opdUnitId),
                'audit_statistics' => $this->getAuditStatistics($opdUnitId),
                'opd_units' => $this->getOpdUnitsSummary(),
                'recent_activities' => $this->getRecentActivities($opdUnitId),
                'asset_value_trend' => $this->getAssetValueTrend($opdUnitId),
            ];
        });
    }

    /**
     * Get maintenance statistics
     */
    private function getMaintenanceStatistics(?int $opdUnitId = null): array
    {
        $query = Maintenance::query();

        if ($opdUnitId) {
            $query->whereHas('asset', function ($q) use ($opdUnitId) {
                $q->where('opd_unit_id', $opdUnitId);
            });
        }

        $total = $query->count();
        $scheduled = $query->clone()->scheduled()->count();
        $inProgress = $query->clone()->inProgress()->count();
        $completed = $query->clone()->completed()->count();
        $overdue = $query->clone()->overdue()->count();
        $totalCost = $query->clone()->sum('cost');

        return [
            'total' => $total,
            'scheduled' => $scheduled,
            'in_progress' => $inProgress,
            'completed' => $completed,
            'overdue' => $overdue,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
            'total_cost' => $totalCost,
            'average_cost' => $completed > 0 ? round($totalCost / $completed, 2) : 0,
        ];
    }

    /**
     * Get audit statistics
     */
    private function getAuditStatistics(?int $opdUnitId = null): array
    {
        $query = AuditReport::query();

        if ($opdUnitId) {
            $query->whereHas('asset', function ($q) use ($opdUnitId) {
                $q->where('opd_unit_id', $opdUnitId);
            });
        }

        $total = $query->count();
        $draft = $query->clone()->where('status', 'draft')->count();
        $submitted = $query->clone()->where('status', 'submitted')->count();
        $followUp = $query->clone()->where('status', 'follow_up')->count();
        $completed = $query->clone()->where('status', 'completed')->count();
        $overdue = $query->clone()->where('status', 'follow_up')
            ->where('follow_up_deadline', '<', now()->toDateString())
            ->count();

        return [
            'total' => $total,
            'draft' => $draft,
            'submitted' => $submitted,
            'follow_up' => $followUp,
            'completed' => $completed,
            'overdue' => $overdue,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get OPD units summary
     */
    private function getOpdUnitsSummary(): array
    {
        return Cache::remember('opd_units_summary', 1800, function () {
            return OpdUnit::withCount(['assets'])
                ->withSum('assets', 'value')
                ->orderBy('kode_opd_numeric')
                ->get()
                ->map(function ($opd) {
                    return [
                        'id' => $opd->opd_unit_id,
                        'kode_opd' => $opd->kode_opd,
                        'nama_opd' => $opd->nama_opd,
                        'kepala_opd' => $opd->kepala_opd,
                        'asset_count' => $opd->assets_count,
                        'total_value' => (float) $opd->assets_sum_value,
                        'formatted_value' => 'Rp ' . number_format($opd->assets_sum_value, 0, ',', '.'),
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get recent activities
     */
    public function getRecentActivities(?int $opdUnitId = null): array
    {
        $query = DB::table('asset_histories')
            ->join('assets', 'asset_histories.asset_id', '=', 'assets.asset_id')
            ->join('users', 'asset_histories.change_by', '=', 'users.user_id')
            ->select([
                'asset_histories.*',
                'assets.name as asset_name',
                'assets.asset_code',
                'users.name as user_name',
            ])
            ->orderBy('asset_histories.change_date', 'desc')
            ->limit(10);

        if ($opdUnitId) {
            $query->where('assets.opd_unit_id', $opdUnitId);
        }

        return $query->get()->toArray();
    }

    /**
     * Get asset value trend (last 12 months)
     */
    private function getAssetValueTrend(?int $opdUnitId = null): array
    {
        $query = Asset::query()
            ->select(
                DB::raw('EXTRACT(YEAR FROM created_at)::int as year'),
                DB::raw('EXTRACT(MONTH FROM created_at)::int as month'),
                DB::raw('COUNT(*) as asset_count'),
                DB::raw('SUM(value) as total_value')
            )
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy(
                DB::raw('EXTRACT(YEAR FROM created_at)'),
                DB::raw('EXTRACT(MONTH FROM created_at)')
            )
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc');

        if ($opdUnitId) {
            $query->where('opd_unit_id', $opdUnitId);
        }

        $results = $query->get();

        $trend = [];
        foreach ($results as $result) {
            $trend[] = [
                'period' => "{$result->year}-" . str_pad($result->month, 2, '0', STR_PAD_LEFT),
                'asset_count' => $result->asset_count,
                'total_value' => (float) $result->total_value,
            ];
        }

        return $trend;
    }

    /**
     * Get asset distribution by category
     */
    public function getAssetDistributionByCategory(?int $opdUnitId = null): array
    {
        $query = DB::table('assets')
            ->join('categories', 'assets.category_id', '=', 'categories.category_id')
            ->select(
                'categories.kib_code',
                'categories.name as category_name',
                DB::raw('COUNT(*) as asset_count'),
                DB::raw('SUM(assets.value) as total_value')
            )
            ->groupBy('categories.kib_code', 'categories.name')
            ->orderBy('categories.kib_code');

        if ($opdUnitId) {
            $query->where('assets.opd_unit_id', $opdUnitId);
        }

        return $query->get()->toArray();
    }

    /**
     * Get asset condition summary
     */
    public function getAssetConditionSummary(?int $opdUnitId = null): array
    {
        $query = Asset::query()
            ->select(
                'condition',
                DB::raw('COUNT(*) as count'),
                DB::raw('SUM(value) as total_value')
            )
            ->groupBy('condition')
            ->orderBy('condition');

        if ($opdUnitId) {
            $query->where('opd_unit_id', $opdUnitId);
        }

        return $query->get()->map(function ($item) {
            return [
                'condition' => $item->condition,
                'count' => $item->count,
                'percentage' => 0, // Will be calculated
                'total_value' => (float) $item->total_value,
            ];
        })->toArray();
    }
}