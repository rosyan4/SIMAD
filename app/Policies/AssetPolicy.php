<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Asset;
use Illuminate\Auth\Access\Response;

class AssetPolicy
{
    /**
     * Determine if the user can view the asset.
     */
    public function view(User $user, Asset $asset): bool
    {
        // Admin utama can view all assets
        if ($user->isAdminUtama()) {
            return true;
        }

        // Admin OPD can only view assets from their OPD
        return $user->isAdminOPD() && $asset->opd_unit_id === $user->opd_unit_id;
    }

    /**
     * Determine if the user can update the asset.
     */
    public function update(User $user, Asset $asset): bool
    {
        // Admin utama can update all assets
        if ($user->isAdminUtama()) {
            return true;
        }

        // Admin OPD can only update assets from their OPD
        // and only if asset is not yet verified/validated
        return $user->isAdminOPD() && 
               $asset->opd_unit_id === $user->opd_unit_id &&
               $asset->document_verification_status === 'belum_diverifikasi' &&
               $asset->validation_status === 'belum_divalidasi';
    }

    /**
     * Determine if the user can delete the asset.
     */
    public function delete(User $user, Asset $asset): bool
    {
        // Admin utama can delete all assets
        if ($user->isAdminUtama()) {
            return true;
        }

        // Admin OPD can only delete assets from their OPD
        // and only if asset is not yet verified/validated
        return $user->isAdminOPD() && 
               $asset->opd_unit_id === $user->opd_unit_id &&
               $asset->document_verification_status === 'belum_diverifikasi' &&
               $asset->validation_status === 'belum_divalidasi';
    }
}