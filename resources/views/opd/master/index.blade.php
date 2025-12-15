@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Data Master</h4>
            @if($tab == 'locations')
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLocationModal">
                <i class="fas fa-plus me-1"></i> Tambah Lokasi
            </button>
            @endif
        </div>
        
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ $tab == 'locations' ? 'active' : '' }}" 
                   href="{{ route('opd.master.index', ['tab' => 'locations']) }}">
                    <i class="fas fa-map-marker-alt me-1"></i> Lokasi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab == 'categories' ? 'active' : '' }}" 
                   href="{{ route('opd.master.index', ['tab' => 'categories']) }}">
                    <i class="fas fa-tags me-1"></i> Kategori
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab == 'map' ? 'active' : '' }}" 
                   href="{{ route('opd.master.index', ['tab' => 'map']) }}">
                    <i class="fas fa-map me-1"></i> Peta Lokasi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab == 'statistics' ? 'active' : '' }}" 
                   href="{{ route('opd.master.index', ['tab' => 'statistics']) }}">
                    <i class="fas fa-chart-bar me-1"></i> Statistik
                </a>
            </li>
        </ul>
        
        <div class="tab-content mt-4">
            @if($tab == 'locations')
                <!-- Locations Tab -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <form method="GET" class="row g-3">
                                    <input type="hidden" name="tab" value="locations">
                                    <div class="col-md-4">
                                        <input type="text" name="search" class="form-control" placeholder="Cari lokasi..." 
                                               value="{{ request('search') }}">
                                    </div>
                                    <div class="col-md-3">
                                        <select name="type" class="form-select">
                                            <option value="">Semua Jenis</option>
                                            @foreach($locationTypes as $type)
                                            <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                                {{ ucfirst($type) }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-search"></i> Cari
                                        </button>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="{{ route('opd.master.index', ['tab' => 'locations']) }}" class="btn btn-outline-secondary w-100">
                                            Reset
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table data-table">
                                        <thead>
                                            <tr>
                                                <th>Nama Lokasi</th>
                                                <th>Jenis</th>
                                                <th>Alamat</th>
                                                <th>Jumlah Aset</th>
                                                <th>Koordinat</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data['locations'] as $location)
                                            <tr>
                                                <td>
                                                    <strong>{{ $location->name }}</strong>
                                                    @if($location->description)
                                                    <br><small class="text-muted">{{ $location->description }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-info">{{ ucfirst($location->type) }}</span>
                                                </td>
                                                <td>{{ $location->address ?? '-' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $location->assets_count > 0 ? 'primary' : 'secondary' }}">
                                                        {{ $location->assets_count }} aset
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($location->latitude && $location->longitude)
                                                    <small>{{ $location->latitude }}, {{ $location->longitude }}</small>
                                                    @else
                                                    <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <button type="button" class="btn btn-outline-info" 
                                                                onclick="viewLocation({{ $location->location_id }})">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-outline-warning" 
                                                                onclick="editLocation({{ $location->location_id }})">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        @if($location->assets_count == 0)
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="deleteLocation({{ $location->location_id }})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="d-flex justify-content-center">
                                    {{ $data['locations']->appends(request()->all())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            @elseif($tab == 'categories')
                <!-- Categories Tab -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Kategori Aset (KIB)</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table data-table">
                                        <thead>
                                            <tr>
                                                <th>Kode KIB</th>
                                                <th>Nama Kategori</th>
                                                <th>Deskripsi</th>
                                                <th>Jumlah Aset</th>
                                                <th>Total Nilai</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data['categories'] as $category)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-primary">{{ $category->kib_code }}</span>
                                                </td>
                                                <td>
                                                    <strong>{{ $category->name }}</strong>
                                                </td>
                                                <td>{{ $category->description ?? '-' }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ $category->assets_count ?? 0 }}</span>
                                                </td>
                                                <td>Rp {{ number_format($category->assets_sum_value ?? 0, 0, ',', '.') }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="d-flex justify-content-center">
                                    {{ $data['categories']->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Sub Categories -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Sub Kategori Berdasarkan KIB</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @php
                                        $subCategories = [
                                            'A' => ['01' => 'Tanah Perkantoran', '02' => 'Tanah Fasilitas Umum', '03' => 'Tanah Lainnya'],
                                            'B' => ['01' => 'Alat Berat', '02' => 'Alat Elektronik', '03' => 'Kendaraan Dinas', '04' => 'Peralatan Medis', '05' => 'Peralatan Olahraga', '06' => 'Furniture', '07' => 'Alat Laboratorium'],
                                            'C' => ['01' => 'Gedung Kantor', '02' => 'Gedung Sekolah', '03' => 'Rumah Sakit', '04' => 'Gedung Olahraga (GOR)', '05' => 'Stadion'],
                                            'D' => ['01' => 'Jalan Kota', '02' => 'Jembatan', '03' => 'Jaringan Irigasi', '04' => 'Jaringan Internet'],
                                            'E' => ['01' => 'Koleksi Perpustakaan', '02' => 'Aset Lainnya'],
                                            'F' => ['01' => 'Konstruksi Gedung', '02' => 'Konstruksi Jalan', '03' => 'Konstruksi Lainnya']
                                        ];
                                    @endphp
                                    
                                    @foreach($subCategories as $kibCode => $subs)
                                    <div class="col-md-4 mb-4">
                                        <div class="card">
                                            <div class="card-header">
                                                <h6 class="mb-0">KIB {{ $kibCode }}</h6>
                                            </div>
                                            <div class="card-body">
                                                <ul class="list-group list-group-flush">
                                                    @foreach($subs as $code => $name)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <small class="text-muted">{{ $code }}</small>
                                                            <div>{{ $name }}</div>
                                                        </div>
                                                        <span class="badge bg-light text-dark">
                                                            {{ \App\Models\Asset::whereHas('category', function($q) use ($kibCode) {
                                                                $q->where('kib_code', $kibCode);
                                                            })->where('sub_category_code', $code)->count() }}
                                                        </span>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            @elseif($tab == 'map')
                <!-- Map Tab -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Peta Distribusi Lokasi Aset</h6>
                            </div>
                            <div class="card-body">
                                <div id="locationMap" style="height: 600px; border-radius: 5px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <h6 class="mb-3">Daftar Lokasi dengan Koordinat</h6>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Nama Lokasi</th>
                                                <th>Jenis</th>
                                                <th>Alamat</th>
                                                <th>Koordinat</th>
                                                <th>Jumlah Aset</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data['locations'] as $location)
                                            <tr>
                                                <td>{{ $location->name }}</td>
                                                <td>
                                                    <span class="badge bg-info">{{ ucfirst($location->type) }}</span>
                                                </td>
                                                <td>{{ $location->address ?? '-' }}</td>
                                                <td>
                                                    @if($location->latitude && $location->longitude)
                                                    <code>{{ $location->latitude }}, {{ $location->longitude }}</code>
                                                    @else
                                                    <span class="text-danger">
                                                        <i class="fas fa-exclamation-triangle"></i> Koordinat belum diisi
                                                    </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ $location->assets_count > 0 ? 'primary' : 'secondary' }}">
                                                        {{ $location->assets_count }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-warning" 
                                                            onclick="editLocation({{ $location->location_id }})">
                                                        <i class="fas fa-edit"></i> Edit Koordinat
                                                    </button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                @push('styles')
                <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
                <style>
                .leaflet-popup-content {
                    min-width: 200px;
                }
                </style>
                @endpush
                
                @push('scripts')
                <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
                <script>
                $(document).ready(function() {
                    const map = L.map('locationMap').setView([-2.5489, 118.0149], 5);
                    
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(map);
                    
                    // Add markers for locations with coordinates
                    @foreach($data['locations'] as $location)
                        @if($location->latitude && $location->longitude)
                            const marker{{ $location->location_id }} = L.marker([{{ $location->latitude }}, {{ $location->longitude }}])
                                .addTo(map)
                                .bindPopup(`
                                    <div style="min-width: 200px;">
                                        <h6><strong>{{ $location->name }}</strong></h6>
                                        <p class="mb-1"><small>{{ $location->address }}</small></p>
                                        <p class="mb-1">
                                            <span class="badge bg-info">{{ ucfirst($location->type) }}</span>
                                            <span class="badge bg-primary ms-1">{{ $location->assets_count }} aset</span>
                                        </p>
                                        <button class="btn btn-sm btn-primary w-100 mt-2" onclick="viewLocation({{ $location->location_id }})">
                                            <i class="fas fa-eye me-1"></i> Lihat Detail
                                        </button>
                                    </div>
                                `);
                        @endif
                    @endforeach
                });
                </script>
                @endpush
                
            @elseif($tab == 'statistics')
                <!-- Statistics Tab -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Statistik Lokasi</h6>
                            </div>
                            <div class="card-body">
                                <table class="table">
                                    <tr>
                                        <td>Total Lokasi</td>
                                        <td class="text-end">{{ number_format($data['locationStats']['total_locations']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Lokasi dengan Aset</td>
                                        <td class="text-end">{{ number_format($data['locationStats']['locations_with_assets']) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Total Aset di OPD</td>
                                        <td class="text-end">{{ number_format($data['locationStats']['total_assets']) }}</td>
                                    </tr>
                                </table>
                                
                                <h6 class="mt-4">Distribusi Jenis Lokasi</h6>
                                <canvas id="locationTypeChart" height="150"></canvas>
                                
                                <h6 class="mt-4">Top 5 Lokasi dengan Aset Terbanyak</h6>
                                <div class="list-group">
                                    @foreach(array_slice($data['locationStats']['top_locations']->toArray(), 0, 5) as $location)
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $location['name'] }}</strong>
                                            <br>
                                            <small class="text-muted">{{ ucfirst($location['type']) }}</small>
                                        </div>
                                        <span class="badge bg-primary rounded-pill">{{ $location['asset_count'] }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Statistik Kategori</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>KIB</th>
                                                <th>Kategori</th>
                                                <th class="text-end">Jumlah</th>
                                                <th class="text-end">Nilai</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data['categoryStats'] as $category)
                                            <tr>
                                                <td>
                                                    <span class="badge bg-primary">{{ $category['kib_code'] }}</span>
                                                </td>
                                                <td>{{ $category['category_name'] }}</td>
                                                <td class="text-end">{{ number_format($category['asset_count']) }}</td>
                                                <td class="text-end">
                                                    Rp {{ number_format($category['total_value'], 0, ',', '.') }}
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <h6 class="mt-4">Distribusi Aset per Kategori</h6>
                                <canvas id="categoryChart" height="150"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                @push('scripts')
                <script>
                $(document).ready(function() {
                    // Location Type Chart
                    const locationTypeCtx = document.getElementById('locationTypeChart').getContext('2d');
                    const locationTypeChart = new Chart(locationTypeCtx, {
                        type: 'pie',
                        data: {
                            labels: @json(array_keys($data['locationStats']['type_distribution']->toArray())),
                            datasets: [{
                                data: @json(array_values($data['locationStats']['type_distribution']->toArray())),
                                backgroundColor: ['#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6']
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: {
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                    
                    // Category Chart
                    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
                    const categoryChart = new Chart(categoryCtx, {
                        type: 'bar',
                        data: {
                            labels: @json(collect($data['categoryStats'])->pluck('category_name')),
                            datasets: [{
                                label: 'Jumlah Aset',
                                data: @json(collect($data['categoryStats'])->pluck('asset_count')),
                                backgroundColor: '#3498db'
                            }]
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                });
                </script>
                @endpush
            @endif
        </div>
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="addLocationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Lokasi Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addLocationForm" action="{{ route('opd.master.location-store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lokasi *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Jenis Lokasi *</label>
                        <select class="form-select" id="type" name="type" required>
                            <option value="">Pilih Jenis</option>
                            @foreach($locationTypes as $type)
                            <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="address" class="form-label">Alamat</label>
                        <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="latitude" class="form-label">Latitude</label>
                            <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Contoh: -6.2088">
                        </div>
                        <div class="col-md-6">
                            <label for="longitude" class="form-label">Longitude</label>
                            <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Contoh: 106.8456">
                        </div>
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

<div class="modal fade" id="editLocationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editLocationForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Nama Lokasi *</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_type" class="form-label">Jenis Lokasi *</label>
                        <select class="form-select" id="edit_type" name="type" required>
                            <option value="">Pilih Jenis</option>
                            @foreach($locationTypes as $type)
                            <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_address" class="form-label">Alamat</label>
                        <textarea class="form-control" id="edit_address" name="address" rows="2"></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="edit_latitude" class="form-label">Latitude</label>
                            <input type="text" class="form-control" id="edit_latitude" name="latitude">
                        </div>
                        <div class="col-md-6">
                            <label for="edit_longitude" class="form-label">Longitude</label>
                            <input type="text" class="form-control" id="edit_longitude" name="longitude">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="viewLocationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Lokasi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="locationDetail"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewLocation(locationId) {
    $.ajax({
        url: `/opd/master/locations/${locationId}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const location = response.location;
                const assets = response.assets;
                
                let html = `
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-sm">
                                <tr>
                                    <th width="40%">Nama Lokasi</th>
                                    <td>${location.name}</td>
                                </tr>
                                <tr>
                                    <th>Jenis</th>
                                    <td><span class="badge bg-info">${location.type}</span></td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>${location.address || '-'}</td>
                                </tr>
                                <tr>
                                    <th>Koordinat</th>
                                    <td>
                                        ${location.latitude && location.longitude 
                                            ? `${location.latitude}, ${location.longitude}` 
                                            : '<span class="text-danger">Belum diisi</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th>Jumlah Aset</th>
                                    <td><span class="badge bg-primary">${location.assets_count}</span></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>10 Aset Terbaru</h6>
                            <div class="list-group" style="max-height: 300px; overflow-y: auto;">
                `;
                
                if (assets.length > 0) {
                    assets.forEach(asset => {
                        html += `
                            <a href="/opd/assets/${asset.asset_id}" class="list-group-item list-group-item-action">
                                <div class="d-flex w-100 justify-content-between">
                                    <h6 class="mb-1">${asset.name}</h6>
                                    <small>${asset.asset_code}</small>
                                </div>
                                <p class="mb-1">
                                    <span class="badge bg-${asset.condition === 'Baik' ? 'success' : (asset.condition === 'Rusak Ringan' ? 'warning' : 'danger')}">
                                        ${asset.condition}
                                    </span>
                                    <span class="badge bg-info ms-1">${asset.category?.name || '-'}</span>
                                </p>
                            </a>
                        `;
                    });
                } else {
                    html += `
                        <div class="text-center py-4">
                            <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                            <p class="text-muted">Belum ada aset di lokasi ini</p>
                        </div>
                    `;
                }
                
                html += `
                            </div>
                        </div>
                    </div>
                `;
                
                $('#locationDetail').html(html);
                new bootstrap.Modal(document.getElementById('viewLocationModal')).show();
            }
        }
    });
}

function editLocation(locationId) {
    // Fetch location data first
    $.ajax({
        url: `/opd/master/locations/${locationId}`,
        type: 'GET',
        success: function(response) {
            if (response.success) {
                const location = response.location;
                
                // Populate form
                $('#edit_name').val(location.name);
                $('#edit_type').val(location.type);
                $('#edit_address').val(location.address || '');
                $('#edit_latitude').val(location.latitude || '');
                $('#edit_longitude').val(location.longitude || '');
                
                // Set form action
                $('#editLocationForm').attr('action', `/opd/master/locations/${locationId}/update`);
                
                // Show modal
                new bootstrap.Modal(document.getElementById('editLocationModal')).show();
            }
        }
    });
}

function deleteLocation(locationId) {
    if (confirm('Apakah Anda yakin ingin menghapus lokasi ini?')) {
        $.ajax({
            url: `/opd/master/locations/${locationId}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success || response.redirect) {
                    window.location.reload();
                } else {
                    alert(response.message || 'Gagal menghapus lokasi');
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Gagal menghapus lokasi');
            }
        });
    }
}

// Form submission
$('#addLocationForm').on('submit', function(e) {
    e.preventDefault();
    const formData = $(this).serialize();
    
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                window.location.reload();
            } else {
                alert(response.message || 'Gagal menambahkan lokasi');
            }
        }
    });
});

$('#editLocationForm').on('submit', function(e) {
    e.preventDefault();
    const formData = $(this).serialize();
    
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
        success: function(response) {
            if (response.success) {
                window.location.reload();
            } else {
                alert(response.message || 'Gagal mengupdate lokasi');
            }
        }
    });
});
</script>
@endpush
@endsection