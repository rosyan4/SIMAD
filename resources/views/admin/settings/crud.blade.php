@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-0">{{ $title }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Pengaturan</a></li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('admin.settings.manage', ['section' => $section, 'action' => 'index']) }}">
                                {{ ucfirst(str_replace('-', ' ', $section)) }}
                            </a>
                        </li>
                        @if($action != 'index')
                            <li class="breadcrumb-item active">{{ ucfirst($action) }}</li>
                        @endif
                    </ol>
                </nav>
            </div>
            <div>
                @if($action == 'index')
                    <a href="{{ route('admin.settings.manage', ['section' => $section, 'action' => 'create']) }}" 
                       class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i> Tambah Baru
                    </a>
                @else
                    <a href="{{ route('admin.settings.manage', ['section' => $section, 'action' => 'index']) }}" 
                       class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Kembali
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

@if($action == 'index')
    <!-- List View -->
    <div class="card-custom">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-table me-2"></i> Daftar {{ ucfirst(str_replace('-', ' ', $section)) }}
                <span class="badge bg-primary ms-2">{{ $data['items']->total() ?? 0 }} data</span>
            </div>
            @if(isset($data['search']) || isset($data['role']))
                <form method="GET" class="d-flex gap-2">
                    @if(isset($data['role']))
                        <select name="role" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="admin_opd" {{ $data['role'] == 'admin_opd' ? 'selected' : '' }}>Admin OPD</option>
                            <option value="admin_utama" {{ $data['role'] == 'admin_utama' ? 'selected' : '' }}>Admin Utama</option>
                        </select>
                    @endif
                    @if(isset($data['search']))
                        <input type="text" name="search" class="form-control form-control-sm" 
                               placeholder="Cari..." value="{{ $data['search'] }}">
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fas fa-search"></i>
                        </button>
                    @endif
                </form>
            @endif
        </div>
        
        <div class="card-body p-0">
            @if($section == 'categories')
                <!-- Categories Table -->
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Nama Kategori</th>
                                <th>Kode KIB</th>
                                <th>Kode Standar</th>
                                <th>Jumlah Sub Kategori</th>
                                <th>Jumlah Aset</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['items'] as $index => $item)
                                <tr>
                                    <td>{{ $data['items']->firstItem() + $index }}</td>
                                    <td>
                                        <strong>{{ $item->name }}</strong>
                                        @if($item->description)
                                            <br><small class="text-muted">{{ Str::limit($item->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-info">{{ $item->kib_code }}</span></td>
                                    <td><code>{{ $item->standard_code_ref }}</code></td>
                                    <td>{{ count($item->sub_categories ?? []) }}</td>
                                    <td>{{ $item->assets_count ?? $item->assets()->count() }}</td>
                                    <td class="table-actions">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.settings.manage', ['section' => $section, 'action' => 'edit', 'id' => $item->category_id]) }}" 
                                               class="btn btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if(($item->assets_count ?? 0) == 0)
                                                <form action="{{ route('admin.settings.destroy', ['section' => $section, 'id' => $item->category_id]) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Hapus kategori ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-outline-secondary" title="Tidak dapat dihapus" disabled>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-3"></i>
                                        <p>Tidak ada data kategori</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
            @elseif($section == 'opd-units')
                <!-- OPD Units Table -->
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Kode OPD</th>
                                <th>Nama OPD</th>
                                <th>Kepala OPD</th>
                                <th>Jumlah Aset</th>
                                <th>Total Nilai</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['items'] as $index => $item)
                                <tr>
                                    <td>{{ $data['items']->firstItem() + $index }}</td>
                                    <td>
                                        <strong>{{ $item->kode_opd }}</strong>
                                        <br><small class="text-muted">Numeric: {{ $item->kode_opd_numeric }}</small>
                                    </td>
                                    <td>{{ $item->nama_opd }}</td>
                                    <td>
                                        {{ $item->kepala_opd ?? '-' }}
                                        @if($item->nip_kepala_opd)
                                            <br><small class="text-muted">{{ $item->nip_kepala_opd }}</small>
                                        @endif
                                    </td>
                                    <td>{{ $item->assets_count ?? $item->assets()->count() }}</td>
                                    <td>
                                        @if(($item->assets_count ?? 0) > 0)
                                            Rp {{ number_format($item->total_asset_value ?? $item->assets()->sum('value'), 0, ',', '.') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="table-actions">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.settings.manage', ['section' => $section, 'action' => 'edit', 'id' => $item->opd_unit_id]) }}" 
                                               class="btn btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if(($item->assets_count ?? 0) == 0 && ($item->users_count ?? $item->users()->count()) == 0)
                                                <form action="{{ route('admin.settings.destroy', ['section' => $section, 'id' => $item->opd_unit_id]) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Hapus OPD ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-outline-secondary" title="Tidak dapat dihapus" disabled>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-3"></i>
                                        <p>Tidak ada data OPD</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
            @elseif($section == 'users')
                <!-- Users Table -->
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>OPD</th>
                                <th>Login Terakhir</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($data['items'] as $index => $item)
                                <tr>
                                    <td>{{ $data['items']->firstItem() + $index }}</td>
                                    <td>
                                        <strong>{{ $item->name }}</strong>
                                        <br><small class="text-muted">ID: {{ $item->user_id }}</small>
                                    </td>
                                    <td>{{ $item->email }}</td>
                                    <td>
                                        <span class="badge {{ $item->role == 'admin_utama' ? 'bg-primary' : 'bg-success' }}">
                                            {{ $item->display_role }}
                                        </span>
                                    </td>
                                    <td>{{ $item->opdUnit->nama_opd ?? '-' }}</td>
                                    <td>
                                        @if($item->last_login)
                                            {{ $item->last_login->diffForHumans() }}
                                            <br><small class="text-muted">{{ $item->last_login->translatedFormat('d/m/Y H:i') }}</small>
                                        @else
                                            <span class="text-muted">Belum login</span>
                                        @endif
                                    </td>
                                    <td class="table-actions">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.settings.manage', ['section' => $section, 'action' => 'edit', 'id' => $item->user_id]) }}" 
                                               class="btn btn-outline-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($item->user_id != auth()->id())
                                                <form action="{{ route('admin.settings.destroy', ['section' => $section, 'id' => $item->user_id]) }}" 
                                                      method="POST" class="d-inline"
                                                      onsubmit="return confirm('Hapus pengguna ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <button class="btn btn-outline-secondary" title="Tidak dapat dihapus" disabled>
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        <i class="fas fa-inbox fa-2x mb-3"></i>
                                        <p>Tidak ada data pengguna</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
            @elseif($section == 'system' && isset($data['logs']))
                <!-- System Logs -->
                <div class="card-body">
                    <div class="terminal-container">
                        <div class="terminal-header bg-dark text-white p-2">
                            <i class="fas fa-terminal me-2"></i> System Logs
                        </div>
                        <div class="terminal-body bg-black text-white p-3" style="height: 400px; overflow-y: auto; font-family: monospace;">
                            @foreach($data['logs'] as $log)
                                <div class="log-line">
                                    @if(str_contains($log, 'ERROR'))
                                        <span class="text-danger">{{ $log }}</span>
                                    @elseif(str_contains($log, 'WARNING'))
                                        <span class="text-warning">{{ $log }}</span>
                                    @elseif(str_contains($log, 'INFO'))
                                        <span class="text-info">{{ $log }}</span>
                                    @else
                                        <span class="text-white">{{ $log }}</span>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <form action="{{ route('admin.settings.backup') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-database me-1"></i> Backup Database
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
        
        @if($section != 'system')
            <div class="card-footer">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        Menampilkan {{ $data['items']->firstItem() }} sampai {{ $data['items']->lastItem() }} dari {{ $data['items']->total() }} data
                    </div>
                    <div>
                        {{ $data['items']->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>
    
@elseif(in_array($action, ['create', 'edit']))
    <!-- Create/Edit Form -->
    <div class="card-custom">
        <div class="card-header">
            <i class="fas fa-{{ $action == 'create' ? 'plus' : 'edit' }} me-2"></i> 
            {{ $action == 'create' ? 'Tambah' : 'Edit' }} {{ ucfirst(str_replace('-', ' ', $section)) }}
        </div>
        <div class="card-body">
            @if($section == 'categories')
                <!-- Category Form -->
                <form action="{{ route('admin.settings.store', ['section' => $section, 'id' => $id ?? null]) }}" method="POST">
                    @csrf
                    @if(isset($id)) @method('PUT') @endif
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Kategori</label>
                            <input type="text" name="name" class="form-control" 
                                   value="{{ old('name', $data['item']->name ?? '') }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode KIB</label>
                            <select name="kib_code" class="form-select" required>
                                <option value="">Pilih Kode KIB</option>
                                @foreach($data['kibCategories'] as $code => $label)
                                    <option value="{{ $code }}" 
                                        {{ old('kib_code', $data['item']->kib_code ?? '') == $code ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode Standar Referensi</label>
                            <input type="text" name="standard_code_ref" class="form-control" 
                                   value="{{ old('standard_code_ref', $data['item']->standard_code_ref ?? '') }}" required>
                            <small class="text-muted">Kode referensi standar (misal: PMK-123/2023)</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="1">{{ old('description', $data['item']->description ?? '') }}</textarea>
                        </div>
                        
                        <!-- Sub Categories -->
                        <div class="col-12 mb-3">
                            <label class="form-label">Sub Kategori</label>
                            <div id="subCategoriesContainer">
                                @php
                                    $subCategories = old('sub_categories', $data['item']->sub_categories ?? []);
                                    $counter = 0;
                                @endphp
                                
                                @if($subCategories && count($subCategories) > 0)
                                    @foreach($subCategories as $code => $name)
                                        <div class="row mb-2" id="subCatRow{{ $counter }}">
                                            <div class="col-md-2">
                                                <input type="text" name="sub_category_codes[]" class="form-control" 
                                                       placeholder="Kode" value="{{ $code }}" required>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" name="sub_category_names[]" class="form-control" 
                                                       placeholder="Nama Sub Kategori" value="{{ $name }}" required>
                                            </div>
                                            <div class="col-md-2">
                                                <button type="button" class="btn btn-danger btn-sm w-100" 
                                                        onclick="removeSubCategory({{ $counter }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @php $counter++; @endphp
                                    @endforeach
                                @else
                                    <div class="row mb-2" id="subCatRow0">
                                        <div class="col-md-2">
                                            <input type="text" name="sub_category_codes[]" class="form-control" 
                                                   placeholder="Kode" value="01" required>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" name="sub_category_names[]" class="form-control" 
                                                   placeholder="Nama Sub Kategori" required>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger btn-sm w-100" 
                                                    onclick="removeSubCategory(0)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="addSubCategory()">
                                <i class="fas fa-plus me-1"></i> Tambah Sub Kategori
                            </button>
                            <small class="text-muted d-block mt-1">Format: Kode (2 digit angka) - Nama Sub Kategori</small>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.settings.manage', ['section' => $section, 'action' => 'index']) }}" 
                           class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
                
            @elseif($section == 'opd-units')
                <!-- OPD Unit Form -->
                <form action="{{ route('admin.settings.store', ['section' => $section, 'id' => $id ?? null]) }}" method="POST">
                    @csrf
                    @if(isset($id)) @method('PUT') @endif
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode OPD</label>
                            <input type="text" name="kode_opd" class="form-control" 
                                   value="{{ old('kode_opd', $data['item']->kode_opd ?? '') }}" required>
                            <small class="text-muted">Format: DINAS-XXX</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kode OPD Numerik</label>
                            <input type="number" name="kode_opd_numeric" class="form-control" min="1" max="99"
                                   value="{{ old('kode_opd_numeric', $data['item']->kode_opd_numeric ?? '') }}" required>
                            <small class="text-muted">Angka 1-99 (2 digit)</small>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Nama OPD</label>
                            <input type="text" name="nama_opd" class="form-control" 
                                   value="{{ old('nama_opd', $data['item']->nama_opd ?? '') }}" required>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2">{{ old('alamat', $data['item']->alamat ?? '') }}</textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Kepala OPD</label>
                            <input type="text" name="kepala_opd" class="form-control" 
                                   value="{{ old('kepala_opd', $data['item']->kepala_opd ?? '') }}">
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIP Kepala OPD</label>
                            <input type="text" name="nip_kepala_opd" class="form-control" 
                                   value="{{ old('nip_kepala_opd', $data['item']->nip_kepala_opd ?? '') }}">
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.settings.manage', ['section' => $section, 'action' => 'index']) }}" 
                           class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
                
            @elseif($section == 'users')
                <!-- User Form -->
                <form action="{{ route('admin.settings.store', ['section' => $section, 'id' => $id ?? null]) }}" method="POST">
                    @csrf
                    @if(isset($id)) @method('PUT') @endif
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" 
                                   value="{{ old('name', $data['item']->name ?? '') }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" 
                                   value="{{ old('email', $data['item']->email ?? '') }}" required>
                        </div>
                        
                        @if($action == 'create')
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control" required>
                            </div>
                        @else
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Password (Kosongkan jika tidak diubah)</label>
                                <input type="password" name="password" class="form-control">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>
                        @endif
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="admin_opd" 
                                    {{ old('role', $data['item']->role ?? '') == 'admin_opd' ? 'selected' : '' }}>
                                    Admin OPD
                                </option>
                                <option value="admin_utama" 
                                    {{ old('role', $data['item']->role ?? '') == 'admin_utama' ? 'selected' : '' }}>
                                    Admin Utama
                                </option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">OPD Unit</label>
                            <select name="opd_unit_id" class="form-select">
                                <option value="">Pilih OPD (Opsional)</option>
                                @foreach($data['opdUnits'] as $opd)
                                    <option value="{{ $opd->opd_unit_id }}"
                                        {{ old('opd_unit_id', $data['item']->opd_unit_id ?? '') == $opd->opd_unit_id ? 'selected' : '' }}>
                                        {{ $opd->kode_opd }} - {{ $opd->nama_opd }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Wajib untuk Admin OPD</small>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.settings.manage', ['section' => $section, 'action' => 'index']) }}" 
                           class="btn btn-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endif

@push('styles')
<style>
    .terminal-container {
        border-radius: 5px;
        overflow: hidden;
    }
    
    .terminal-body {
        font-size: 12px;
        line-height: 1.4;
    }
    
    .log-line {
        padding: 2px 0;
        border-bottom: 1px solid #333;
    }
</style>
@endpush

@push('scripts')
<script>
    let subCategoryCounter = {{ $counter ?? 1 }};
    
    function addSubCategory() {
        const container = document.getElementById('subCategoriesContainer');
        const row = document.createElement('div');
        row.className = 'row mb-2';
        row.id = 'subCatRow' + subCategoryCounter;
        row.innerHTML = `
            <div class="col-md-2">
                <input type="text" name="sub_category_codes[]" class="form-control" 
                       placeholder="Kode" value="${String(subCategoryCounter + 1).padStart(2, '0')}" required>
            </div>
            <div class="col-md-8">
                <input type="text" name="sub_category_names[]" class="form-control" 
                       placeholder="Nama Sub Kategori" required>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-danger btn-sm w-100" 
                        onclick="removeSubCategory(${subCategoryCounter})">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
        subCategoryCounter++;
    }
    
    function removeSubCategory(id) {
        const row = document.getElementById('subCatRow' + id);
        if (row) {
            row.remove();
        }
    }
</script>
@endpush
@endsection