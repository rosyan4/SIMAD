<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\OpdUnit;
use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportService
{
    /**
     * Generate asset summary report
     */
    public function generateAssetSummaryReport(?int $opdUnitId = null, ?string $year = null): array
    {
        $year = $year ?? date('Y');
        
        $query = Asset::query()
            ->whereYear('created_at', $year)
            ->with(['category', 'opdUnit', 'location']);

        if ($opdUnitId) {
            $query->where('opd_unit_id', $opdUnitId);
        }

        $assets = $query->get();

        $summary = [
            'year' => $year,
            'total_assets' => $assets->count(),
            'total_value' => $assets->sum('value'),
            'average_value' => $assets->avg('value') ?? 0,
            'opd_unit' => $opdUnitId ? OpdUnit::find($opdUnitId)->nama_opd : 'Semua OPD',
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];

        // Group by category
        $categorySummary = [];
        foreach ($assets->groupBy('category_id') as $categoryId => $categoryAssets) {
            $category = Category::find($categoryId);
            if ($category) {
                $categorySummary[] = [
                    'category' => $category->name,
                    'kib_code' => $category->kib_code,
                    'asset_count' => $categoryAssets->count(),
                    'total_value' => $categoryAssets->sum('value'),
                    'percentage' => $summary['total_value'] > 0 
                        ? round(($categoryAssets->sum('value') / $summary['total_value']) * 100, 2) 
                        : 0,
                ];
            }
        }

        // Group by condition
        $conditionSummary = [];
        foreach ($assets->groupBy('condition') as $condition => $conditionAssets) {
            $conditionSummary[] = [
                'condition' => $condition,
                'asset_count' => $conditionAssets->count(),
                'total_value' => $conditionAssets->sum('value'),
            ];
        }

        // Monthly acquisition
        $monthlySummary = [];
        for ($month = 1; $month <= 12; $month++) {
            $monthAssets = $assets->filter(function ($asset) use ($year, $month) {
                return Carbon::parse($asset->created_at)->year == $year && 
                       Carbon::parse($asset->created_at)->month == $month;
            });

            $monthlySummary[] = [
                'month' => $month,
                'month_name' => Carbon::createFromDate($year, $month, 1)->locale('id')->monthName,
                'asset_count' => $monthAssets->count(),
                'total_value' => $monthAssets->sum('value'),
            ];
        }

        return [
            'summary' => $summary,
            'category_summary' => $categorySummary,
            'condition_summary' => $conditionSummary,
            'monthly_summary' => $monthlySummary,
            'assets' => $assets->take(100)->values(), // Limit to 100 for performance
        ];
    }

    /**
     * Generate mutation report
     */
    public function generateMutationReport(?string $startDate = null, ?string $endDate = null): array
    {
        $startDate = $startDate ?? now()->subMonth()->format('Y-m-d');
        $endDate = $endDate ?? now()->format('Y-m-d');

        $mutations = DB::table('asset_mutations')
            ->join('assets', 'asset_mutations.asset_id', '=', 'assets.asset_id')
            ->join('opd_units as from_opd', 'asset_mutations.from_opd_unit_id', '=', 'from_opd.opd_unit_id')
            ->join('opd_units as to_opd', 'asset_mutations.to_opd_unit_id', '=', 'to_opd.opd_unit_id')
            ->select([
                'asset_mutations.*',
                'assets.name as asset_name',
                'assets.asset_code',
                'assets.value',
                'from_opd.nama_opd as from_opd_name',
                'to_opd.nama_opd as to_opd_name',
            ])
            ->whereBetween('asset_mutations.mutation_date', [$startDate, $endDate])
            ->orderBy('asset_mutations.mutation_date', 'desc')
            ->get();

        $summary = [
            'period' => "{$startDate} sampai {$endDate}",
            'total_mutations' => $mutations->count(),
            'completed_mutations' => $mutations->where('status', 'selesai')->count(),
            'pending_mutations' => $mutations->where('status', 'diusulkan')->count(),
            'total_asset_value' => $mutations->where('status', 'selesai')->sum('value'),
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];

        return [
            'summary' => $summary,
            'mutations' => $mutations,
        ];
    }

    /**
     * Generate deletion report
     */
    public function generateDeletionReport(?string $startDate = null, ?string $endDate = null): array
    {
        $startDate = $startDate ?? now()->subYear()->format('Y-m-d');
        $endDate = $endDate ?? now()->format('Y-m-d');

        $deletions = DB::table('asset_deletions')
            ->join('assets', 'asset_deletions.asset_id', '=', 'assets.asset_id')
            ->join('opd_units', 'assets.opd_unit_id', '=', 'opd_units.opd_unit_id')
            ->select([
                'asset_deletions.*',
                'assets.name as asset_name',
                'assets.asset_code',
                'assets.value',
                'opd_units.nama_opd as opd_name',
            ])
            ->whereBetween('asset_deletions.proposed_at', [$startDate, $endDate])
            ->orderBy('asset_deletions.proposed_at', 'desc')
            ->get();

        $summary = [
            'period' => "{$startDate} sampai {$endDate}",
            'total_deletions' => $deletions->count(),
            'completed_deletions' => $deletions->where('status', 'selesai')->count(),
            'total_asset_value' => $deletions->where('status', 'selesai')->sum('value'),
            'total_sale_value' => $deletions->where('deletion_method', 'jual')->sum('sale_value'),
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];

        // Group by reason
        $reasonSummary = [];
        foreach ($deletions->groupBy('deletion_reason') as $reason => $reasonDeletions) {
            $reasonSummary[] = [
                'reason' => $this->getDeletionReasonDisplay($reason),
                'count' => $reasonDeletions->count(),
                'total_value' => $reasonDeletions->sum('value'),
            ];
        }

        return [
            'summary' => $summary,
            'reason_summary' => $reasonSummary,
            'deletions' => $deletions,
        ];
    }

    /**
     * Get deletion reason display name
     */
    private function getDeletionReasonDisplay(string $reason): string
    {
        $reasons = [
            'rusak_berat' => 'Rusak Berat',
            'hilang' => 'Hilang',
            'jual' => 'Dijual',
            'hibah' => 'Dihibahkan',
            'musnah' => 'Musnah',
            'lainnya' => 'Lainnya',
        ];

        return $reasons[$reason] ?? $reason;
    }

    /**
     * Generate audit report
     */
    public function generateAuditReport(?string $year = null): array
    {
        $year = $year ?? date('Y');

        $audits = DB::table('audit_reports')
            ->join('assets', 'audit_reports.asset_id', '=', 'assets.asset_id')
            ->join('opd_units', 'assets.opd_unit_id', '=', 'opd_units.opd_unit_id')
            ->join('users', 'audit_reports.auditor_id', '=', 'users.user_id')
            ->select([
                'audit_reports.*',
                'assets.name as asset_name',
                'assets.asset_code',
                'assets.value',
                'opd_units.nama_opd as opd_name',
                'users.name as auditor_name',
            ])
            ->whereYear('audit_reports.audit_date', $year)
            ->orderBy('audit_reports.audit_date', 'desc')
            ->get();

        $summary = [
            'year' => $year,
            'total_audits' => $audits->count(),
            'completed_audits' => $audits->where('status', 'completed')->count(),
            'follow_up_audits' => $audits->where('status', 'follow_up')->count(),
            'total_findings' => $audits->count(),
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];

        return [
            'summary' => $summary,
            'audits' => $audits,
        ];
    }
}