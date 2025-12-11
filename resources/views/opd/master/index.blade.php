@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </div>
        </div>
    </div>

    <!-- Master Data Tabs -->
    <ul class="nav nav-tabs" id="masterTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'locations' ? 'active' : '' }}" 
                    id="locations-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#locations"
                    type="button"
                    onclick="window.location.href='{{ route('opd.master.index') }}?tab=locations'">
                <i class="fas fa-map-marker-alt me-1"></i> Lokasi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'categories' ? 'active' : '' }}" 
                    id="categories-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#categories"
                    type="button"
                    onclick="window.location.href='{{ route('opd.master.index') }}?tab=categories'">
                <i class="fas fa-tags me-1"></i> Kategori
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'map' ? 'active' : '' }}" 
                    id="map-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#map"
                    type="button"
                    onclick="window.location.href='{{ route('opd.master.index') }}?tab=map'">
                <i class="fas fa-map me-1"></i> Peta Lokasi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'statistics' ? 'active' : '' }}" 
                    id="statistics-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#statistics"
                    type="button"
                    onclick="window.location.href='{{ route('opd.master.index') }}?tab=statistics'">
                <i class="fas fa-chart-bar me-1"></i> Statistik
            </button>
        </li>
    </ul>

    <div class="tab-content mt-3" id="masterTabsContent">
        <!-- Locations Tab -->
        <div class="tab-pane fade {{ $tab == 'locations' ? 'show active' : '' }}" id="locations">
            <!-- Filter Section -->
            <div class="card mb-4 shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-filter me-2"></i> Filter Lokasi
                    </h6>
                </div>
                <div class="card-body">
                    <form id="locationFilterForm" method="GET" action="{{ route('opd.master.index') }}">
                        <input type="hidden" name="tab" value="locations">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Jenis Lokasi</label>
                                    <select class="form-select" id="type" name="type" onchange="this.form.submit()">
                                        <option value="">Semua Jenis</option>
                                        @foreach($locationTypes as $type)
                                            @php
                                                $typeNames = [
                                                    'gedung' => 'Gedung',
                                                    'ruangan' => 'Ruangan',
                                                    'gudang' => 'Gudang',
                                                    'lapangan' => 'Lapangan',
                                                    'lainnya' => 'Lainnya'
                                                ];
                                            @endphp
                                            <option value="{{ $type }}" {{ (request('type') ?? ($data['type'] ?? '')) == $type ? 'selected' : '' }}>
                                                {{ $typeNames[$type] ?? $type }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="search" class="form-label">Pencarian</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="search" name="search" 
                                               placeholder="Cari nama atau alamat lokasi..." value="{{ request('search') }}">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        @if(request('search') || request('type'))
                                            <a href="{{ route('opd.master.index', ['tab' => 'locations']) }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-times"></i> Reset
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Locations Table Card -->
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Daftar Lokasi</h6>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addLocationModal">
                        <i class="fas fa-plus me-1"></i> Tambah Lokasi
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th>Jenis</th>
                                    <th>Alamat</th>
                                    <th>Koordinat</th>
                                    <th>Jumlah Aset</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($data['locations'] ?? []) as $location)
                                    <tr>
                                        <td>
                                            <strong>{{ $location->name }}</strong>
                                        </td>
                                        <td>
                                            @php
                                                $typeBadges = [
                                                    'gedung' => 'primary',
                                                    'ruangan' => 'info',
                                                    'gudang' => 'warning',
                                                    'lapangan' => 'success',
                                                    'lainnya' => 'secondary'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $typeBadges[$location->type] ?? 'secondary' }}">
                                                {{ $location->type }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($location->address)
                                                <small>{{ Str::limit($location->address, 50) }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($location->latitude && $location->longitude)
                                                <small class="text-muted">{{ $location->latitude }}, {{ $location->longitude }}</small>
                                            @else
                                                <span class="text-muted">Belum diatur</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $location->assets_count > 0 ? 'info' : 'secondary' }}">
                                                {{ $location->assets_count }} aset
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-outline-info" 
                                                        onclick="viewLocation({{ $location->location_id }})"
                                                        title="Lihat Detail">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-primary"
                                                        onclick="editLocation({{ $location->location_id }})"
                                                        title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                @if($location->assets_count == 0)
                                                    <button type="button" class="btn btn-outline-danger"
                                                            onclick="deleteLocation({{ $location->location_id }}, '{{ $location->name }}')"
                                                            title="Hapus">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="fas fa-map-marker-alt fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Belum ada lokasi</p>
                                            <button type="button" class="btn btn-sm btn-primary mt-2" 
                                                    data-bs-toggle="modal" data-bs-target="#addLocationModal">
                                                Tambah Lokasi Pertama
                                            </button>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($tab === 'locations' && $data['locations'] instanceof \Illuminate\Contracts\Pagination\Paginator)
                        <div class="d-flex justify-content-center mt-3">
                            {{ $data['locations']->appends([
                                'tab' => 'locations',
                                'type' => $data['type'] ?? null,
                                'search' => request('search')
                            ])->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Categories Tab -->
        <div class="tab-pane fade {{ $tab == 'categories' ? 'show active' : '' }}" id="categories">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tags me-2"></i> Daftar Kategori Aset
                    </h6>
                    <div>
                        <button class="btn btn-sm btn-outline-primary me-2" 
                               onclick="loadCategoryStats()">
                            <i class="fas fa-chart-bar me-1"></i> Statistik
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Kode KIB</th>
                                    <th>Nama Kategori</th>
                                    <th>Kode Standar</th>
                                    <th>Sub Kategori</th>
                                    <th>Jumlah Aset</th>
                                    <th>Total Nilai</th>
                                    <th>Deskripsi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($data['categories'] ?? collect()) as $category)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">
                                                KIB {{ $category->kib_code }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>{{ $category->name }}</strong>
                                        </td>
                                        <td>
                                            <code>{{ $category->standard_code_ref }}</code>
                                        </td>
                                        <td>
                                            @if($category->sub_categories && count($category->sub_categories) > 0)
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-info dropdown-toggle" 
                                                            type="button" data-bs-toggle="dropdown">
                                                        {{ count($category->sub_categories) }} sub
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        @foreach($category->sub_categories as $code => $name)
                                                            <li><span class="dropdown-item-text">{{ $code }}: {{ $name }}</span></li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $category->assets_count ?? 0 }}
                                            </span>
                                        </td>
                                        <td>
                                            @if(($category->assets_sum_value ?? 0) > 0)
                                                <span class="fw-bold text-success">Rp {{ number_format($category->assets_sum_value, 0, ',', '.') }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($category->description)
                                                <small>{{ Str::limit($category->description, 50) }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-tags fa-2x text-muted mb-2"></i>
                                            <p class="text-muted mb-0">Belum ada kategori</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @php
                        $categories = $data['categories'] ?? null;
                    @endphp

                    @if($categories instanceof \Illuminate\Contracts\Pagination\Paginator)
                        @if($categories->hasPages())
                            <div class="d-flex justify-content-center mt-3">
                                {{ $categories->appends(['tab' => 'categories'])->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            </div>

            <!-- KIB Information -->
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle me-2"></i> Informasi KIB (Kartu Inventaris Barang)
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-2 mb-3">
                            <span class="badge bg-primary mb-2 p-2 d-block">KIB A</span>
                            <p class="mb-0"><small>Tanah</small></p>
                        </div>
                        <div class="col-md-2 mb-3">
                            <span class="badge bg-primary mb-2 p-2 d-block">KIB B</span>
                            <p class="mb-0"><small>Peralatan dan Mesin</small></p>
                        </div>
                        <div class="col-md-2 mb-3">
                            <span class="badge bg-primary mb-2 p-2 d-block">KIB C</span>
                            <p class="mb-0"><small>Gedung dan Bangunan</small></p>
                        </div>
                        <div class="col-md-2 mb-3">
                            <span class="badge bg-primary mb-2 p-2 d-block">KIB D</span>
                            <p class="mb-0"><small>Jalan, Irigasi, dan Jaringan</small></p>
                        </div>
                        <div class="col-md-2 mb-3">
                            <span class="badge bg-primary mb-2 p-2 d-block">KIB E</span>
                            <p class="mb-0"><small>Aset Tetap Lainnya</small></p>
                        </div>
                        <div class="col-md-2 mb-3">
                            <span class="badge bg-primary mb-2 p-2 d-block">KIB F</span>
                            <p class="mb-0"><small>Konstruksi Dalam Pengerjaan</small></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Tab -->
        <div class="tab-pane fade {{ $tab == 'map' ? 'show active' : '' }}" id="map">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-map me-2"></i> Peta Lokasi Aset
                    </h6>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="refreshMap()">
                            <i class="fas fa-sync-alt me-1"></i> Refresh
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="zoomToAll()">
                            <i class="fas fa-search me-1"></i> Zoom All
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="locationMap" style="height: 500px; border-radius: 8px;"></div>
                    <div class="mt-3">
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            Klik pada marker di peta untuk melihat detail lokasi dan aset yang berada di lokasi tersebut.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Locations without coordinates -->
            @php
                $locations = $data['locations'] ?? collect();
                $locationsWithoutCoords = $locations->filter(function($location) {
                    return !$location->latitude || !$location->longitude;
                });
            @endphp

            @if($tab === 'map' && $locationsWithoutCoords->count() > 0)
                <div class="card shadow mt-4">
                    <div class="card-header py-3 bg-warning text-dark">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-exclamation-triangle me-2"></i> Lokasi Tanpa Koordinat
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($locationsWithoutCoords as $location)
                                <div class="col-md-4 mb-3">
                                    <div class="card border-left-warning shadow-sm h-100">
                                        <div class="card-body py-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong>{{ $location->name }}</strong>
                                                    <small class="d-block text-muted">{{ $location->type }}</small>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary"
                                                        onclick="editLocation({{ $location->location_id }})">
                                                    <i class="fas fa-map-marker-alt me-1"></i> Tambah Koordinat
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Statistics Tab -->
        <div class="tab-pane fade {{ $tab == 'statistics' ? 'show active' : '' }}" id="statistics">
            <div class="row">
                <!-- Location Statistics -->
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-map-marker-alt me-2"></i> Statistik Lokasi
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-4 mb-3">
                                    <div class="stat-card-sm">
                                        <div class="stat-value">{{ $data['locationStats']['total_locations'] ?? 0 }}</div>
                                        <div class="stat-label">Total Lokasi</div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="stat-card-sm">
                                        <div class="stat-value">{{ $data['locationStats']['locations_with_assets'] ?? 0 }}</div>
                                        <div class="stat-label">Lokasi Berisi Aset</div>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <div class="stat-card-sm">
                                        <div class="stat-value">{{ $data['locationStats']['total_assets'] ?? 0 }}</div>
                                        <div class="stat-label">Total Aset</div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Location Type Distribution -->
                            <h6 class="mt-4 mb-3">Distribusi Jenis Lokasi</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Jenis</th>
                                            <th>Jumlah</th>
                                            <th>Persentase</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $typeDist = $data['locationStats']['type_distribution'] ?? collect();
                                            $totalTypes = $typeDist instanceof \Illuminate\Support\Collection ? $typeDist->sum() : array_sum($typeDist);
                                        @endphp
                                        @foreach(($data['locationStats']['type_distribution'] ?? []) as $type => $count)
                                            @php
                                                $typeNames = [
                                                    'gedung' => 'Gedung',
                                                    'ruangan' => 'Ruangan',
                                                    'gudang' => 'Gudang',
                                                    'lapangan' => 'Lapangan',
                                                    'lainnya' => 'Lainnya'
                                                ];
                                                $percentage = $totalTypes > 0 ? round(($count / $totalTypes) * 100, 1) : 0;
                                            @endphp
                                            <tr>
                                                <td>{{ $typeNames[$type] ?? $type }}</td>
                                                <td>{{ $count }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="progress flex-grow-1 me-2" style="height: 8px;">
                                                            <div class="progress-bar" role="progressbar" 
                                                                 style="width: {{ $percentage }}%"
                                                                 aria-valuenow="{{ $percentage }}" 
                                                                 aria-valuemin="0" 
                                                                 aria-valuemax="100"></div>
                                                        </div>
                                                        <span style="width: 50px;">{{ $percentage }}%</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Top Locations -->
                            @if(!empty($data['locationStats']['top_locations']))
                                <h6 class="mt-4 mb-3">Lokasi dengan Aset Terbanyak</h6>
                                <div class="list-group">
                                    @foreach($data['locationStats']['top_locations'] as $topLocation)
                                        <a href="javascript:void(0)" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center"
                                           onclick="viewLocation({{ $topLocation['id'] }})">
                                            <div>
                                                <strong>{{ $topLocation['name'] }}</strong>
                                                <small class="d-block text-muted">{{ $topLocation['type'] }}</small>
                                            </div>
                                            <span class="badge bg-primary rounded-pill">{{ $topLocation['asset_count'] }} aset</span>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Category Statistics -->
                <div class="col-md-6">
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">
                                <i class="fas fa-tags me-2"></i> Statistik Kategori
                            </h6>
                        </div>
                        <div class="card-body">
                            <canvas id="categoryChart" height="250"></canvas>
                            
                            <!-- Category Details -->
                            <h6 class="mt-4 mb-3">Detail per Kategori</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>KIB</th>
                                            <th>Kategori</th>
                                            <th>Jumlah Aset</th>
                                            <th>Total Nilai</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach(($data['categoryStats'] ?? []) as $stat)
                                            <tr>
                                                <td><span class="badge bg-primary">KIB {{ $stat['kib_code'] }}</span></td>
                                                <td>{{ $stat['category_name'] }}</td>
                                                <td>{{ $stat['asset_count'] }}</td>
                                                <td>
                                                    <strong>Rp {{ number_format($stat['total_value'], 0, ',', '.') }}</strong>
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
        </div>
    </div>

    <!-- Add Location Modal -->
    <div class="modal fade" id="addLocationModal" tabindex="-1" aria-labelledby="addLocationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addLocationModalLabel">
                        <i class="fas fa-map-marker-alt me-2"></i> Tambah Lokasi Baru
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="addLocationForm" method="POST" action="{{ route('opd.master.locationStore') }}">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" required
                                           placeholder="Contoh: Gedung A, Ruang 101, Gudang Pusat">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="type" class="form-label">Jenis Lokasi <span class="text-danger">*</span></label>
                                    <select class="form-select" id="type" name="type" required>
                                        <option value="">Pilih Jenis...</option>
                                        @foreach($locationTypes as $type)
                                            @php
                                                $typeNames = [
                                                    'gedung' => 'Gedung',
                                                    'ruangan' => 'Ruangan',
                                                    'gudang' => 'Gudang',
                                                    'lapangan' => 'Lapangan',
                                                    'lainnya' => 'Lainnya'
                                                ];
                                            @endphp
                                            <option value="{{ $type }}">{{ $typeNames[$type] ?? $type }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="address" class="form-label">Alamat</label>
                                    <textarea class="form-control" id="address" name="address" rows="2"
                                              placeholder="Alamat lengkap lokasi..."></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="latitude" class="form-label">Latitude</label>
                                    <input type="number" class="form-control" id="latitude" name="latitude" 
                                           step="any" min="-90" max="90"
                                           placeholder="-7.250445">
                                    <small class="text-muted">Contoh: -7.250445</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="longitude" class="form-label">Longitude</label>
                                    <input type="number" class="form-control" id="longitude" name="longitude" 
                                           step="any" min="-180" max="180"
                                           placeholder="112.768845">
                                    <small class="text-muted">Contoh: 112.768845</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="getCurrentLocation" 
                                               onclick="getCurrentLocation()">
                                        <label class="form-check-label" for="getCurrentLocation">
                                            <i class="fas fa-location-crosshairs me-1"></i> Gunakan lokasi saat ini
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Location Modal -->
    <div class="modal fade" id="editLocationModal" tabindex="-1" aria-labelledby="editLocationModalLabel" aria-hidden="true">
        <!-- Will be loaded via AJAX -->
    </div>

    <!-- View Location Modal -->
    <div class="modal fade" id="viewLocationModal" tabindex="-1" aria-labelledby="viewLocationModalLabel" aria-hidden="true">
        <!-- Will be loaded via AJAX -->
    </div>
</div>
@endsection

@push('styles')
<style>
    .stat-card-sm {
        text-align: center;
        padding: 15px;
        border-radius: 8px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
    }
    .stat-card-sm .stat-value {
        font-size: 24px;
        font-weight: bold;
        color: #3498db;
    }
    .stat-card-sm .stat-label {
        font-size: 12px;
        color: #6c757d;
        text-transform: uppercase;
        margin-top: 5px;
    }
    
    /* Map Marker Styles */
    .custom-marker {
        position: relative;
    }
    .marker-icon {
        width: 30px;
        height: 30px;
        border-radius: 50% 50% 50% 0;
        background: #3498db;
        position: absolute;
        transform: rotate(-45deg);
        left: 50%;
        top: 50%;
        margin: -15px 0 0 -15px;
    }
    .marker-icon:after {
        content: "";
        width: 24px;
        height: 24px;
        margin: 3px 0 0 3px;
        background: #fff;
        position: absolute;
        border-radius: 50%;
    }
    .marker-icon i {
        position: absolute;
        width: 30px;
        font-size: 14px;
        left: 0;
        top: 7px;
        text-align: center;
        color: #3498db;
        transform: rotate(45deg);
        z-index: 1;
    }
    .marker-badge {
        position: absolute;
        background: #e74c3c;
        color: white;
        font-size: 10px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        top: -5px;
        right: -5px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 2;
    }
    
    .map-popup {
        min-width: 250px;
    }
    .map-popup h6 {
        margin-bottom: 8px;
    }
    .map-popup p {
        margin-bottom: 5px;
    }
    
    /* Tab styles */
    .nav-tabs .nav-link {
        color: #495057;
        font-weight: 500;
    }
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        border-bottom-color: #0d6efd;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Global variables
    let locationMap = null;
    let markers = [];
    let categoryChart = null;

    $(document).ready(function() {
        // Initialize map if on map tab
        if ('{{ $tab }}' === 'map') {
            initMap();
        }
        
        // Initialize category chart if on statistics tab
        if ('{{ $tab }}' === 'statistics') {
            renderCategoryChart(@json($data['categoryStats'] ?? []));
        }
        
        // Form submission handling
        $('#addLocationForm').on('submit', function(e) {
            e.preventDefault();
            submitLocationForm(this, 'add');
        });
        
        // Tab change event
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            if (e.target.id === 'map-tab' && !locationMap) {
                initMap();
            }
            if (e.target.id === 'statistics-tab' && !categoryChart) {
                renderCategoryChart(@json($data['categoryStats'] ?? []));
            }
        });
    });
    
    // Map Functions
    function initMap() {
        // Initialize map
        locationMap = L.map('locationMap').setView([-7.250445, 112.768845], 13);
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(locationMap);
        
        // Add markers for locations with coordinates
        @php
            $mapLocations = $data['locations'] ?? collect();
        @endphp

        @foreach($mapLocations as $location)
            @if($location->latitude && $location->longitude)
                addMarkerToMap(
                    {{ $location->location_id }},
                    '{{ $location->name }}',
                    {{ $location->latitude }},
                    {{ $location->longitude }},
                    '{{ $location->type }}',
                    {{ $location->assets_count ?? 0 }}
                );
            @endif
        @endforeach
        
        // Fit bounds to show all markers
        if (markers.length > 0) {
            const group = L.featureGroup(markers);
            locationMap.fitBounds(group.getBounds().pad(0.1));
        }
    }
    
    function addMarkerToMap(id, name, lat, lng, type, assetCount) {
        // Determine marker color based on type
        const markerColors = {
            'gedung': 'primary',
            'ruangan': 'info',
            'gudang': 'warning',
            'lapangan': 'success',
            'lainnya': 'secondary'
        };
        
        const colorClass = markerColors[type] || 'primary';
        const colorCode = {
            'primary': '#3498db',
            'info': '#17a2b8',
            'warning': '#f39c12',
            'success': '#28a745',
            'secondary': '#6c757d'
        }[colorClass];
        
        // Create icon
        const icon = L.divIcon({
            className: 'custom-marker',
            html: `<div class="marker-icon" style="background: ${colorCode};">
                     <i class="fas fa-map-marker-alt"></i>
                     ${assetCount > 0 ? `<span class="marker-badge">${assetCount}</span>` : ''}
                   </div>`,
            iconSize: [30, 30],
            iconAnchor: [15, 30],
            popupAnchor: [0, -30]
        });
        
        // Create marker
        const marker = L.marker([lat, lng], { icon: icon })
            .addTo(locationMap)
            .bindPopup(`
                <div class="map-popup">
                    <h6 class="mb-2">${name}</h6>
                    <p class="mb-1"><small><strong>Jenis:</strong> ${type}</small></p>
                    <p class="mb-1"><small><strong>Jumlah Aset:</strong> ${assetCount}</small></p>
                    <div class="mt-2">
                        <button class="btn btn-sm btn-primary w-100" onclick="viewLocation(${id})">
                            <i class="fas fa-eye me-1"></i> Lihat Detail
                        </button>
                    </div>
                </div>
            `);
        
        markers.push(marker);
        return marker;
    }
    
    function refreshMap() {
        if (locationMap) {
            locationMap.remove();
            markers = [];
        }
        initMap();
        showToast('success', 'Peta berhasil diperbarui');
    }
    
    function zoomToAll() {
        if (markers.length > 0) {
            const group = L.featureGroup(markers);
            locationMap.fitBounds(group.getBounds().pad(0.1));
        } else {
            showToast('info', 'Tidak ada marker untuk di-zoom');
        }
    }
    
    // Location Functions
    function viewLocation(id) {
        $.ajax({
            url: "{{ route('opd.master.getLocation', ['location' => '__ID__']) }}".replace('__ID__', id),
            method: 'GET',
            beforeSend: function() {
                $('#viewLocationModal').html(`
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Loading...</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body text-center py-5">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `);
                new bootstrap.Modal(document.getElementById('viewLocationModal')).show();
            },
            success: function(response) {
                if (response.success) {
                    const location = response.location;
                    const assets = response.assets || [];
                    
                    let assetsHtml = '';
                    if (assets.length > 0) {
                        assets.forEach(asset => {
                            assetsHtml += `
                                <tr>
                                    <td>${asset.asset_code}</td>
                                    <td>${asset.name}</td>
                                    <td>${asset.category?.name || '-'}</td>
                                    <td>Rp ${new Intl.NumberFormat('id-ID').format(asset.value || 0)}</td>
                                    <td><span class="badge bg-${asset.status === 'aktif' ? 'success' : 'warning'}">${asset.status}</span></td>
                                </tr>
                            `;
                        });
                    } else {
                        assetsHtml = `
                            <tr>
                                <td colspan="5" class="text-center py-3">
                                    <i class="fas fa-box-open fa-2x text-muted mb-2"></i>
                                    <p class="text-muted mb-0">Tidak ada aset di lokasi ini</p>
                                </td>
                            </tr>
                        `;
                    }
                    
                    const modalHtml = `
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Detail Lokasi: ${location.name}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h6 class="card-title">Informasi Lokasi</h6>
                                                    <table class="table table-sm">
                                                        <tr>
                                                            <th width="40%">Nama:</th>
                                                            <td>${location.name}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Jenis:</th>
                                                            <td><span class="badge bg-primary">${location.type}</span></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Alamat:</th>
                                                            <td>${location.address || '-'}</td>
                                                        </tr>
                                                        <tr>
                                                            <th>Koordinat:</th>
                                                            <td>
                                                                ${location.latitude && location.longitude 
                                                                    ? `${location.latitude}, ${location.longitude}` 
                                                                    : 'Belum diatur'}
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <th>Jumlah Aset:</th>
                                                            <td><span class="badge bg-info">${location.assets_count} aset</span></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <h6 class="card-title">Quick Actions</h6>
                                                    <div class="d-grid gap-2">
                                                        <button class="btn btn-primary" onclick="editLocation(${location.location_id})">
                                                            <i class="fas fa-edit me-1"></i> Edit Lokasi
                                                        </button>
                                                        <button class="btn btn-outline-primary" onclick="moveAssetsToLocation(${location.location_id})">
                                                            <i class="fas fa-exchange-alt me-1"></i> Pindahkan Aset ke Sini
                                                        </button>
                                                        ${location.assets_count === 0 ? `
                                                            <button class="btn btn-outline-danger" onclick="deleteLocation(${location.location_id}, '${location.name}')">
                                                                <i class="fas fa-trash me-1"></i> Hapus Lokasi
                                                            </button>
                                                        ` : ''}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <h6 class="mb-3">Aset di Lokasi Ini</h6>
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Kode Aset</th>
                                                    <th>Nama</th>
                                                    <th>Kategori</th>
                                                    <th>Nilai</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                ${assetsHtml}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            </div>
                        </div>
                    `;
                    
                    $('#viewLocationModal').html(modalHtml);
                    new bootstrap.Modal(document.getElementById('viewLocationModal')).show();
                } else {
                    showToast('error', response.message || 'Gagal memuat detail lokasi');
                }
            },
            error: function(xhr) {
                showToast('error', 'Terjadi kesalahan saat memuat detail lokasi');
            }
        });
    }
    
    function editLocation(id) {
        // First, get location data
        $.ajax({
            url: "{{ route('opd.master.getLocation', ['location' => '__ID__']) }}".replace('__ID__', id),
            method: 'GET',
            success: function(response) {
                if (response.success) {
                    const location = response.location;
                    
                    const modalHtml = `
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Lokasi: ${location.name}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form id="editLocationForm" method="POST" action="{{ route('opd.master.locationUpdate', ['location' => '__ID__']) }}".replace('__ID__', location.location_id)>
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="edit_name" class="form-label">Nama Lokasi <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="edit_name" name="name" 
                                                           value="${location.name}" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="edit_type" class="form-label">Jenis Lokasi <span class="text-danger">*</span></label>
                                                    <select class="form-select" id="edit_type" name="type" required>
                                                        @foreach($locationTypes as $type)
                                                            @php
                                                                $typeNames = [
                                                                    'gedung' => 'Gedung',
                                                                    'ruangan' => 'Ruangan',
                                                                    'gudang' => 'Gudang',
                                                                    'lapangan' => 'Lapangan',
                                                                    'lainnya' => 'Lainnya'
                                                                ];
                                                            @endphp
                                                            <option value="{{ $type }}" ${location.type === '{{ $type }}' ? 'selected' : ''}>
                                                                {{ $typeNames[$type] ?? $type }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="edit_address" class="form-label">Alamat</label>
                                                    <textarea class="form-control" id="edit_address" name="address" rows="2">${location.address || ''}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="edit_latitude" class="form-label">Latitude</label>
                                                    <input type="number" class="form-control" id="edit_latitude" name="latitude" 
                                                           step="any" min="-90" max="90" value="${location.latitude || ''}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label for="edit_longitude" class="form-label">Longitude</label>
                                                    <input type="number" class="form-control" id="edit_longitude" name="longitude" 
                                                           step="any" min="-180" max="180" value="${location.longitude || ''}">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div id="editLocationMap" style="height: 300px; border-radius: 8px; margin-top: 10px;"></div>
                                                <small class="text-muted">Klik pada peta untuk mengatur koordinat</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    `;
                    
                    $('#editLocationModal').html(modalHtml);
                    const modal = new bootstrap.Modal(document.getElementById('editLocationModal'));
                    modal.show();
                    
                    // Initialize edit map
                    initEditMap(location.latitude, location.longitude);
                    
                    // Bind form submission
                    $('#editLocationForm').on('submit', function(e) {
                        e.preventDefault();
                        submitLocationForm(this, 'edit');
                    });
                } else {
                    showToast('error', response.message || 'Gagal memuat data lokasi');
                }
            },
            error: function() {
                showToast('error', 'Terjadi kesalahan saat memuat form edit');
            }
        });
    }
    
    function initEditMap(lat, lng) {
        const defaultLat = lat || -7.250445;
        const defaultLng = lng || 112.768845;
        const zoomLevel = lat && lng ? 16 : 13;
        
        const editMap = L.map('editLocationMap').setView([defaultLat, defaultLng], zoomLevel);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(editMap);
        
        let marker = null;
        
        if (lat && lng) {
            marker = L.marker([lat, lng]).addTo(editMap);
        }
        
        // Add click event to set coordinates
        editMap.on('click', function(e) {
            const clickedLat = e.latlng.lat;
            const clickedLng = e.latlng.lng;
            
            $('#edit_latitude').val(clickedLat.toFixed(6));
            $('#edit_longitude').val(clickedLng.toFixed(6));
            
            if (marker) {
                editMap.removeLayer(marker);
            }
            
            marker = L.marker([clickedLat, clickedLng]).addTo(editMap);
        });
    }
    
    function deleteLocation(id, name) {
        Swal.fire({
            title: 'Hapus Lokasi?',
            html: `Anda yakin ingin menghapus lokasi <strong>"${name}"</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("opd.master.locationDestroy", ["location" => "__ID__"]) }}'.replace('__ID__', id),
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            showToast('success', response.message);
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            showToast('error', response.message || 'Gagal menghapus lokasi');
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'Terjadi kesalahan saat menghapus lokasi';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showToast('error', errorMessage);
                    }
                });
            }
        });
    }
    
    function submitLocationForm(form, type) {
        const submitBtn = $(form).find('button[type="submit"]');
        const originalText = submitBtn.html();
        
        submitBtn.prop('disabled', true).html(`
            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
            Menyimpan...
        `);
        
        $.ajax({
            url: $(form).attr('action'),
            method: $(form).attr('method'),
            data: $(form).serialize(),
            success: function(response) {
                if (response.success) {
                    showToast('success', response.message);
                    
                    // Close modal
                    if (type === 'add') {
                        $('#addLocationModal').modal('hide');
                    } else {
                        $('#editLocationModal').modal('hide');
                    }
                    
                    // Reload page after delay
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast('error', response.message || 'Gagal menyimpan lokasi');
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function(xhr) {
                let errorMessage = 'Terjadi kesalahan saat menyimpan lokasi';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const errors = xhr.responseJSON.errors;
                    errorMessage = Object.values(errors)[0][0];
                }
                
                showToast('error', errorMessage);
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    }
    
    function moveAssetsToLocation(locationId) {
        // This function would open a modal to select and move assets
        // For now, show a message
        Swal.fire({
            title: 'Pindahkan Aset',
            text: 'Fitur untuk memindahkan aset akan segera tersedia',
            icon: 'info',
            confirmButtonText: 'OK'
        });
    }
    
    // Geolocation Function
    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    $('#latitude').val(position.coords.latitude.toFixed(6));
                    $('#longitude').val(position.coords.longitude.toFixed(6));
                    showToast('success', 'Lokasi berhasil diperoleh');
                },
                function(error) {
                    let errorMessage = 'Gagal mendapatkan lokasi: ';
                    switch(error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage += 'Akses lokasi ditolak';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage += 'Informasi lokasi tidak tersedia';
                            break;
                        case error.TIMEOUT:
                            errorMessage += 'Waktu permintaan habis';
                            break;
                        default:
                            errorMessage += 'Error tidak diketahui';
                    }
                    showToast('error', errorMessage);
                }
            );
        } else {
            showToast('error', 'Browser tidak mendukung geolocation');
        }
    }
    
    // Chart Functions
    function renderCategoryChart(categoryStats) {
        const ctx = document.getElementById('categoryChart').getContext('2d');
        
        if (categoryChart) {
            categoryChart.destroy();
        }
        
        if (!categoryStats || categoryStats.length === 0) {
            $('#categoryChart').parent().html(`
                <div class="text-center py-4">
                    <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">Tidak ada data kategori</p>
                </div>
            `);
            return;
        }
        
        const labels = categoryStats.map(stat => stat.category_name);
        const dataValues = categoryStats.map(stat => stat.asset_count);
        const backgroundColors = [
            '#3498db', '#2ecc71', '#e74c3c', '#f39c12', 
            '#9b59b6', '#1abc9c', '#d35400', '#34495e'
        ];
        
        categoryChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Aset',
                    data: dataValues,
                    backgroundColor: backgroundColors,
                    borderColor: backgroundColors.map(color => color.replace('0.8', '1')),
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `Jumlah Aset: ${context.parsed.y}`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
    
    // Utility Functions
    function showToast(type, message) {
        const toast = $(`
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `);
        
        $('#toastContainer').append(toast);
        const bsToast = new bootstrap.Toast(toast[0]);
        bsToast.show();
        
        toast.on('hidden.bs.toast', function() {
            $(this).remove();
        });
    }
    
    // Tab persistence
    function setActiveTab(tab) {
        localStorage.setItem('master_active_tab', tab);
    }
    
    function getActiveTab() {
        return localStorage.getItem('master_active_tab') || 'locations';
    }
    
    // Load tab from localStorage on page load
    const savedTab = getActiveTab();
    if (savedTab && savedTab !== '{{ $tab }}') {
        window.location.href = '{{ route("opd.master.index") }}?tab=' + savedTab;
    }
    
    // Save tab on click
    $('button[data-bs-toggle="tab"]').on('click', function() {
        const tab = $(this).attr('id').replace('-tab', '');
        setActiveTab(tab);
    });
</script>

<!-- Toast Container -->
<div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>

<style>
    .toast {
        z-index: 1060;
    }
    
    .leaflet-popup-content {
        margin: 13px 19px;
    }
    
    .leaflet-popup-content-wrapper {
        border-radius: 8px;
    }
</style>
@endpush