<?php

namespace App\Services;

use App\Models\AssetDeletion;
use App\Models\Asset;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AssetDeletionService
{
    /**
     * Create deletion proposal
     */
    public function createProposal(array $data, int $userId): AssetDeletion
    {
        return DB::transaction(function () use ($data, $userId) {
            $asset = Asset::findOrFail($data['asset_id']);
            
            // Validate asset can be deleted
            $this->validateAssetForDeletion($asset);
            
            $data['proposed_by'] = $userId;
            $data['status'] = AssetDeletion::STATUS_DIUSULKAN;
            $data['proposed_at'] = now();
            
            $deletion = AssetDeletion::create($data);
            
            // Update asset status
            $asset->update(['status' => 'dimutasi']);
            
            return $deletion;
        });
    }

    /**
     * Verify deletion proposal - DIKOREKSI
     */
    public function verifyProposal(int $deletionId, int $userId): AssetDeletion
    {
        return DB::transaction(function () use ($deletionId, $userId) {
            $deletion = AssetDeletion::findOrFail($deletionId);
            
            if (!$deletion->isPending()) {
                throw new \InvalidArgumentException('Proposal sudah diproses');
            }
            
            $deletion->update([
                'verified_by' => $userId,
                'verified_at' => now(),
                'status' => AssetDeletion::STATUS_DIVERIFIKASI, // GUNAKAN KONSTANTA
            ]);
            
            return $deletion;
        });
    }

    /**
     * Approve deletion proposal
     */
    public function approveProposal(int $deletionId, int $userId, ?array $documents = []): AssetDeletion
    {
        return DB::transaction(function () use ($deletionId, $userId, $documents) {
            $deletion = AssetDeletion::findOrFail($deletionId);
            
            if (!$deletion->isVerified()) { // UBAH: dari isPending() ke isVerified()
                throw new \InvalidArgumentException('Proposal belum diverifikasi');
            }
            
            $updateData = [
                'approved_by' => $userId,
                'approved_at' => now(),
                'status' => AssetDeletion::STATUS_DISETUJUI,
            ];
            
            if (!empty($documents)) {
                $updateData['approval_documents'] = $documents;
            }
            
            $deletion->update($updateData);
            
            return $deletion;
        });
    }

    /**
     * Complete deletion process
     */
    public function completeDeletion(int $deletionId, ?string $deletionMethod = null, ?array $details = []): AssetDeletion
    {
        return DB::transaction(function () use ($deletionId, $deletionMethod, $details) {
            $deletion = AssetDeletion::findOrFail($deletionId);
            
            if (!$deletion->isApproved()) {
                throw new \InvalidArgumentException('Proposal belum disetujui');
            }
            
            $updateData = [
                'status' => AssetDeletion::STATUS_SELESAI,
                'deleted_at' => now(),
            ];
            
            if ($deletionMethod) {
                $updateData['deletion_method'] = $deletionMethod;
            }
            
            if (!empty($details)) {
                if (isset($details['sale_value'])) {
                    $updateData['sale_value'] = $details['sale_value'];
                }
                
                if (isset($details['recipient'])) {
                    $updateData['recipient'] = $details['recipient'];
                }
                
                if (isset($details['notes'])) {
                    $updateData['notes'] = $details['notes'];
                }
            }
            
            $deletion->update($updateData);
            
            // Asset status already updated by model boot method
            return $deletion;
        });
    }

    /**
     * Reject deletion proposal
     */
    public function rejectProposal(int $deletionId, string $reason): AssetDeletion
    {
        return DB::transaction(function () use ($deletionId, $reason) {
            $deletion = AssetDeletion::findOrFail($deletionId);
            
            if (!$deletion->isPending()) {
                throw new \InvalidArgumentException('Proposal sudah diproses');
            }
            
            $deletion->update([
                'status' => AssetDeletion::STATUS_DITOLAK,
                'notes' => $deletion->notes ? $deletion->notes . "\nDitolak: {$reason}" : "Ditolak: {$reason}",
            ]);
            
            // Restore asset status
            $deletion->asset->update(['status' => 'aktif']);
            
            return $deletion;
        });
    }

    /**
     * Cancel deletion proposal
     */
    public function cancelProposal(int $deletionId, string $reason): AssetDeletion
    {
        return DB::transaction(function () use ($deletionId, $reason) {
            $deletion = AssetDeletion::findOrFail($deletionId);
            
            $deletion->update([
                'status' => AssetDeletion::STATUS_DIBATALKAN,
                'notes' => $deletion->notes ? $deletion->notes . "\nDibatalkan: {$reason}" : "Dibatalkan: {$reason}",
            ]);
            
            // Restore asset status
            $deletion->asset->update(['status' => 'aktif']);
            
            return $deletion;
        });
    }

    /**
     * Validate asset can be deleted
     */
    private function validateAssetForDeletion(Asset $asset): void
    {
        // Check if asset is already deleted
        if ($asset->status === 'dihapus') {
            throw new \InvalidArgumentException('Aset sudah dihapus sebelumnya');
        }

        // Check if there's pending deletion
        $pendingDeletion = AssetDeletion::where('asset_id', $asset->asset_id)
            ->whereIn('status', [
                AssetDeletion::STATUS_DIUSULKAN,
                AssetDeletion::STATUS_DIVERIFIKASI,
                AssetDeletion::STATUS_DISETUJUI
            ])
            ->exists();
        
        if ($pendingDeletion) {
            throw new \InvalidArgumentException('Sudah ada proposal penghapusan untuk aset ini');
        }

        // Check if asset has active mutations
        if ($asset->status === 'dimutasi') {
            throw new \InvalidArgumentException('Aset sedang dalam proses mutasi');
        }
    }

    /**
     * Get deletion statistics - DIKOREKSI
     */
    public function getStatistics(?int $opdUnitId = null): array
    {
        $query = AssetDeletion::query();
        
        if ($opdUnitId) {
            $query->whereHas('asset', function ($q) use ($opdUnitId) {
                $q->where('opd_unit_id', $opdUnitId);
            });
        }

        $total = $query->count();
        $pending = $query->clone()->pending()->count();
        $verified = $query->clone()->verified()->count();
        $approved = $query->clone()->approved()->count();
        $completed = $query->clone()->completed()->count();
        $rejected = $query->clone()->rejected()->count();
        $cancelled = $query->clone()->cancelled()->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'verified' => $verified,
            'approved' => $approved,
            'completed' => $completed,
            'rejected' => $rejected,
            'cancelled' => $cancelled,
            'approval_rate' => $total > 0 ? round(($completed / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get deletion timeline - DIKOREKSI
     */
    public function getTimeline(AssetDeletion $deletion): array
    {
        $timeline = [];
        
        if ($deletion->proposed_at) {
            $timeline[] = [
                'action' => 'Diusulkan',
                'by' => $deletion->proposer ? $deletion->proposer->name : 'Unknown',
                'date' => $deletion->proposed_at,
                'description' => 'Pengajuan penghapusan aset'
            ];
        }

        if ($deletion->verified_at) {
            $timeline[] = [
                'action' => 'Diverifikasi',
                'by' => $deletion->verifier ? $deletion->verifier->name : 'Unknown',
                'date' => $deletion->verified_at,
                'description' => 'Verifikasi dokumen penghapusan'
            ];
        }

        if ($deletion->approved_at) {
            $timeline[] = [
                'action' => 'Disetujui',
                'by' => $deletion->approver ? $deletion->approver->name : 'Unknown',
                'date' => $deletion->approved_at,
                'description' => 'Persetujuan akhir penghapusan'
            ];
        }

        if ($deletion->deleted_at) {
            $timeline[] = [
                'action' => 'Selesai',
                'by' => 'System',
                'date' => $deletion->deleted_at,
                'description' => 'Penghapusan aset selesai diproses'
            ];
        }

        return $timeline;
    }
}