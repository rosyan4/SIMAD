@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0"><i class="fas fa-cube me-2"></i>{{ $title }}</h2>
            <div class="d-flex gap-2">
                @if($type == 'pending-verification' || $type == 'pending-validation')
                    <a href="{{ route('admin.assets.bulk-actions', ['action' => str_replace('pending-', '', $type)]) }}" 
                       class="btn btn-primary btn-sm">
                        <i class="fas fa-bolt me-1"></i> Aksi Massal
                    </a>
                @endif
                <a href="{{ route('admin.assets.export') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-download me-1"></i> Export
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Filter Form -->
<div class="card-custom mb-4">
    <div class="card-header">
        <i class="fas fa-filter me-2"></i> Filter Aset
    </div>
    <div class="card-body">
        <form method="GET" class="row g-3">
            <input type="hidden" name="type" value="{{ $type }}">
            
            <div class="col-md-3">
                <label class="form-label">OPD</label>
                <select name="opd_unit_id" class="form-select form-select-sm">
                    <option value="">Semua OPD</option>
                    @foreach($opdUnits as $opd)
                        <option value="{{ $opd->opd_unit_id }}" 
                            {{ ($filters['opd_unit_id'] ?? '') == $opd->opd_unit_id ? 'selected' : '' }}>
                            {{ $opd->kode_opd }} - {{ $opd->nama_opd }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach(['aktif', 'dimutasi', 'dihapus', 'dalam_perbaikan', 'nonaktif'] as $s)
                        <option value="{{ $s }}" 
                            {{ ($status ?? '') == $s ? 'selected' : '' }}>
                            {{ ucfirst($s) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Kategori</label>
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->category_id }}" 
                            {{ ($filters['category_id'] ?? '') == $category->category_id ? 'selected' : '' }}>
                            {{ $category->kib_code }} - {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Kondisi</label>
                <select name="condition" class="form-select form-select-sm">
                    <option value="">Semua Kondisi</option>
                    @foreach(['Baik', 'Rusak Ringan', 'Rusak Berat'] as $cond)
                        <option value="{{ $cond }}" 
                            {{ ($filters['condition'] ?? '') == $cond ? 'selected' : '' }}>
                            {{ $cond }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-6">
                <label class="form-label">Pencarian</label>
                <input type="text" name="search" class="form-control form-control-sm" 
                       placeholder="Cari nama aset/kode..." value="{{ $filters['search'] ?? '' }}">
            </div>
            
            <div class="col-md-6 d-flex align-items-end">
                <div class="d-flex gap-2 w-100">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                        <i class="fas fa-search me-1"></i> Terapkan Filter
                    </button>
                    <a href="{{ route('admin.assets.index', ['type' => $type]) }}" 
                       class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-redo me-1"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Assets Table -->
<div class="card-custom">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-table me-2"></i> Daftar Aset
            <span class="badge bg-primary ms-2">{{ $assets->total() }} data</span>
        </div>
        <div>
            <div class="btn-group btn-group-sm" role="group">
                <a href="{{ route('admin.assets.index', ['type' => 'all']) }}" 
                   class="btn btn-outline-primary {{ $type == 'all' ? 'active' : '' }}">
                    Semua
                </a>
                <a href="{{ route('admin.assets.index', ['type' => 'pending-verification']) }}" 
                   class="btn btn-outline-warning {{ $type == 'pending-verification' ? 'active' : '' }}">
                    Verifikasi
                </a>
                <a href="{{ route('admin.assets.index', ['type' => 'pending-validation']) }}" 
                   class="btn btn-outline-info {{ $type == 'pending-validation' ? 'active' : '' }}">
                    Validasi
                </a>
            </div>
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Kode Aset</th>
                        <th>Nama Aset</th>
                        <th>Kategori</th>
                        <th>OPD</th>
                        <th>Nilai</th>
                        <th>Status</th>
                        <th>Verifikasi</th>
                        <th>Validasi</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assets as $index => $asset)
                        <tr>
                            <td>{{ $assets->firstItem() + $index }}</td>
                            <td>
                                <strong>{{ $asset->asset_code }}</strong>
                                @if($asset->asset_code_old)
                                    <br><small class="text-muted">Lama: {{ $asset->asset_code_old }}</small>
                                @endif
                            </td>
                            <td>{{ $asset->name }}</td>
                            <td>
                                {{ $asset->category->kib_code ?? '-' }}
                                <br><small class="text-muted">{{ $asset->category->name ?? '-' }}</small>
                            </td>
                            <td>{{ $asset->opdUnit->kode_opd ?? '-' }}</td>
                            <td>{{ $asset->formatted_value }}</td>
                            <td>
                                @php
                                    $statusClass = match($asset->status) {
                                        'aktif' => 'success',
                                        'dimutasi' => 'warning',
                                        'dihapus' => 'danger',
                                        'dalam_perbaikan' => 'info',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($asset->status) }}</span>
                            </td>
                            <td>
                                @php
                                    $verifClass = match($asset->document_verification_status) {
                                        'valid' => 'success',
                                        'tidak_valid' => 'danger',
                                        default => 'warning'
                                    };
                                @endphp
                                <span class="badge bg-{{ $verifClass }}">
                                    {{ str_replace('_', ' ', $asset->document_verification_status) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $validClass = match($asset->validation_status) {
                                        'disetujui' => 'success',
                                        'revisi' => 'warning',
                                        'ditolak' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $validClass }}">
                                    {{ str_replace('_', ' ', $asset->validation_status) }}
                                </span>
                            </td>
                            <td class="table-actions">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.assets.show', $asset) }}" 
                                       class="btn btn-outline-primary" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($asset->document_verification_status == 'belum_diverifikasi')
                                        <button type="button" class="btn btn-outline-warning" 
                                                data-bs-toggle="modal" data-bs-target="#verifyModal{{ $asset->asset_id }}"
                                                title="Verifikasi">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    @endif
                                    @if($asset->validation_status == 'belum_divalidasi')
                                        <button type="button" class="btn btn-outline-info"
                                                data-bs-toggle="modal" data-bs-target="#validateModal{{ $asset->asset_id }}"
                                                title="Validasi">
                                            <i class="fas fa-clipboard-check"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Verification Modal -->
                        <div class="modal fade" id="verifyModal{{ $asset->asset_id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.assets.verify-document', $asset) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Verifikasi Dokumen Aset</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Status Verifikasi</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="valid">Valid</option>
                                                    <option value="tidak_valid">Tidak Valid</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Catatan</label>
                                                <textarea name="notes" class="form-control" rows="3" 
                                                          placeholder="Berikan catatan jika diperlukan..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Validation Modal -->
                        <div class="modal fade" id="validateModal{{ $asset->asset_id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="{{ route('admin.assets.validate', $asset) }}" method="POST">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title">Validasi Aset</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Status Validasi</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="disetujui">Disetujui</option>
                                                    <option value="revisi">Perlu Revisi</option>
                                                    <option value="ditolak">Ditolak</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Catatan</label>
                                                <textarea name="notes" class="form-control" rows="3" 
                                                          placeholder="Berikan catatan validasi..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-3"></i>
                                <p>Tidak ada data aset</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                Menampilkan {{ $assets->firstItem() }} sampai {{ $assets->lastItem() }} dari {{ $assets->total() }} data
            </div>
            <div>
                {{ $assets->links() }}
            </div>
        </div>
    </div>
</div>
@endsection