<?php

namespace App\Http\Controllers\Opd;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\OpdUnit;
use App\Models\Asset;
use App\Models\Maintenance;
use App\Models\AssetMutation;
use App\Models\AssetDeletion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class OPDProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin.opd']);
    }

    /**
     * Display user profile dengan tab-based layout
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $user->load('opdUnit');
        $tab = $request->get('tab', 'profile');
        $data = [];

        switch ($tab) {
            case 'profile':
                // Data user sudah tersedia
                break;

            case 'activities':
                $data['activities'] = DB::table('asset_histories')
                    ->join('assets', 'asset_histories.asset_id', '=', 'assets.asset_id')
                    ->join('users', 'asset_histories.change_by', '=', 'users.user_id')
                    ->select([
                        'asset_histories.*',
                        'assets.name as asset_name',
                        'assets.asset_code',
                        'users.name as user_name',
                    ])
                    ->where('asset_histories.change_by', $user->user_id)
                    ->orderBy('asset_histories.change_date', 'desc')
                    ->paginate(15, ['*'], 'activities_page');
                break;

            case 'statistics':
                $data['userStats'] = $this->getUserStatistics($user);
                $data['opdStats'] = $this->getOpdStatistics($user->opd_unit_id);
                break;

            case 'opd':
                $data['opdUnit'] = $user->opdUnit;
                break;

            case 'notifications':
                // MODIFIKASI: Gunakan session atau default jika kolom tidak ada
                $data['notificationPreferences'] = [
                    'email' => true,
                    'push' => true,
                    'types' => ['important', 'maintenance', 'deletion', 'mutation']
                ];
                break;

            case 'security':
                // Tab untuk ganti password
                break;
        }

        return view('opd.profile.index', [
            'title' => 'Profil Saya - ' . ucfirst($tab),
            'user' => $user,
            'tab' => $tab,
            'data' => $data,
        ]);
    }

    /**
     * Update user profile (AJAX compatible)
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
        ]);

        try {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profil berhasil diperbarui',
                    'user' => $user->fresh(),
                ]);
            }

            return redirect()
                ->route('opd.profile', ['tab' => 'profile'])
                ->with('success', 'Profil berhasil diperbarui');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui profil: ' . $e->getMessage(),
                ], 400);
            }

            return redirect()
                ->route('opd.profile', ['tab' => 'profile'])
                ->withInput()
                ->with('error', 'Gagal memperbarui profil: ' . $e->getMessage());
        }
    }

    /**
     * Change password (AJAX compatible)
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password saat ini tidak sesuai',
                ], 400);
            }

            return redirect()
                ->route('opd.profile', ['tab' => 'security'])
                ->with('error', 'Password saat ini tidak sesuai');
        }

        try {
            $user->update([
                'password' => Hash::make($request->new_password),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password berhasil diubah',
                ]);
            }

            return redirect()
                ->route('opd.profile', ['tab' => 'security'])
                ->with('success', 'Password berhasil diubah');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal mengubah password: ' . $e->getMessage(),
                ], 400);
            }

            return redirect()
                ->route('opd.profile', ['tab' => 'security'])
                ->with('error', 'Gagal mengubah password: ' . $e->getMessage());
        }
    }

    /**
     * Update OPD unit profile (AJAX compatible)
     */
    public function updateOpdProfile(Request $request)
    {
        $opdUnit = auth()->user()->opdUnit;

        $request->validate([
            'nama_opd' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'kepala_opd' => 'nullable|string|max:255',
            'nip_kepala_opd' => 'nullable|string|max:20',
        ]);

        try {
            $opdUnit->update($request->all());

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Profil OPD berhasil diperbarui',
                    'opdUnit' => $opdUnit->fresh(),
                ]);
            }

            return redirect()
                ->route('opd.profile', ['tab' => 'opd'])
                ->with('success', 'Profil OPD berhasil diperbarui');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui profil OPD: ' . $e->getMessage(),
                ], 400);
            }

            return redirect()
                ->route('opd.profile', ['tab' => 'opd'])
                ->withInput()
                ->with('error', 'Gagal memperbarui profil OPD: ' . $e->getMessage());
        }
    }

    /**
     * Update notification settings (AJAX compatible) - MODIFIKASI
     */
    public function updateNotifications(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'notification_types' => 'nullable|array',
            'notification_types.*' => 'string|in:important,maintenance,deletion,mutation,verification,audit',
        ]);

        try {
            $preferences = [
                'email' => $request->boolean('email_notifications', true),
                'push' => $request->boolean('push_notifications', true),
                'types' => $request->notification_types ?? ['important', 'maintenance', 'deletion'],
            ];

            // MODIFIKASI: Simpan di session karena kolom tidak ada di database
            // Atau bisa buat migration untuk menambahkan kolom notification_preferences
            session(['user_notification_preferences' => $preferences]);
            
            // Atau jika ingin menggunakan database, tambahkan kolom notification_preferences JSON di tabel users
            // $user->update([
            //     'notification_preferences' => $preferences,
            // ]);

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Pengaturan notifikasi berhasil diperbarui',
                    'preferences' => $preferences,
                ]);
            }

            return redirect()
                ->route('opd.profile', ['tab' => 'notifications'])
                ->with('success', 'Pengaturan notifikasi berhasil diperbarui');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memperbarui pengaturan: ' . $e->getMessage(),
                ], 400);
            }

            return redirect()
                ->route('opd.profile', ['tab' => 'notifications'])
                ->withInput()
                ->with('error', 'Gagal memperbarui pengaturan: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Get user statistics
     */
    public function getUserStats()
    {
        $user = auth()->user();
        $stats = $this->getUserStatistics($user);

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * AJAX: Get OPD statistics
     */
    public function getOpdStats()
    {
        $opdUnitId = auth()->user()->opd_unit_id;
        $stats = $this->getOpdStatistics($opdUnitId);

        return response()->json([
            'success' => true,
            'stats' => $stats,
        ]);
    }

    /**
     * Get user statistics
     */
    private function getUserStatistics(User $user)
    {
        return [
            'total_assets_created' => $user->createdAssets()->count(),
            'total_maintenances_recorded' => Maintenance::where('recorded_by', $user->user_id)->count(),
            'total_mutations_proposed' => AssetMutation::where('mutated_by', $user->user_id)->count(),
            'total_deletions_proposed' => AssetDeletion::where('proposed_by', $user->user_id)->count(),
            'total_documents_uploaded' => DB::table('documents')
                ->where('uploaded_by', $user->user_id)
                ->count(),
            'last_login' => $user->last_login ? 
                $user->last_login->format('d-m-Y H:i') : 'Belum pernah',
            'account_created' => $user->created_at->format('d-m-Y'),
            'account_age' => now()->diffInDays($user->created_at) . ' hari',
        ];
    }

    /**
     * Get OPD statistics
     */
    private function getOpdStatistics($opdUnitId)
    {
        return [
            'total_assets' => Asset::where('opd_unit_id', $opdUnitId)->count(),
            'total_value' => Asset::where('opd_unit_id', $opdUnitId)->sum('value'),
            'verified_assets' => Asset::where('opd_unit_id', $opdUnitId)
                ->where('document_verification_status', 'valid')
                ->where('validation_status', 'disetujui')
                ->count(),
            'active_assets' => Asset::where('opd_unit_id', $opdUnitId)
                ->where('status', 'aktif')
                ->count(),
            'locations_count' => DB::table('locations')
                ->where('opd_unit_id', $opdUnitId)
                ->count(),
            'pending_actions' => DB::table('maintenances')
                ->join('assets', 'maintenances.asset_id', '=', 'assets.asset_id')
                ->where('assets.opd_unit_id', $opdUnitId)
                ->where('maintenances.status', 'dijadwalkan')
                ->count(),
        ];
    }
}