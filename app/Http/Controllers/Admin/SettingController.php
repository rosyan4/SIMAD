<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\CategoryService;
use App\Models\Category;
use App\Models\OpdUnit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    private CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->middleware(['auth', 'admin.utama']);
        $this->categoryService = $categoryService;
    }

    /**
     * Display settings dashboard
     */
    public function index()
    {
        $categoryStats = $this->categoryService->getCategoryStatistics();
        
        $sections = [
            ['id' => 'categories', 'name' => 'Kategori Aset', 'icon' => 'fas fa-tags', 'count' => Category::count()],
            ['id' => 'opd-units', 'name' => 'Unit OPD', 'icon' => 'fas fa-building', 'count' => OpdUnit::count()],
            ['id' => 'users', 'name' => 'Pengguna', 'icon' => 'fas fa-users', 'count' => User::count()],
            ['id' => 'system', 'name' => 'Sistem', 'icon' => 'fas fa-cogs', 'count' => 0],
        ];
        
        return view('admin.settings.index', [
            'title' => 'Pengaturan Sistem',
            'categoryStats' => $categoryStats,
            'sections' => $sections,
        ]);
    }

    /**
     * Unified CRUD management
     */
    public function manage(Request $request, $section, $action = 'index', $id = null)
    {
        $allowedSections = ['categories', 'opd-units', 'users', 'system'];
        
        if (!in_array($section, $allowedSections)) {
            abort(404);
        }
        
        $data = [];
        $title = '';
        
        switch ($section) {
            case 'categories':
                $title = $this->getTitle($section, $action, $id, Category::class);
                
                if ($action === 'index') {
                    $search = $request->get('search');
                    $query = Category::query();
                    
                    if ($search) {
                        $query->where(function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%{$search}%")
                              ->orWhere('kib_code', 'LIKE', "%{$search}%")
                              ->orWhere('standard_code_ref', 'LIKE', "%{$search}%");
                        });
                    }
                    
                    $data['items'] = $query->orderBy('kib_code')->paginate(20);
                    $data['search'] = $search;
                } elseif (in_array($action, ['create', 'edit'])) {
                    $data['kibCategories'] = $this->categoryService->getKibCategories();
                    if ($id && $action === 'edit') {
                        $data['item'] = Category::findOrFail($id);
                    }
                }
                break;
                
            case 'opd-units':
                $title = $this->getTitle($section, $action, $id, OpdUnit::class);
                
                if ($action === 'index') {
                    $search = $request->get('search');
                    $query = OpdUnit::query();
                    
                    if ($search) {
                        $query->where(function ($q) use ($search) {
                            $q->where('nama_opd', 'LIKE', "%{$search}%")
                              ->orWhere('kode_opd', 'LIKE', "%{$search}%")
                              ->orWhere('kepala_opd', 'LIKE', "%{$search}%");
                        });
                    }
                    
                    $data['items'] = $query->orderBy('kode_opd_numeric')->paginate(20);
                    $data['search'] = $search;
                } elseif (in_array($action, ['create', 'edit'])) {
                    if ($id && $action === 'edit') {
                        $data['item'] = OpdUnit::findOrFail($id);
                    }
                }
                break;
                
            case 'users':
                $title = $this->getTitle($section, $action, $id, User::class);
                
                if ($action === 'index') {
                    $role = $request->get('role', 'admin_opd');
                    $search = $request->get('search');
                    
                    $query = User::with('opdUnit')->where('role', $role);
                    
                    if ($search) {
                        $query->where(function ($q) use ($search) {
                            $q->where('name', 'LIKE', "%{$search}%")
                              ->orWhere('email', 'LIKE', "%{$search}%")
                              ->orWhereHas('opdUnit', function ($q) use ($search) {
                                  $q->where('nama_opd', 'LIKE', "%{$search}%")
                                    ->orWhere('kode_opd', 'LIKE', "%{$search}%");
                              });
                        });
                    }
                    
                    $data['items'] = $query->orderBy('name')->paginate(20);
                    $data['role'] = $role;
                    $data['search'] = $search;
                    $data['opdUnits'] = OpdUnit::orderBy('kode_opd_numeric')->get();
                } elseif (in_array($action, ['create', 'edit'])) {
                    $data['opdUnits'] = OpdUnit::orderBy('kode_opd_numeric')->get();
                    if ($id && $action === 'edit') {
                        $data['item'] = User::with('opdUnit')->findOrFail($id);
                    }
                }
                break;
                
            case 'system':
                $title = 'Pengaturan Sistem';
                
                if ($action === 'logs') {
                    $logFile = storage_path('logs/laravel.log');
                    $logs = [];
                    
                    if (file_exists($logFile)) {
                        $logs = array_slice(file($logFile, FILE_IGNORE_NEW_LINES), -100);
                    }
                    
                    $data['logs'] = array_reverse($logs);
                }
                break;
        }
        
        return view('admin.settings.crud', [
            'title' => $title,
            'section' => $section,
            'action' => $action,
            'id' => $id,
            'data' => $data,
        ]);
    }

    /**
     * Store or update item
     */
    public function storeOrUpdate(Request $request, $section, $id = null)
    {
        $rules = [];
        $successMessage = '';
        
        switch ($section) {
            case 'categories':
                $rules = Category::rules($id);
                $successMessage = 'Kategori berhasil ' . ($id ? 'diperbarui' : 'ditambahkan');
                
                $request->validate($rules);
                $this->categoryService->saveCategory($request->all(), $id);
                break;
                
            case 'opd-units':
                $rules = OpdUnit::rules($id);
                $successMessage = 'OPD berhasil ' . ($id ? 'diperbarui' : 'ditambahkan');
                
                $request->validate($rules);
                
                if ($id) {
                    $item = OpdUnit::findOrFail($id);
                    $item->update($request->all());
                } else {
                    OpdUnit::create($request->all());
                }
                break;
                
            case 'users':
                $rules = [
                    'name' => 'required|string|max:255',
                    'email' => 'required|string|email|max:255|unique:users,email' . ($id ? ",{$id},user_id" : ''),
                    'password' => $id ? 'nullable|string|min:8' : 'required|string|min:8',
                    'role' => 'required|in:admin_utama,admin_opd',
                    'opd_unit_id' => 'nullable|exists:opd_units,opd_unit_id',
                ];
                
                $successMessage = 'Pengguna berhasil ' . ($id ? 'diperbarui' : 'ditambahkan');
                
                $request->validate($rules);
                
                $userData = [
                    'name' => $request->name,
                    'email' => $request->email,
                    'role' => $request->role,
                    'opd_unit_id' => $request->opd_unit_id,
                ];
                
                if ($request->filled('password')) {
                    $userData['password'] = Hash::make($request->password);
                }
                
                if ($id) {
                    $user = User::findOrFail($id);
                    $user->update($userData);
                } else {
                    $userData['email_verified_at'] = now();
                    User::create($userData);
                }
                break;
        }
        
        return redirect()
            ->route('admin.settings.manage', ['section' => $section, 'action' => 'index'])
            ->with('success', $successMessage);
    }

    /**
     * Delete item
     */
    public function destroy($section, $id)
    {
        try {
            switch ($section) {
                case 'categories':
                    $item = Category::findOrFail($id);
                    
                    // Check if category has assets
                    if ($item->assets()->count() > 0) {
                        return redirect()
                            ->back()
                            ->with('error', 'Tidak dapat menghapus kategori yang memiliki aset');
                    }
                    
                    $item->delete();
                    break;
                    
                case 'opd-units':
                    $item = OpdUnit::findOrFail($id);
                    
                    // Check if OPD has assets or users
                    if ($item->assets()->count() > 0 || $item->users()->count() > 0) {
                        return redirect()
                            ->back()
                            ->with('error', 'Tidak dapat menghapus OPD yang memiliki aset atau pengguna');
                    }
                    
                    $item->delete();
                    break;
                    
                case 'users':
                    $item = User::findOrFail($id);
                    
                    // Prevent deleting yourself
                    if ($item->user_id === auth()->id()) {
                        return redirect()
                            ->back()
                            ->with('error', 'Tidak dapat menghapus akun sendiri');
                    }
                    
                    $item->delete();
                    break;
            }
            
            return redirect()
                ->route('admin.settings.manage', ['section' => $section, 'action' => 'index'])
                ->with('success', ucfirst(str_replace('-', ' ', $section)) . ' berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    /**
     * System backup
     */
    public function backup()
    {
        try {
            // Implementation depends on your backup solution
            \Artisan::call('backup:run');
            
            $output = \Artisan::output();
            
            return redirect()
                ->route('admin.settings.manage', ['section' => 'system', 'action' => 'index'])
                ->with('success', 'Backup berhasil dibuat: ' . $output);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Gagal membuat backup: ' . $e->getMessage());
        }
    }

    /**
     * Helper to get title
     */
    private function getTitle($section, $action, $id, $modelClass)
    {
        $sectionName = match($section) {
            'categories' => 'Kategori',
            'opd-units' => 'OPD',
            'users' => 'Pengguna',
            default => 'Item'
        };
        
        if ($action === 'index') {
            return "Manajemen {$sectionName}";
        } elseif ($action === 'create') {
            return "Tambah {$sectionName}";
        } elseif ($action === 'edit' && $id) {
            $item = $modelClass::find($id);
            $itemName = $item ? ($item->name ?? $item->nama_opd ?? $item->email) : '';
            return "Edit {$sectionName}: {$itemName}";
        }
        
        return "{$sectionName}";
    }
}