@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="viewModeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-th"></i> {{ $viewMode == 'list' ? 'List' : ($viewMode == 'grid' ? 'Grid' : 'Map') }}
                </button>
                <ul class="dropdown-menu" aria-labelledby="viewModeDropdown">
                    <li><a class="dropdown-item {{ $viewMode == 'list' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['view' => 'list']) }}"><i class="fas fa-list"></i> List View</a></li>
                    <li><a class="dropdown-item {{ $viewMode == 'grid' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}"><i class="fas fa-th-large"></i> Grid View</a></li>
                    <li><a class="dropdown-item {{ $viewMode == 'map' ? 'active' : '' }}" href="{{ request()->fullUrlWithQuery(['view' => 'map']) }}"><i class="fas fa-map"></i> Map View</a></li>
                </ul>
            </div>
            
            <a href="{{ route('opd.assets.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Aset
            </a>
            
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="fas fa-filter"></i> Filter
            </button>
            
            <button type="button" class="btn btn-outline-success" id="exportBtn">
                <i class="fas fa-file-export"></i> Export
            </button>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Aset</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $assets->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-boxes fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Aset Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="activeAssetsCount">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Nilai Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalValue">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Terverifikasi</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" id="verifiedAssetsCount">-</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-double fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form method="GET" action="{{ route('opd.assets.index') }}">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filterModalLabel">Filter Aset</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="dimutasi" {{ request('status') == 'dimutasi' ? 'selected' : '' }}>Dimutasi</option>
                                    <option value="dihapus" {{ request('status') == 'dihapus' ? 'selected' : '' }}>Dihapus</option>
                                    <option value="dalam_perbaikan" {{ request('status') == 'dalam_perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                                    <option value="nonaktif" {{ request('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label">Kategori</label>
                                <select name="category_id" id="category_id" class="form-select">
                                    <option value="">Semua Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}" {{ request('category_id') == $category->category_id ? 'selected' : '' }}>
                                            {{ $category->kib_code }} - {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="condition" class="form-label">Kondisi</label>
                                <select name="condition" id="condition" class="form-select">
                                    <option value="">Semua Kondisi</option>
                                    <option value="Baik" {{ request('condition') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="Rusak Ringan" {{ request('condition') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                    <option value="Rusak Berat" {{ request('condition') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="location_id" class="form-label">Lokasi</label>
                                <select name="location_id" id="location_id" class="form-select">
                                    <option value="">Semua Lokasi</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->location_id }}" {{ request('location_id') == $location->location_id ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="document_verification_status" class="form-label">Status Verifikasi Dokumen</label>
                                <select name="document_verification_status" id="document_verification_status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="belum_diverifikasi" {{ request('document_verification_status') == 'belum_diverifikasi' ? 'selected' : '' }}>Belum Diverifikasi</option>
                                    <option value="valid" {{ request('document_verification_status') == 'valid' ? 'selected' : '' }}>Valid</option>
                                    <option value="tidak_valid" {{ request('document_verification_status') == 'tidak_valid' ? 'selected' : '' }}>Tidak Valid</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="validation_status" class="form-label">Status Validasi</label>
                                <select name="validation_status" id="validation_status" class="form-select">
                                    <option value="">Semua Status</option>
                                    <option value="belum_divalidasi" {{ request('validation_status') == 'belum_divalidasi' ? 'selected' : '' }}>Belum Divalidasi</option>
                                    <option value="disetujui" {{ request('validation_status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="revisi" {{ request('validation_status') == 'revisi' ? 'selected' : '' }}>Revisi</option>
                                    <option value="ditolak" {{ request('validation_status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="search" class="form-label">Pencarian</label>
                                <input type="text" class="form-control" id="search" name="search" 
                                       value="{{ request('search') }}" placeholder="Cari berdasarkan nama, kode aset...">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <a href="{{ route('opd.assets.index') }}" class="btn btn-secondary">Reset</a>
                        <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    @if($viewMode == 'list')
        <!-- List View -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover" id="assetsTable">
                        <thead>
                            <tr>
                                <th>Kode Aset</th>
                                <th>Nama Aset</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Nilai</th>
                                <th>Status</th>
                                <th>Kondisi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assets as $asset)
                                <tr>
                                    <td>
                                        <strong>{{ $asset->asset_code }}</strong>
                                        @if($asset->asset_code_old)
                                            <br><small class="text-muted">Lama: {{ $asset->asset_code_old }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $asset->name }}
                                        <br>
                                        <small class="text-muted">{{ $asset->sub_category_name }}</small>
                                    </td>
                                    <td>
                                        {{ $asset->category->kib_code }} - {{ $asset->category->name }}
                                    </td>
                                    <td>
                                        {{ $asset->location->name ?? '-' }}
                                    </td>
                                    <td>{{ $asset->formatted_value }}</td>
                                    <td>
                                        <span class="badge bg-{{ $asset->status == 'aktif' ? 'success' : ($asset->status == 'dalam_perbaikan' ? 'warning' : 'secondary') }}">
                                            {{ $asset->status }}
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            Verif: {{ $asset->document_verification_status }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $asset->condition == 'Baik' ? 'success' : ($asset->condition == 'Rusak Ringan' ? 'warning' : 'danger') }}">
                                            {{ $asset->condition }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('opd.assets.show', $asset) }}" class="btn btn-sm btn-info" title="Detail">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($asset->document_verification_status == 'belum_diverifikasi' && $asset->validation_status == 'belum_divalidasi')
                                                <a href="{{ route('opd.assets.edit', $asset) }}" class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('opd.assets.destroy', $asset) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus aset ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-box-open fa-3x mb-3"></i>
                                            <p>Tidak ada data aset</p>
                                            <a href="{{ route('opd.assets.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Tambah Aset Pertama
                                            </a>
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
                            Menampilkan {{ $assets->firstItem() }} sampai {{ $assets->lastItem() }} dari {{ $assets->total() }} data
                        </div>
                        {{ $assets->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>
        </div>
        
    @elseif($viewMode == 'grid')
        <!-- Grid View -->
        <div class="row">
            @forelse($assets as $asset)
                <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                    <div class="card h-100 shadow">
                        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 class="m-0 font-weight-bold text-primary">
                                {{ Str::limit($asset->name, 30) }}
                            </h6>
                            <div class="dropdown no-arrow">
                                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink{{ $asset->asset_id }}" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink{{ $asset->asset_id }}">
                                    <a class="dropdown-item" href="{{ route('opd.assets.show', $asset) }}">
                                        <i class="fas fa-eye fa-sm fa-fw mr-2 text-gray-400"></i> Detail
                                    </a>
                                    @if($asset->document_verification_status == 'belum_diverifikasi' && $asset->validation_status == 'belum_divalidasi')
                                        <a class="dropdown-item" href="{{ route('opd.assets.edit', $asset) }}">
                                            <i class="fas fa-edit fa-sm fa-fw mr-2 text-gray-400"></i> Edit
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <form action="{{ route('opd.assets.destroy', $asset) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger" onclick="return confirm('Yakin ingin menghapus?')">
                                                <i class="fas fa-trash fa-sm fa-fw mr-2 text-gray-400"></i> Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="text-muted small">Kode Aset</div>
                                <div class="font-weight-bold">{{ $asset->asset_code }}</div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="text-muted small">Kategori</div>
                                <div>{{ $asset->category->kib_code }} - {{ $asset->category->name }}</div>
                                <small class="text-muted">{{ $asset->sub_category_name }}</small>
                            </div>
                            
                            <div class="mb-3">
                                <div class="text-muted small">Lokasi</div>
                                <div>{{ $asset->location->name ?? '-' }}</div>
                            </div>
                            
                            <div class="row mb-3">
                                <div class="col-6">
                                    <div class="text-muted small">Nilai</div>
                                    <div class="font-weight-bold text-success">{{ $asset->formatted_value }}</div>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">Tahun</div>
                                    <div>{{ $asset->acquisition_year }}</div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-6">
                                    <div class="text-muted small">Status</div>
                                    <span class="badge bg-{{ $asset->status == 'aktif' ? 'success' : ($asset->status == 'dalam_perbaikan' ? 'warning' : 'secondary') }}">
                                        {{ $asset->status }}
                                    </span>
                                </div>
                                <div class="col-6">
                                    <div class="text-muted small">Kondisi</div>
                                    <span class="badge bg-{{ $asset->condition == 'Baik' ? 'success' : ($asset->condition == 'Rusak Ringan' ? 'warning' : 'danger') }}">
                                        {{ $asset->condition }}
                                    </span>
                                </div>
                            </div>
                            
                            @if($asset->document_verification_status != 'valid' || $asset->validation_status != 'disetujui')
                                <div class="mt-3 p-2 bg-light rounded">
                                    <small class="text-warning">
                                        <i class="fas fa-exclamation-triangle"></i>
                                        Belum diverifikasi/validasi
                                    </small>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer">
                            <small class="text-muted">
                                Dibuat: {{ $asset->created_at->format('d/m/Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">Tidak ada data aset</h5>
                        <p class="text-muted">Mulai dengan menambahkan aset pertama Anda</p>
                        <a href="{{ route('opd.assets.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Tambah Aset
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
        
        <!-- Pagination for Grid View -->
        @if($assets->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $assets->appends(request()->query())->links() }}
            </div>
        @endif
        
    @elseif($viewMode == 'map')
        <!-- Map View -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Peta Lokasi Aset</h6>
            </div>
            <div class="card-body">
                <div id="assetMap" style="height: 500px; width: 100%;"></div>
                <div class="mt-3">
                    <div class="row">
                        @foreach($assets->take(10) as $asset)
                            @if($asset->location && $asset->location->latitude && $asset->location->longitude)
                                <div class="col-md-6 mb-2">
                                    <div class="card border-left-info shadow-sm h-100">
                                        <div class="card-body py-2">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $asset->name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $asset->location->name }}</small>
                                                </div>
                                                <a href="{{ route('opd.assets.show', $asset) }}" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<style>
    .badge {
        font-size: 0.85em;
        padding: 0.35em 0.65em;
    }
    
    .card {
        transition: transform 0.2s;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    #assetMap {
        border-radius: 8px;
        overflow: hidden;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
    // Load statistics
    $(document).ready(function() {
        loadAssetStatistics();
        
        // Export button
        $('#exportBtn').click(function() {
            Swal.fire({
                title: 'Ekspor Data',
                text: 'Pilih format ekspor:',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Excel',
                cancelButtonText: 'PDF',
                showDenyButton: true,
                denyButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    exportAssets('excel');
                } else if (result.dismiss === Swal.DismissReason.cancel) {
                    exportAssets('pdf');
                }
            });
        });
        
        // Initialize map if in map view
        @if($viewMode == 'map')
            initializeMap();
        @endif
    });
    
    function loadAssetStatistics() {
        $.ajax({
            url: '{{ route("opd.assets.stats") }}',
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    $('#activeAssetsCount').text(response.stats.active_assets || 0);
                    $('#verifiedAssetsCount').text(response.stats.verified_assets || 0);
                    
                    // Format total value
                    if (response.stats.total_value) {
                        const formattedValue = new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR',
                            minimumFractionDigits: 0
                        }).format(response.stats.total_value);
                        $('#totalValue').text(formattedValue);
                    }
                }
            },
            error: function() {
                console.error('Failed to load asset statistics');
            }
        });
    }
    
    function exportAssets(format) {
        const params = new URLSearchParams(window.location.search);
        params.append('format', format);
        
        window.location.href = '{{ route("opd.assets.export") }}?' + params.toString();
    }
    
    function initializeMap() {
        const map = L.map('assetMap').setView([-6.2088, 106.8456], 12);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Add markers for assets with location data
        @foreach($assets as $asset)
            @if($asset->location && $asset->location->latitude && $asset->location->longitude)
                const marker{{ $asset->asset_id }} = L.marker([{{ $asset->location->latitude }}, {{ $asset->location->longitude }}])
                    .addTo(map)
                    .bindPopup(`
                        <div class="map-popup">
                            <h6><strong>{{ $asset->name }}</strong></h6>
                            <p>Kode: {{ $asset->asset_code }}</p>
                            <p>Lokasi: {{ $asset->location->name }}</p>
                            <p>Status: <span class="badge bg-{{ $asset->status == 'aktif' ? 'success' : 'secondary' }}">{{ $asset->status }}</span></p>
                            <a href="{{ route('opd.assets.show', $asset) }}" class="btn btn-sm btn-primary btn-block mt-2">
                                Lihat Detail
                            </a>
                        </div>
                    `);
            @endif
        @endforeach
        
        // Fit bounds to show all markers
        const bounds = L.latLngBounds([]);
        @foreach($assets as $asset)
            @if($asset->location && $asset->location->latitude && $asset->location->longitude)
                bounds.extend([{{ $asset->location->latitude }}, {{ $asset->location->longitude }}]);
            @endif
        @endforeach
        
        if (bounds.isValid()) {
            map.fitBounds(bounds, { padding: [50, 50] });
        }
    }
</script>
@endpush