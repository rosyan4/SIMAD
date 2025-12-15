@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Asset Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="mb-1">{{ $asset->name }}</h4>
                        <div class="d-flex align-items-center">
                            <code class="me-3">{{ $asset->asset_code }}</code>
                            @if($asset->asset_code_old)
                            <small class="text-muted">Kode lama: {{ $asset->asset_code_old }}</small>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex">
                        @if($asset->document_verification_status == 'belum_diverifikasi' && $asset->validation_status == 'belum_divalidasi')
                        <a href="{{ route('opd.assets.edit', $asset) }}" class="btn btn-warning me-2">
                            <i class="fas fa-edit me-1"></i> Edit
                        </a>
                        @endif
                        <div class="dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog me-1"></i> Aksi
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-print me-2"></i> Cetak Barcode
                                </a>
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-file-pdf me-2"></i> Export PDF
                                </a>
                                <div class="dropdown-divider"></div>
                                @if($asset->document_verification_status == 'belum_diverifikasi' && $asset->validation_status == 'belum_divalidasi')
                                <a class="dropdown-item text-danger" href="#" 
                                   onclick="confirmDelete('{{ route('opd.assets.destroy', $asset) }}')">
                                    <i class="fas fa-trash me-2"></i> Hapus
                                </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-3">
                        <small class="text-muted d-block">Status</small>
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
                    <div class="col-md-3">
                        <small class="text-muted d-block">Kondisi</small>
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
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Verifikasi</small>
                        @if($asset->document_verification_status == 'valid')
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i> Terverifikasi
                            </span>
                        @else
                            <span class="badge bg-warning">
                                <i class="fas fa-clock me-1"></i> Belum
                            </span>
                        @endif
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Validasi</small>
                        @if($asset->validation_status == 'disetujui')
                            <span class="badge bg-success">
                                <i class="fas fa-check-double me-1"></i> Disetujui
                            </span>
                        @else
                            <span class="badge bg-warning">
                                <i class="fas fa-clock me-1"></i> Belum
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tab Navigation -->
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ $tab == 'overview' ? 'active' : '' }}" 
                   href="{{ route('opd.assets.show', ['asset' => $asset, 'tab' => 'overview']) }}">
                    <i class="fas fa-info-circle me-1"></i> Overview
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab == 'documents' ? 'active' : '' }}" 
                   href="{{ route('opd.assets.show', ['asset' => $asset, 'tab' => 'documents']) }}">
                    <i class="fas fa-file-alt me-1"></i> Dokumen
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab == 'maintenance' ? 'active' : '' }}" 
                   href="{{ route('opd.assets.show', ['asset' => $asset, 'tab' => 'maintenance']) }}">
                    <i class="fas fa-tools me-1"></i> Pemeliharaan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab == 'history' ? 'active' : '' }}" 
                   href="{{ route('opd.assets.show', ['asset' => $asset, 'tab' => 'history']) }}">
                    <i class="fas fa-history me-1"></i> Riwayat
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab == 'depreciation' ? 'active' : '' }}" 
                   href="{{ route('opd.assets.show', ['asset' => $asset, 'tab' => 'depreciation']) }}">
                    <i class="fas fa-chart-line me-1"></i> Penyusutan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab == 'mutations' ? 'active' : '' }}" 
                   href="{{ route('opd.assets.show', ['asset' => $asset, 'tab' => 'mutations']) }}">
                    <i class="fas fa-exchange-alt me-1"></i> Mutasi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab == 'deletions' ? 'active' : '' }}" 
                   href="{{ route('opd.assets.show', ['asset' => $asset, 'tab' => 'deletions']) }}">
                    <i class="fas fa-trash me-1"></i> Penghapusan
                </a>
            </li>
        </ul>
        
        <!-- Tab Content -->
        <div class="tab-content mt-4">
            @if($tab == 'overview')
                <!-- Overview Tab -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Informasi Detail</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <table class="table table-sm">
                                            <tr>
                                                <th width="40%">Kategori</th>
                                                <td>{{ $asset->category->kib_code ?? '-' }} - {{ $asset->category->name ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Sub Kategori</th>
                                                <td>{{ $asset->sub_category_code }}</td>
                                            </tr>
                                            <tr>
                                                <th>Tahun Perolehan</th>
                                                <td>{{ $asset->acquisition_year }}</td>
                                            </tr>
                                            <tr>
                                                <th>Nilai Aset</th>
                                                <td>Rp {{ number_format($asset->value, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <th>Merek</th>
                                                <td>{{ $asset->brand ?? '-' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-6">
                                        <table class="table table-sm">
                                            <tr>
                                                <th width="40%">Model/Tipe</th>
                                                <td>{{ $asset->model ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Nomor Seri</th>
                                                <td>{{ $asset->serial_number ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <th>Lokasi</th>
                                                <td>
                                                    {{ $asset->location->name ?? '-' }}
                                                    @if($asset->location && $asset->location->address)
                                                    <br><small class="text-muted">{{ $asset->location->address }}</small>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Dibuat Oleh</th>
                                                <td>
                                                    {{ $asset->creator->name ?? '-' }}
                                                    <br><small class="text-muted">{{ $asset->created_at->format('d/m/Y H:i') }}</small>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Terakhir Diupdate</th>
                                                <td>{{ $asset->updated_at->format('d/m/Y H:i') }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                                
                                @if($asset->description)
                                <div class="mt-3">
                                    <h6>Deskripsi</h6>
                                    <p class="mb-0">{{ $asset->description }}</p>
                                </div>
                                @endif
                                
                                @if($asset->kib_data && is_array($asset->kib_data))
                                <div class="mt-4">
                                    <h6>Data KIB {{ $asset->category->kib_code ?? '' }}</h6>
                                    <table class="table table-sm">
                                        @foreach($asset->kib_data as $key => $value)
                                        <tr>
                                            <th width="40%">{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                            <td>{{ $value }}</td>
                                        </tr>
                                        @endforeach
                                    </table>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <!-- Quick Actions -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0">Aksi Cepat</h6>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    @if($asset->status == 'aktif')
                                    <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#changeConditionModal">
                                        <i class="fas fa-wrench me-2"></i> Ubah Kondisi
                                    </button>
                                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#changeLocationModal">
                                        <i class="fas fa-map-marker-alt me-2"></i> Pindah Lokasi
                                    </button>
                                    @endif
                                    
                                    <a href="{{ route('opd.transactions.create', ['type' => 'maintenance', 'asset_id' => $asset->asset_id]) }}" 
                                       class="btn btn-outline-primary">
                                        <i class="fas fa-tools me-2"></i> Jadwalkan Perbaikan
                                    </a>
                                    
                                    @if($asset->status == 'aktif' && $asset->document_verification_status == 'valid' && $asset->validation_status == 'disetujui')
                                    <a href="{{ route('opd.transactions.create', ['type' => 'deletion', 'asset_id' => $asset->asset_id]) }}" 
                                       class="btn btn-outline-danger">
                                        <i class="fas fa-trash me-2"></i> Ajukan Penghapusan
                                    </a>
                                    <a href="{{ route('opd.transactions.create', ['type' => 'mutation', 'asset_id' => $asset->asset_id]) }}" 
                                       class="btn btn-outline-success">
                                        <i class="fas fa-exchange-alt me-2"></i> Ajukan Mutasi
                                    </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Status Badge -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Status Sistem</h6>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        Verifikasi Dokumen
                                        <span class="badge bg-{{ $asset->document_verification_status == 'valid' ? 'success' : 'warning' }}">
                                            {{ $asset->document_verification_status == 'valid' ? 'Valid' : 'Belum' }}
                                        </span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        Validasi Aset
                                        <span class="badge bg-{{ $asset->validation_status == 'disetujui' ? 'success' : 'warning' }}">
                                            {{ $asset->validation_status == 'disetujui' ? 'Disetujui' : 'Belum' }}
                                        </span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        Status Aset
                                        <span class="badge bg-{{ $asset->status == 'aktif' ? 'success' : ($asset->status == 'dalam_perbaikan' ? 'warning' : 'info') }}">
                                            {{ ucfirst(str_replace('_', ' ', $asset->status)) }}
                                        </span>
                                    </div>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        Kondisi Fisik
                                        <span class="badge bg-{{ $asset->condition == 'Baik' ? 'success' : ($asset->condition == 'Rusak Ringan' ? 'warning' : 'danger') }}">
                                            {{ $asset->condition }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            @elseif($tab == 'documents')
                <!-- Documents Tab -->
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Dokumen Aset</h6>
                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                                    <i class="fas fa-upload me-1"></i> Upload Dokumen
                                </button>
                            </div>
                            <div class="card-body">
                                @if($asset->documents->count() > 0)
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Jenis Dokumen</th>
                                                <th>Nama File</th>
                                                <th>Diupload Oleh</th>
                                                <th>Tanggal</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($asset->documents as $document)
                                            <tr>
                                                <td>
                                                    @php
                                                        $docTypes = [
                                                            'pengadaan' => 'Pengadaan',
                                                            'mutasi' => 'Mutasi',
                                                            'penghapusan' => 'Penghapusan',
                                                            'pemeliharaan' => 'Pemeliharaan',
                                                            'lainnya' => 'Lainnya'
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-info">{{ $docTypes[$document->document_type] ?? $document->document_type }}</span>
                                                </td>
                                                <td>
                                                    {{ basename($document->file_path) }}
                                                    <br>
                                                    <small class="text-muted">.{{ $document->file_type }} ({{ round(filesize(storage_path('app/public/' . $document->file_path)) / 1024) }} KB)</small>
                                                </td>
                                                <td>{{ $document->uploader->name ?? '-' }}</td>
                                                <td>{{ $document->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ Storage::url($document->file_path) }}" 
                                                           target="_blank" class="btn btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ Storage::url($document->file_path) }}" 
                                                           download class="btn btn-outline-success">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="deleteDocument({{ $document->document_id }})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-5">
                                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada dokumen untuk aset ini</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Statistik Dokumen</h6>
                            </div>
                            <div class="card-body">
                                <div class="list-group list-group-flush">
                                    <div class="list-group-item d-flex justify-content-between">
                                        <span>Total Dokumen</span>
                                        <strong>{{ $asset->documents->count() }}</strong>
                                    </div>
                                    @php
                                        $docCounts = $asset->documents->groupBy('document_type')->map->count();
                                    @endphp
                                    @foreach($docCounts as $type => $count)
                                    <div class="list-group-item d-flex justify-content-between">
                                        <span>{{ $additionalData['documentTypes'][$type] ?? ucfirst($type) }}</span>
                                        <span class="badge bg-primary">{{ $count }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            @elseif($tab == 'maintenance')
                <!-- Maintenance Tab -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Riwayat Pemeliharaan</h6>
                                <a href="{{ route('opd.transactions.create', ['type' => 'maintenance', 'asset_id' => $asset->asset_id]) }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus me-1"></i> Jadwalkan Baru
                                </a>
                            </div>
                            <div class="card-body">
                                @if($asset->maintenances->count() > 0)
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Jenis</th>
                                                <th>Judul</th>
                                                <th>Vendor</th>
                                                <th>Biaya</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($asset->maintenances->sortByDesc('scheduled_date') as $maintenance)
                                            <tr>
                                                <td>
                                                    <small class="d-block">{{ \Carbon\Carbon::parse($maintenance->scheduled_date)->format('d/m/Y') }}</small>
                                                    @if($maintenance->actual_date)
                                                    <small class="text-muted">Aktual: {{ \Carbon\Carbon::parse($maintenance->actual_date)->format('d/m/Y') }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $types = [
                                                            'rutin' => 'Rutin',
                                                            'perbaikan' => 'Perbaikan',
                                                            'kalibrasi' => 'Kalibrasi',
                                                            'penggantian' => 'Penggantian'
                                                        ];
                                                    @endphp
                                                    {{ $types[$maintenance->maintenance_type] ?? $maintenance->maintenance_type }}
                                                </td>
                                                <td>{{ $maintenance->title }}</td>
                                                <td>{{ $maintenance->vendor ?? '-' }}</td>
                                                <td>Rp {{ number_format($maintenance->cost, 0, ',', '.') }}</td>
                                                <td>
                                                    @php
                                                        $statusColors = [
                                                            'dijadwalkan' => 'warning',
                                                            'dalam_pengerjaan' => 'info',
                                                            'selesai' => 'success',
                                                            'dibatalkan' => 'secondary'
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $statusColors[$maintenance->status] ?? 'secondary' }}">
                                                        {{ ucfirst(str_replace('_', ' ', $maintenance->status)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('opd.transactions.show', ['maintenance', $maintenance->maintenance_id]) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-5">
                                    <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada riwayat pemeliharaan untuk aset ini</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
            @elseif($tab == 'history')
                <!-- History Tab -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Riwayat Perubahan</h6>
                            </div>
                            <div class="card-body">
                                @if($asset->histories->count() > 0)
                                <div class="timeline">
                                    @foreach($asset->histories->sortByDesc('change_date') as $history)
                                    <div class="timeline-item mb-4">
                                        <div class="timeline-marker bg-{{ $history->action == 'create' ? 'success' : ($history->action == 'update' ? 'warning' : 'danger') }}"></div>
                                        <div class="timeline-content">
                                            <div class="d-flex justify-content-between">
                                                <h6 class="mb-1">{{ ucfirst($history->action) }} - {{ $history->description }}</h6>
                                                <small class="text-muted">{{ $history->change_date->format('d/m/Y H:i') }}</small>
                                            </div>
                                            <p class="mb-1 text-muted">
                                                <i class="fas fa-user me-1"></i> {{ $history->changer->name ?? 'System' }}
                                            </p>
                                            
                                            @if($history->old_value || $history->new_value)
                                            <div class="row mt-2">
                                                @if($history->old_value)
                                                <div class="col-md-6">
                                                    <small class="d-block text-muted">Nilai Lama:</small>
                                                    <pre class="bg-light p-2 rounded" style="font-size: 0.8rem;">{{ json_encode($history->old_value, JSON_PRETTY_PRINT) }}</pre>
                                                </div>
                                                @endif
                                                @if($history->new_value)
                                                <div class="col-md-6">
                                                    <small class="d-block text-muted">Nilai Baru:</small>
                                                    <pre class="bg-light p-2 rounded" style="font-size: 0.8rem;">{{ json_encode($history->new_value, JSON_PRETTY_PRINT) }}</pre>
                                                </div>
                                                @endif
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="text-center py-5">
                                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada riwayat perubahan untuk aset ini</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <style>
                .timeline {
                    position: relative;
                    padding-left: 30px;
                }
                .timeline-item {
                    position: relative;
                }
                .timeline-marker {
                    position: absolute;
                    left: -30px;
                    top: 0;
                    width: 15px;
                    height: 15px;
                    border-radius: 50%;
                }
                .timeline-content {
                    border-left: 2px solid #dee2e6;
                    padding-left: 20px;
                    padding-bottom: 20px;
                }
                .timeline-item:last-child .timeline-content {
                    border-left: none;
                }
                </style>
                
            @elseif($tab == 'depreciation')
                <!-- Depreciation Tab -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Perhitungan Penyusutan</h6>
                            </div>
                            <div class="card-body">
                                @if($asset->depreciations->count() > 0)
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Tahun</th>
                                                <th>Nilai Awal</th>
                                                <th>Penyusutan</th>
                                                <th>Nilai Buku</th>
                                                <th>Akumulasi</th>
                                                <th>Metode</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($asset->depreciations as $depreciation)
                                            <tr>
                                                <td>{{ $depreciation->year }}</td>
                                                <td>Rp {{ number_format($depreciation->beginning_value, 0, ',', '.') }}</td>
                                                <td>Rp {{ number_format($depreciation->depreciation_value, 0, ',', '.') }}</td>
                                                <td>Rp {{ number_format($depreciation->book_value, 0, ',', '.') }}</td>
                                                <td>Rp {{ number_format($depreciation->accumulated_depreciation, 0, ',', '.') }}</td>
                                                <td>{{ $depreciation->method }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <h6>Ringkasan Penyusutan</h6>
                                                <table class="table table-sm">
                                                    <tr>
                                                        <td>Nilai Perolehan</td>
                                                        <td class="text-end">Rp {{ number_format($asset->value, 0, ',', '.') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Nilai Residu</td>
                                                        <td class="text-end">Rp {{ number_format($asset->value * 0.1, 0, ',', '.') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Masa Manfaat</td>
                                                        <td class="text-end">{{ $asset->useful_life ?? 5 }} tahun</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Umur Aset</td>
                                                        <td class="text-end">{{ date('Y') - $asset->acquisition_year }} tahun</td>
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <canvas id="depreciationChart" height="200"></canvas>
                                    </div>
                                </div>
                                @else
                                <div class="text-center py-5">
                                    <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada data penyusutan untuk aset ini</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                @if($asset->depreciations->count() > 0)
                @push('scripts')
                <script>
                $(document).ready(function() {
                    const ctx = document.getElementById('depreciationChart').getContext('2d');
                    const chart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: @json($asset->depreciations->pluck('year')),
                            datasets: [{
                                label: 'Nilai Buku',
                                data: @json($asset->depreciations->pluck('book_value')),
                                borderColor: '#3498db',
                                backgroundColor: 'rgba(52, 152, 219, 0.1)',
                                fill: true,
                                tension: 0.4
                            }, {
                                label: 'Akumulasi Penyusutan',
                                data: @json($asset->depreciations->pluck('accumulated_depreciation')),
                                borderColor: '#e74c3c',
                                borderDash: [5, 5]
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            return context.dataset.label + ': Rp ' + context.raw.toLocaleString('id-ID');
                                        }
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function(value) {
                                            return 'Rp ' + value.toLocaleString('id-ID');
                                        }
                                    }
                                }
                            }
                        }
                    });
                });
                </script>
                @endpush
                @endif
                
            @elseif($tab == 'mutations')
                <!-- Mutations Tab -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Riwayat Mutasi</h6>
                            </div>
                            <div class="card-body">
                                @if($asset->mutations->count() > 0)
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Dari</th>
                                                <th>Ke</th>
                                                <th>Status</th>
                                                <th>Catatan</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($asset->mutations as $mutation)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($mutation->mutation_date)->format('d/m/Y') }}</td>
                                                <td>
                                                    {{ $mutation->fromOpdUnit->nama_opd ?? '-' }}
                                                    <br>
                                                    <small class="text-muted">{{ $mutation->fromLocation->name ?? '-' }}</small>
                                                </td>
                                                <td>
                                                    {{ $mutation->toOpdUnit->nama_opd ?? '-' }}
                                                    <br>
                                                    <small class="text-muted">{{ $mutation->toLocation->name ?? '-' }}</small>
                                                </td>
                                                <td>
                                                    @php
                                                        $statusColors = [
                                                            'diusulkan' => 'warning',
                                                            'disetujui' => 'info',
                                                            'selesai' => 'success',
                                                            'ditolak' => 'danger',
                                                            'dibatalkan' => 'secondary'
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $statusColors[$mutation->status] ?? 'secondary' }}">
                                                        {{ ucfirst($mutation->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ Str::limit($mutation->notes, 50) }}</td>
                                                <td>
                                                    <a href="{{ route('opd.transactions.show', ['mutation', $mutation->mutation_id]) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-5">
                                    <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada riwayat mutasi untuk aset ini</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
            @elseif($tab == 'deletions')
                <!-- Deletions Tab -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Riwayat Penghapusan</h6>
                            </div>
                            <div class="card-body">
                                @if($asset->deletions->count() > 0)
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Tanggal Diajukan</th>
                                                <th>Alasan</th>
                                                <th>Status</th>
                                                <th>Diajukan Oleh</th>
                                                <th>Nilai Aset</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($asset->deletions as $deletion)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($deletion->proposed_at)->format('d/m/Y') }}</td>
                                                <td>
                                                    @php
                                                        $reasons = [
                                                            'rusak_berat' => 'Rusak Berat',
                                                            'hilang' => 'Hilang',
                                                            'jual' => 'Dijual',
                                                            'hibah' => 'Dihibahkan',
                                                            'musnah' => 'Musnah'
                                                        ];
                                                    @endphp
                                                    {{ $reasons[$deletion->deletion_reason] ?? $deletion->deletion_reason }}
                                                </td>
                                                <td>
                                                    @php
                                                        $statusColors = [
                                                            'diusulkan' => 'warning',
                                                            'diverifikasi' => 'info',
                                                            'disetujui' => 'primary',
                                                            'selesai' => 'success',
                                                            'ditolak' => 'danger',
                                                            'dibatalkan' => 'secondary'
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $statusColors[$deletion->status] ?? 'secondary' }}">
                                                        {{ ucfirst($deletion->status) }}
                                                    </span>
                                                </td>
                                                <td>{{ $deletion->proposer->name ?? '-' }}</td>
                                                <td>Rp {{ number_format($asset->value, 0, ',', '.') }}</td>
                                                <td>
                                                    <a href="{{ route('opd.transactions.show', ['deletion', $deletion->deletion_id]) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @else
                                <div class="text-center py-5">
                                    <i class="fas fa-trash fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada riwayat penghapusan untuk aset ini</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modals -->
@include('opd.assets.modals.condition')
@include('opd.assets.modals.location')
@include('opd.assets.modals.document')

@push('scripts')
<script>
function confirmDelete(url) {
    if (confirm('Apakah Anda yakin ingin menghapus aset ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteDocument(documentId) {
    if (confirm('Apakah Anda yakin ingin menghapus dokumen ini?')) {
        $.ajax({
            url: `/opd/assets/documents/${documentId}`,
            type: 'DELETE',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    location.reload();
                }
            }
        });
    }
}
</script>
@endpush
@endsection