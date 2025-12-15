@extends('layouts.app')

@section('title', 'Daftar Aset')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Daftar Aset</h4>
            <div>
                <div class="btn-group" role="group">
                    <a href="{{ route('opd.assets.index', array_merge(request()->all(), ['view' => 'list'])) }}" 
                       class="btn btn-outline-secondary {{ $viewMode == 'list' ? 'active' : '' }}">
                        <i class="fas fa-list"></i>
                    </a>
                    <a href="{{ route('opd.assets.index', array_merge(request()->all(), ['view' => 'grid'])) }}" 
                       class="btn btn-outline-secondary {{ $viewMode == 'grid' ? 'active' : '' }}">
                        <i class="fas fa-th-large"></i>
                    </a>
                    <a href="{{ route('opd.assets.index', array_merge(request()->all(), ['view' => 'map'])) }}" 
                       class="btn btn-outline-secondary {{ $viewMode == 'map' ? 'active' : '' }}">
                        <i class="fas fa-map"></i>
                    </a>
                </div>
                <a href="{{ route('opd.assets.create') }}" class="btn btn-primary ms-2">
                    <i class="fas fa-plus me-1"></i> Tambah Aset
                </a>
            </div>
        </div>
        
        <!-- Filter Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Cari nama/kode..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="dalam_perbaikan" {{ request('status') == 'dalam_perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                            <option value="dimutasi" {{ request('status') == 'dimutasi' ? 'selected' : '' }}>Dimutasi</option>
                            <option value="dihapus" {{ request('status') == 'dihapus' ? 'selected' : '' }}>Dihapus</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="category_id" class="form-select">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->category_id }}" {{ request('category_id') == $category->category_id ? 'selected' : '' }}>
                                {{ $category->kib_code }} - {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="condition" class="form-select">
                            <option value="">Semua Kondisi</option>
                            <option value="Baik" {{ request('condition') == 'Baik' ? 'selected' : '' }}>Baik</option>
                            <option value="Rusak Ringan" {{ request('condition') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="Rusak Berat" {{ request('condition') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="per_page" class="form-select">
                            <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20 per halaman</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per halaman</option>
                            <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per halaman</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        @if($viewMode == 'list')
        <!-- List View -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table data-table">
                        <thead>
                            <tr>
                                <th>Kode Aset</th>
                                <th>Nama Aset</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th>Kondisi</th>
                                <th>Status</th>
                                <th>Nilai</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assets as $asset)
                            <tr>
                                <td>
                                    <code>{{ $asset->asset_code }}</code>
                                    @if($asset->asset_code_old)
                                    <br><small class="text-muted">Lama: {{ $asset->asset_code_old }}</small>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $asset->name }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $asset->brand ?? '' }} {{ $asset->model ?? '' }}</small>
                                </td>
                                <td>
                                    {{ $asset->category->kib_code ?? '-' }} - {{ $asset->category->name ?? '-' }}
                                </td>
                                <td>{{ $asset->location->name ?? '-' }}</td>
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
                                            'dalam_perbaikan' => 'warning',
                                            'dimutasi' => 'info',
                                            'dihapus' => 'secondary'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$asset->status] ?? 'secondary' }}">
                                        {{ ucfirst(str_replace('_', ' ', $asset->status)) }}
                                    </span>
                                    <br>
                                    <small>
                                        @if($asset->document_verification_status == 'valid')
                                            <i class="fas fa-check-circle text-success" title="Terverifikasi"></i>
                                        @endif
                                        @if($asset->validation_status == 'disetujui')
                                            <i class="fas fa-check-double text-success" title="Tervalidasi"></i>
                                        @endif
                                    </small>
                                </td>
                                <td>Rp {{ number_format($asset->value, 0, ',', '.') }}</td>
                                <td class="action-buttons">
                                    <a href="{{ route('opd.assets.show', $asset) }}" class="btn btn-sm btn-outline-primary" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($asset->document_verification_status == 'belum_diverifikasi' && $asset->validation_status == 'belum_divalidasi')
                                    <a href="{{ route('opd.assets.edit', $asset) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-center">
                    {{ $assets->appends(request()->all())->links() }}
                </div>
            </div>
        </div>
        
        @elseif($viewMode == 'grid')
        <!-- Grid View -->
        <div class="row">
            @foreach($assets as $asset)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="mb-0">{{ $asset->name }}</h6>
                                <small class="text-muted">{{ $asset->asset_code }}</small>
                            </div>
                            @php
                                $statusColors = [
                                    'aktif' => 'success',
                                    'dalam_perbaikan' => 'warning',
                                    'dimutasi' => 'info',
                                    'dihapus' => 'secondary'
                                ];
                            @endphp
                            <span class="badge bg-{{ $statusColors[$asset->status] ?? 'secondary' }}">
                                {{ ucfirst(str_replace('_', ' ', $asset->status)) }}
                            </span>
                        </div>
                        
                        <div class="mb-3">
                            <small class="text-muted">Kategori:</small>
                            <p class="mb-0">{{ $asset->category->name ?? '-' }}</p>
                        </div>
                        
                        <div class="mb-3">
                            <small class="text-muted">Lokasi:</small>
                            <p class="mb-0">{{ $asset->location->name ?? '-' }}</p>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <small class="text-muted">Kondisi:</small>
                                <p class="mb-0">
                                    @php
                                        $conditionColors = [
                                            'Baik' => 'text-success',
                                            'Rusak Ringan' => 'text-warning',
                                            'Rusak Berat' => 'text-danger'
                                        ];
                                    @endphp
                                    <i class="fas fa-circle {{ $conditionColors[$asset->condition] ?? 'text-secondary' }}"></i>
                                    {{ $asset->condition }}
                                </p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Nilai:</small>
                                <p class="mb-0">Rp {{ number_format($asset->value, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <div>
                                @if($asset->document_verification_status == 'valid')
                                    <i class="fas fa-check-circle text-success" title="Terverifikasi"></i>
                                @endif
                                @if($asset->validation_status == 'disetujui')
                                    <i class="fas fa-check-double text-success" title="Tervalidasi"></i>
                                @endif
                            </div>
                            <div>
                                <a href="{{ route('opd.assets.show', $asset) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="d-flex justify-content-center">
            {{ $assets->appends(request()->all())->links() }}
        </div>
        
        @elseif($viewMode == 'map')
        <!-- Map View -->
        <div class="card">
            <div class="card-body">
                <div id="assetMap" style="height: 500px; border-radius: 5px;"></div>
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i> Tampilkan lokasi aset berdasarkan data GPS. 
                        Pastikan lokasi memiliki koordinat latitude dan longitude.
                    </small>
                </div>
            </div>
        </div>
        
        @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            $(document).ready(function() {
                const map = L.map('assetMap').setView([-2.5489, 118.0149], 5);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(map);
                
                // Add markers for locations with assets
                @foreach($locations as $location)
                    @if($location->latitude && $location->longitude && $location->assets_count > 0)
                        const marker{{ $location->location_id }} = L.marker([{{ $location->latitude }}, {{ $location->longitude }}])
                            .addTo(map)
                            .bindPopup(`
                                <strong>{{ $location->name }}</strong><br>
                                {{ $location->address }}<br>
                                <small>{{ $location->assets_count }} aset</small><br>
                                <a href="{{ route('opd.master.index', ['tab' => 'map']) }}" class="btn btn-sm btn-primary mt-2">Lihat Detail</a>
                            `);
                    @endif
                @endforeach
            });
        </script>
        @endpush
        @endif
    </div>
</div>
@endsection