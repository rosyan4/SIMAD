@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <!-- Header dengan Filter dan Statistik -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="card-title mb-0">{{ $title }}</h4>
                            <p class="text-muted">Kelola aset milik OPD {{ auth()->user()->opdUnit->nama_opd ?? '' }}</p>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('opd.assets.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle"></i> Tambah Aset Baru
                            </a>
                            <div class="btn-group ms-2">
                                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                    <i class="fas fa-download"></i> Export
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="{{ route('opd.assets.export', ['format' => 'excel']) }}">Excel</a></li>
                                    <li><a class="dropdown-item" href="{{ route('opd.assets.export', ['format' => 'pdf']) }}">PDF</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistik Cepat -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs fw-bold text-primary text-uppercase mb-1">
                                        Total Aset</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800" id="totalAssets">0</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-boxes fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs fw-bold text-success text-uppercase mb-1">
                                        Nilai Total</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800" id="totalValue">Rp 0</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs fw-bold text-info text-uppercase mb-1">
                                        Aset Aktif</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800" id="activeAssets">0</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs fw-bold text-warning text-uppercase mb-1">
                                        Perlu Perhatian</div>
                                    <div class="h5 mb-0 fw-bold text-gray-800" id="needsAttention">0</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter dan View Options -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Filter & Pencarian</h6>
                        <div class="btn-group" role="group">
                            <a href="{{ route('opd.assets.index', array_merge(request()->query(), ['view' => 'list'])) }}" 
                               class="btn btn-outline-secondary {{ $viewMode == 'list' ? 'active' : '' }}">
                                <i class="fas fa-list"></i> List
                            </a>
                            <a href="{{ route('opd.assets.index', array_merge(request()->query(), ['view' => 'grid'])) }}" 
                               class="btn btn-outline-secondary {{ $viewMode == 'grid' ? 'active' : '' }}">
                                <i class="fas fa-th"></i> Grid
                            </a>
                            <a href="{{ route('opd.assets.index', array_merge(request()->query(), ['view' => 'map'])) }}" 
                               class="btn btn-outline-secondary {{ $viewMode == 'map' ? 'active' : '' }}">
                                <i class="fas fa-map"></i> Map
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('opd.assets.index') }}" method="GET" id="filterForm">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="search" class="form-label">Pencarian</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ $filters['search'] ?? '' }}" placeholder="Nama atau Kode Aset...">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Semua Status</option>
                                    <option value="aktif" {{ ($filters['status'] ?? '') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="dimutasi" {{ ($filters['status'] ?? '') == 'dimutasi' ? 'selected' : '' }}>Dimutasi</option>
                                    <option value="dihapus" {{ ($filters['status'] ?? '') == 'dihapus' ? 'selected' : '' }}>Dihapus</option>
                                    <option value="dalam_perbaikan" {{ ($filters['status'] ?? '') == 'dalam_perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                                    <option value="nonaktif" {{ ($filters['status'] ?? '') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="category_id" class="form-label">Kategori</label>
                                <select class="form-select" id="category_id" name="category_id">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->category_id }}" 
                                            {{ ($filters['category_id'] ?? '') == $category->category_id ? 'selected' : '' }}>
                                        {{ $category->kib_code }} - {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label for="condition" class="form-label">Kondisi</label>
                                <select class="form-select" id="condition" name="condition">
                                    <option value="">Semua Kondisi</option>
                                    <option value="Baik" {{ ($filters['condition'] ?? '') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="Rusak Ringan" {{ ($filters['condition'] ?? '') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                    <option value="Rusak Berat" {{ ($filters['condition'] ?? '') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="per_page" class="form-label">Item per Halaman</label>
                                <select class="form-select" id="per_page" name="per_page">
                                    <option value="10" {{ request('per_page', 20) == 10 ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                                    <option value="50" {{ request('per_page', 20) == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page', 20) == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter"></i> Terapkan Filter
                                </button>
                                <a href="{{ route('opd.assets.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-redo"></i> Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Konten Utama berdasarkan View Mode -->
    <div class="row">
        <div class="col-md-12">
            @if($viewMode == 'list')
                <!-- LIST VIEW -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="50">#</th>
                                        <th>Kode Aset</th>
                                        <th>Nama Aset</th>
                                        <th>Kategori</th>
                                        <th>Lokasi</th>
                                        <th>Nilai</th>
                                        <th>Kondisi</th>
                                        <th>Status</th>
                                        <th>Verifikasi</th>
                                        <th width="120">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($assets as $index => $asset)
                                    <tr>
                                        <td>{{ ($assets->currentPage() - 1) * $assets->perPage() + $loop->iteration }}</td>
                                        <td>
                                            <strong>{{ $asset->asset_code }}</strong>
                                            @if($asset->asset_code_old)
                                            <br><small class="text-muted">{{ $asset->asset_code_old }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $asset->name }}</strong><br>
                                            <small class="text-muted">Tahun: {{ $asset->acquisition_year }}</small>
                                        </td>
                                        <td>
                                            {{ $asset->category->kib_code ?? '-' }}<br>
                                            <small class="text-muted">{{ $asset->category->name ?? '-' }}</small>
                                        </td>
                                        <td>{{ $asset->location->name ?? '-' }}</td>
                                        <td>
                                            <strong class="text-success">Rp {{ number_format($asset->value, 0, ',', '.') }}</strong>
                                        </td>
                                        <td>
                                            @php
                                                $conditionColors = [
                                                    'Baik' => 'success',
                                                    'Rusak Ringan' => 'warning',
                                                    'Rusak Berat' => 'danger'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $conditionColors[$asset->condition] ?? 'secondary' }}">
                                                {{ $asset->condition }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'aktif' => 'success',
                                                    'dimutasi' => 'info',
                                                    'dihapus' => 'secondary',
                                                    'dalam_perbaikan' => 'warning',
                                                    'nonaktif' => 'danger'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$asset->status] ?? 'secondary' }}">
                                                {{ ucfirst(str_replace('_', ' ', $asset->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @php
                                                $verifStatus = $asset->document_verification_status;
                                                $valStatus = $asset->validation_status;
                                                $verifColor = $verifStatus == 'valid' ? 'success' : ($verifStatus == 'tidak_valid' ? 'danger' : 'warning');
                                                $valColor = $valStatus == 'disetujui' ? 'success' : ($valStatus == 'ditolak' ? 'danger' : ($valStatus == 'revisi' ? 'warning' : 'secondary'));
                                            @endphp
                                            <span class="badge bg-{{ $verifColor }} mb-1" title="Dokumen: {{ $verifStatus }}">
                                                <i class="fas fa-file"></i> {{ ucfirst(str_replace('_', ' ', $verifStatus)) }}
                                            </span>
                                            <br>
                                            <span class="badge bg-{{ $valColor }}" title="Validasi: {{ $valStatus }}">
                                                <i class="fas fa-check"></i> {{ ucfirst(str_replace('_', ' ', $valStatus)) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('opd.assets.show', $asset) }}" 
                                                   class="btn btn-sm btn-info" title="Detail">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @can('update', $asset)
                                                <a href="{{ route('opd.assets.edit', $asset) }}" 
                                                   class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @endcan
                                                @can('delete', $asset)
                                                <button type="button" class="btn btn-sm btn-danger delete-asset" 
                                                        data-id="{{ $asset->asset_id }}" data-name="{{ $asset->name }}" title="Hapus">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="fas fa-box-open fa-3x mb-3"></i><br>
                                                Tidak ada data aset ditemukan.
                                                @if(count(request()->query()) > 0)
                                                <br><a href="{{ route('opd.assets.index') }}">Tampilkan semua aset</a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($assets->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Menampilkan {{ $assets->firstItem() }} - {{ $assets->lastItem() }} dari {{ $assets->total() }} aset
                            </div>
                            <div>
                                {{ $assets->withQueryString()->links() }}
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
            @elseif($viewMode == 'grid')
                <!-- GRID VIEW -->
                <div class="row">
                    @forelse($assets as $asset)
                    <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-header bg-light py-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="fw-bold">{{ $asset->asset_code }}</small>
                                    <span class="badge bg-{{ $asset->status == 'aktif' ? 'success' : ($asset->status == 'dalam_perbaikan' ? 'warning' : 'secondary') }}">
                                        {{ ucfirst(str_replace('_', ' ', $asset->status)) }}
                                    </span>
                                </div>
                            </div>
                            <div class="card-body">
                                <h6 class="card-title mb-2">{{ Str::limit($asset->name, 30) }}</h6>
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-tag me-1"></i>
                                        {{ $asset->category->kib_code ?? '-' }} - {{ Str::limit($asset->category->name ?? '-', 15) }}
                                    </small>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        {{ Str::limit($asset->location->name ?? '-', 20) }}
                                    </small>
                                </div>
                                <div class="mb-3">
                                    <span class="badge bg-{{ $asset->condition == 'Baik' ? 'success' : ($asset->condition == 'Rusak Ringan' ? 'warning' : 'danger') }}">
                                        {{ $asset->condition }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="text-success mb-0">Rp {{ number_format($asset->value, 0, ',', '.') }}</h6>
                                    <div class="btn-group">
                                        <a href="{{ route('opd.assets.show', $asset) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @can('update', $asset)
                                        <a href="{{ route('opd.assets.edit', $asset) }}" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-top-0 pt-0">
                                <div class="d-flex justify-content-between small text-muted">
                                    <div>
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $asset->acquisition_year }}
                                    </div>
                                    <div>
                                        @if($asset->document_verification_status == 'valid')
                                            <i class="fas fa-check-circle text-success"></i>
                                        @elseif($asset->document_verification_status == 'tidak_valid')
                                            <i class="fas fa-times-circle text-danger"></i>
                                        @else
                                            <i class="fas fa-clock text-warning"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Tidak ada data aset ditemukan</h5>
                                @if(count(request()->query()) > 0)
                                <a href="{{ route('opd.assets.index') }}" class="btn btn-primary mt-3">
                                    <i class="fas fa-redo"></i> Tampilkan semua aset
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforelse
                </div>
                
                <!-- Pagination untuk Grid View -->
                @if($assets->hasPages())
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-center">
                            {{ $assets->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
                @endif
                
            @elseif($viewMode == 'map')
                <!-- MAP VIEW -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Peta Lokasi Aset</h6>
                    </div>
                    <div class="card-body">
                        <div id="assetMap" style="height: 500px; width: 100%;"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus aset <strong id="assetNameToDelete"></strong>?</p>
                <p class="text-danger"><small>Tindakan ini tidak dapat dibatalkan.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    .badge {
        font-size: 0.75em;
    }
    .card-grid {
        transition: transform 0.2s;
    }
    .card-grid:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Load statistics
    function loadStatistics() {
        $.ajax({
            url: "{{ route('opd.assets.stats') }}",
            method: 'GET',
            success: function(response) {
                if (response.success && response.stats) {
                    const stats = response.stats;
                    $('#totalAssets').text(stats.total_assets);
                    $('#totalValue').text('Rp ' + formatNumber(stats.total_value));
                    $('#activeAssets').text(stats.active_assets);
                    
                    // Hitung aset yang perlu perhatian (rusak)
                    let needsAttention = 0;
                    if (stats.condition_distribution) {
                        needsAttention = (stats.condition_distribution['Rusak Ringan'] || 0) + 
                                        (stats.condition_distribution['Rusak Berat'] || 0);
                    }
                    $('#needsAttention').text(needsAttention);
                }
            }
        });
    }
    
    // Format number with thousands separator
    function formatNumber(number) {
        return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
    
    // Delete asset confirmation
    $('.delete-asset').click(function() {
        const assetId = $(this).data('id');
        const assetName = $(this).data('name');
        
        $('#assetNameToDelete').text(assetName);
        $('#deleteForm').attr('action', "{{ url('opd/assets') }}/" + assetId);
        $('#deleteModal').modal('show');
    });
    
    // Quick status update
    $('.quick-status-update').click(function() {
        const assetId = $(this).data('id');
        const field = $(this).data('field');
        const value = $(this).data('value');
        const assetName = $(this).data('name');
        
        if (confirm(`Ubah status "${assetName}" menjadi "${value}"?`)) {
            $.ajax({
                url: "{{ url('opd/assets') }}/" + assetId + "/update-field",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    field: field,
                    value: value,
                    notes: 'Diubah dari halaman daftar'
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        alert('Gagal mengubah status: ' + response.message);
                    }
                }
            });
        }
    });
    
    // Initialize map if in map view
    @if($viewMode == 'map')
    function initMap() {
        const map = L.map('assetMap').setView([-6.2, 106.8], 10); // Default to Jakarta
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        
        // Load assets with locations
        $.ajax({
            url: "{{ route('opd.assets.index') }}",
            method: 'GET',
            data: {
                ...@json(request()->query()),
                view: 'list',
                per_page: 100
            },
            success: function(response) {
                // You would need to parse the response and add markers
                // This is a simplified version
                console.log('Assets for map:', response);
            }
        });
    }
    
    // Load Leaflet CSS and JS if not loaded
    if (typeof L === 'undefined') {
        $.getScript('https://unpkg.com/leaflet@1.7.1/dist/leaflet.js', function() {
            $('<link>').appendTo('head').attr({
                rel: 'stylesheet',
                href: 'https://unpkg.com/leaflet@1.7.1/dist/leaflet.css'
            });
            initMap();
        });
    } else {
        initMap();
    }
    @endif
    
    // Load statistics on page load
    loadStatistics();
    
    // Auto-submit filter form on select change
    $('#per_page').change(function() {
        $('#filterForm').submit();
    });
});
</script>
@endpush