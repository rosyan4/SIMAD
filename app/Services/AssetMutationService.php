<?php

namespace App\Services;

use App\Models\AssetMutation;
use App\Models\Asset;
use Illuminate\Support\Facades\DB;

class AssetMutationService
{
    /**
     * Create mutation proposal
     */
    public function createProposal(array $data, int $userId): AssetMutation
    {
        return DB::transaction(function () use ($data, $userId) {
            $asset = Asset::findOrFail($data['asset_id']);
            
            // Validate asset can be mutated
            $this->validateAssetForMutation($asset);

            $data['mutated_by'] = $userId;
            $data['status'] = 'diusulkan';
            $data['mutation_date'] = $data['mutation_date'] ?? now();

            $mutation = AssetMutation::create($data);

            // Update asset status
            $asset->update(['status' => 'dimutasi']);

            return $mutation;
        });
    }

    /**
     * Approve mutation
     */
    public function approveMutation(int $mutationId): AssetMutation
    {
        return DB::transaction(function () use ($mutationId) {
            $mutation = AssetMutation::findOrFail($mutationId);
            
            if ($mutation->status !== 'diusulkan') {
                throw new \InvalidArgumentException('Mutasi sudah diproses');
            }

            $mutation->update(['status' => 'disetujui']);

            return $mutation;
        });
    }

    /**
     * Complete mutation
     */
    public function completeMutation(int $mutationId): AssetMutation
    {
        return DB::transaction(function () use ($mutationId) {
            $mutation = AssetMutation::findOrFail($mutationId);
            $asset = $mutation->asset;
            
            if ($mutation->status !== 'disetujui') {
                throw new \InvalidArgumentException('Mutasi belum disetujui');
            }

            // Update asset data
            $asset->update([
                'opd_unit_id' => $mutation->to_opd_unit_id,
                'location_id' => $mutation->to_location_id,
                'status' => 'aktif',
            ]);

            $mutation->update(['status' => 'selesai']);

            return $mutation;
        });
    }

    /**
     * Reject mutation
     */
    public function rejectMutation(int $mutationId, string $reason): AssetMutation
    {
        return DB::transaction(function () use ($mutationId, $reason) {
            $mutation = AssetMutation::findOrFail($mutationId);
            $asset = $mutation->asset;
            
            if ($mutation->status !== 'diusulkan') {
                throw new \InvalidArgumentException('Mutasi sudah diproses');
            }

            $mutation->update([
                'status' => 'ditolak',
                'notes' => $mutation->notes ? $mutation->notes . "\nDitolak: {$reason}" : "Ditolak: {$reason}",
            ]);

            // Restore asset status
            $asset->update(['status' => 'aktif']);

            return $mutation;
        });
    }

    /**
     * Validate asset can be mutated
     */
    private function validateAssetForMutation(Asset $asset): void
    {
        // Check if asset is active
        if ($asset->status !== 'aktif') {
            throw new \InvalidArgumentException('Aset harus dalam status aktif untuk dimutasi');
        }

        // Check if there's pending mutation
        $pendingMutation = AssetMutation::where('asset_id', $asset->asset_id)
            ->whereIn('status', ['diusulkan', 'disetujui'])
            ->exists();

        if ($pendingMutation) {
            throw new \InvalidArgumentException('Sudah ada proposal mutasi untuk aset ini');
        }

        // Check if asset has pending deletion
        if ($asset->deletions()->whereIn('status', ['diusulkan', 'disetujui'])->exists()) {
            throw new \InvalidArgumentException('Aset sedang dalam proses penghapusan');
        }
    }

    /**
     * Get mutation statistics
     */
    public function getStatistics(?int $opdUnitId = null): array
    {
        $query = AssetMutation::query();

        if ($opdUnitId) {
            $query->where(function ($q) use ($opdUnitId) {
                $q->where('from_opd_unit_id', $opdUnitId)
                  ->orWhere('to_opd_unit_id', $opdUnitId);
            });
        }

        $total = $query->count();
        $pending = $query->clone()->where('status', 'diusulkan')->count();
        $approved = $query->clone()->where('status', 'disetujui')->count();
        $completed = $query->clone()->where('status', 'selesai')->count();

        // Monthly statistics
        $monthly = $query->clone()
            ->select(
                DB::raw('EXTRACT(YEAR FROM mutation_date)::int as year'),
                DB::raw('EXTRACT(MONTH FROM mutation_date)::int as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('status', 'selesai')
            ->groupBy(
                DB::raw('EXTRACT(YEAR FROM mutation_date)'),
                DB::raw('EXTRACT(MONTH FROM mutation_date)')
            )
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();

        return [
            'total' => $total,
            'pending' => $pending,
            'approved' => $approved,
            'completed' => $completed,
            'completion_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
            'monthly_statistics' => $monthly,
        ];
    }
}