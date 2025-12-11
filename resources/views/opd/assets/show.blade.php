@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
        <div>
            <a href="{{ route('opd.assets.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar
            </a>
            @if($asset->document_verification_status == 'belum_diverifikasi' && $asset->validation_status == 'belum_divalidasi')
                <a href="{{ route('opd.assets.edit', $asset) }}" class="btn btn-warning ms-2">
                    <i class="fas fa-edit"></i> Edit
                </a>
            @endif
            <button type="button" class="btn btn-primary ms-2" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    <!-- Asset Info Header -->
    <div class="row mb-4">
        <div class="col-xl-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Informasi Umum Aset
                    </h6>
                    <div>
                        <span class="badge bg-{{ $asset->status == 'aktif' ? 'success' : ($asset->status == 'dalam_perbaikan' ? 'warning' : 'secondary') }} fs-6">
                            {{ $asset->status }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Kode Aset</label>
                            <div class="fs-5 fw-bold">{{ $asset->asset_code }}</div>
                            @if($asset->asset_code_old)
                                <small class="text-muted">Kode lama: {{ $asset->asset_code_old }}</small>
                            @endif
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Nama Aset</label>
                            <div class="fs-5">{{ $asset->name }}</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Kategori</label>
                            <div>
                                <span class="badge bg-info">{{ $asset->category->kib_code }}</span>
                                {{ $asset->category->name }}
                            </div>
                            <div class="text-muted">{{ $asset->sub_category_name }}</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Lokasi</label>
                            <div class="fs-6">{{ $asset->location->name ?? '-' }}</div>
                            @if($asset->location && $asset->location->address)
                                <small class="text-muted">{{ $asset->location->address }}</small>
                            @endif
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Nilai Aset</label>
                            <div class="fs-4 fw-bold text-success">{{ $asset->formatted_value }}</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Tahun Perolehan</label>
                            <div class="fs-5">{{ $asset->acquisition_year }}</div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Kondisi</label>
                            <div>
                                <span class="badge bg-{{ $asset->condition == 'Baik' ? 'success' : ($asset->condition == 'Rusak Ringan' ? 'warning' : 'danger') }}">
                                    {{ $asset->condition }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Dibuat Oleh</label>
                            <div>{{ $asset->creator->name ?? '-' }}</div>
                            <small class="text-muted">{{ $asset->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Quick Actions and Status -->
        <div class="col-xl-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Administrasi</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Verifikasi Dokumen</label>
                        <div>
                            <span class="badge bg-{{ $asset->document_verification_status == 'valid' ? 'success' : ($asset->document_verification_status == 'belum_diverifikasi' ? 'warning' : 'danger') }}">
                                {{ $asset->document_verification_status }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">Validasi Aset</label>
                        <div>
                            <span class="badge bg-{{ $asset->validation_status == 'disetujui' ? 'success' : ($asset->validation_status == 'belum_divalidasi' ? 'warning' : 'danger') }}">
                                {{ $asset->validation_status }}
                            </span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <label class="form-label text-muted">OPD Pemilik</label>
                        <div>{{ $asset->opdUnit->nama_opd }}</div>
                        <small class="text-muted">Kode: {{ $asset->opdUnit->kode_opd }}</small>
                    </div>
                    
                    @if($asset->document_verification_status == 'valid' && $asset->validation_status == 'disetujui')
                        <div class="alert alert-success mt-3">
                            <i class="fas fa-check-circle"></i> Aset telah terverifikasi dan divalidasi
                        </div>
                    @else
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle"></i> Aset belum sepenuhnya terverifikasi
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Aksi Cepat</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($asset->document_verification_status == 'belum_diverifikasi' && $asset->validation_status == 'belum_divalidasi')
                            <button type="button" class="btn btn-outline-primary" onclick="updateAssetField('status')">
                                <i class="fas fa-exchange-alt"></i> Ubah Status
                            </button>
                            
                            <button type="button" class="btn btn-outline-warning" onclick="updateAssetField('condition')">
                                <i class="fas fa-tools"></i> Ubah Kondisi
                            </button>
                            
                            <button type="button" class="btn btn-outline-info" onclick="updateAssetField('location_id')">
                                <i class="fas fa-map-marker-alt"></i> Pindah Lokasi
                            </button>
                        @endif
                        
                        <a href="{{ route('opd.transactions.create', ['type' => 'maintenance', 'asset_id' => $asset->asset_id]) }}" 
                           class="btn btn-outline-success">
                            <i class="fas fa-wrench"></i> Ajukan Pemeliharaan
                        </a>
                        
                        <a href="{{ route('opd.transactions.create', ['type' => 'mutation', 'asset_id' => $asset->asset_id]) }}" 
                           class="btn btn-outline-secondary">
                            <i class="fas fa-exchange-alt"></i> Ajukan Mutasi
                        </a>
                        
                        @if($asset->document_verification_status == 'valid' && $asset->validation_status == 'disetujui')
                            <a href="{{ route('opd.transactions.create', ['type' => 'deletion', 'asset_id' => $asset->asset_id]) }}" 
                               class="btn btn-outline-danger">
                                <i class="fas fa-trash"></i> Ajukan Penghapusan
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs" id="assetTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'overview' ? 'active' : '' }}" id="overview-tab" data-bs-toggle="tab" 
                    data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">
                <i class="fas fa-info-circle"></i> Overview
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'documents' ? 'active' : '' }}" id="documents-tab" data-bs-toggle="tab" 
                    data-bs-target="#documents" type="button" role="tab" aria-controls="documents">
                <i class="fas fa-file-alt"></i> Dokumen
                @if($asset->documents->count() > 0)
                    <span class="badge bg-primary">{{ $asset->documents->count() }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'history' ? 'active' : '' }}" id="history-tab" data-bs-toggle="tab" 
                    data-bs-target="#history" type="button" role="tab" aria-controls="history">
                <i class="fas fa-history"></i> Riwayat
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="maintenance-tab" data-bs-toggle="tab" 
                    data-bs-target="#maintenance" type="button" role="tab" aria-controls="maintenance">
                <i class="fas fa-wrench"></i> Pemeliharaan
                @if($asset->maintenances->count() > 0)
                    <span class="badge bg-warning">{{ $asset->maintenances->count() }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="mutations-tab" data-bs-toggle="tab" 
                    data-bs-target="#mutations" type="button" role="tab" aria-controls="mutations">
                <i class="fas fa-exchange-alt"></i> Mutasi
                @if($asset->mutations->count() > 0)
                    <span class="badge bg-info">{{ $asset->mutations->count() }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="audits-tab" data-bs-toggle="tab" 
                    data-bs-target="#audits" type="button" role="tab" aria-controls="audits">
                <i class="fas fa-clipboard-check"></i> Audit
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="assetTabContent">
        <!-- Overview Tab -->
        <div class="tab-pane fade {{ $tab == 'overview' ? 'show active' : '' }}" id="overview" role="tabpanel" aria-labelledby="overview-tab">
            <div class="card shadow mt-4">
                <div class="card-body">
                    <!-- KIB Data -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary mb-3">Data KIB {{ $asset->category->kib_code }}</h5>
                            @if($asset->kib_data && count($asset->kib_data) > 0)
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tbody>
                                            @foreach($asset->kib_data as $key => $value)
                                                <tr>
                                                    <th width="30%">{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                                                    <td>{{ $value }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> Tidak ada data KIB spesifik
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Summary Statistics -->
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-primary shadow-sm h-100">
                                <div class="card-body">
                                    <div class="text-primary mb-2">
                                        <i class="fas fa-wrench fa-2x"></i>
                                    </div>
                                    <div class="h5 mb-0">{{ $asset->maintenances->count() }}</div>
                                    <div class="text-muted">Total Pemeliharaan</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-success shadow-sm h-100">
                                <div class="card-body">
                                    <div class="text-success mb-2">
                                        <i class="fas fa-exchange-alt fa-2x"></i>
                                    </div>
                                    <div class="h5 mb-0">{{ $asset->mutations->count() }}</div>
                                    <div class="text-muted">Total Mutasi</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card border-left-info shadow-sm h-100">
                                <div class="card-body">
                                    <div class="text-info mb-2">
                                        <i class="fas fa-file-alt fa-2x"></i>
                                    </div>
                                    <div class="h5 mb-0">{{ $asset->documents->count() }}</div>
                                    <div class="text-muted">Total Dokumen</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Documents Tab -->
        <div class="tab-pane fade {{ $tab == 'documents' ? 'show active' : '' }}" id="documents" role="tabpanel" aria-labelledby="documents-tab">
            <div class="card shadow mt-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Dokumen Aset</h6>
                    @if($asset->document_verification_status == 'belum_diverifikasi' && $asset->validation_status == 'belum_divalidasi')
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                            <i class="fas fa-upload"></i> Upload Dokumen
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    @if($asset->documents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Nama File</th>
                                        <th>Jenis</th>
                                        <th>Tipe File</th>
                                        <th>Status Verifikasi</th>
                                        <th>Diupload Oleh</th>
                                        <th>Tanggal</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($asset->documents as $document)
                                        <tr>
                                            <td>
                                                <i class="fas fa-file-{{ in_array($document->file_type, ['pdf']) ? 'pdf text-danger' : (in_array($document->file_type, ['jpg','jpeg','png']) ? 'image text-success' : 'word text-primary') }} me-2"></i>
                                                {{ basename($document->file_path) }}
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $document->document_type }}</span>
                                            </td>
                                            <td>{{ strtoupper($document->file_type) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $document->verified_status == 'valid' ? 'success' : ($document->verified_status == 'belum_diverifikasi' ? 'warning' : 'danger') }}">
                                                    {{ $document->verified_status }}
                                                </span>
                                            </td>
                                            <td>{{ $document->uploader->name ?? '-' }}</td>
                                            <td>{{ $document->uploaded_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="btn btn-info" title="Lihat">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ Storage::url($document->file_path) }}" download class="btn btn-success" title="Download">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                    @if($asset->document_verification_status == 'belum_diverifikasi' && $asset->validation_status == 'belum_divalidasi')
                                                        <button type="button" class="btn btn-danger" onclick="deleteDocument({{ $document->document_id }})" title="Hapus">
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
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada dokumen</h5>
                            <p class="text-muted">Upload dokumen pendukung untuk proses verifikasi</p>
                            @if($asset->document_verification_status == 'belum_diverifikasi' && $asset->validation_status == 'belum_divalidasi')
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                                    <i class="fas fa-upload"></i> Upload Dokumen Pertama
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- History Tab -->
        <div class="tab-pane fade {{ $tab == 'history' ? 'show active' : '' }}" id="history" role="tabpanel" aria-labelledby="history-tab">
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Perubahan</h6>
                </div>
                <div class="card-body">
                    @if($asset->histories->count() > 0)
                        <div class="timeline">
                            @foreach($asset->histories->sortByDesc('change_date') as $history)
                                <div class="timeline-item mb-4">
                                    <div class="timeline-marker bg-{{ $history->action == 'create' ? 'success' : ($history->action == 'update' ? 'primary' : 'danger') }}">
                                        <i class="fas fa-{{ $history->action == 'create' ? 'plus' : ($history->action == 'update' ? 'edit' : 'trash') }}"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="timeline-header d-flex justify-content-between">
                                            <h6 class="mb-0">{{ ucfirst($history->action) }}</h6>
                                            <small class="text-muted">{{ $history->change_date->format('d/m/Y H:i') }}</small>
                                        </div>
                                        <p class="mb-1">{{ $history->description }}</p>
                                        <small class="text-muted">
                                            <i class="fas fa-user"></i> {{ $history->changer->name ?? 'System' }}
                                        </small>
                                        
                                        @if($history->old_value || $history->new_value)
                                            <div class="mt-2">
                                                <button class="btn btn-sm btn-outline-info" type="button" data-bs-toggle="collapse" 
                                                        data-bs-target="#historyDetails{{ $history->history_id }}">
                                                    <i class="fas fa-eye"></i> Lihat Detail Perubahan
                                                </button>
                                                <div class="collapse mt-2" id="historyDetails{{ $history->history_id }}">
                                                    <div class="card card-body">
                                                        <div class="row">
                                                            @if($history->old_value)
                                                                <div class="col-md-6">
                                                                    <h6>Sebelum:</h6>
                                                                    <pre class="bg-light p-2">{{ json_encode($history->old_value, JSON_PRETTY_PRINT) }}</pre>
                                                                </div>
                                                            @endif
                                                            @if($history->new_value)
                                                                <div class="col-md-6">
                                                                    <h6>Sesudah:</h6>
                                                                    <pre class="bg-light p-2">{{ json_encode($history->new_value, JSON_PRETTY_PRINT) }}</pre>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-history fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada riwayat perubahan</h5>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Maintenance Tab -->
        <div class="tab-pane fade" id="maintenance" role="tabpanel" aria-labelledby="maintenance-tab">
            <div class="card shadow mt-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Pemeliharaan</h6>
                    <a href="{{ route('opd.transactions.create', ['type' => 'maintenance', 'asset_id' => $asset->asset_id]) }}" 
                       class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> Jadwalkan Pemeliharaan
                    </a>
                </div>
                <div class="card-body">
                    @if($asset->maintenances->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Judul</th>
                                        <th>Jenis</th>
                                        <th>Tanggal Jadwal</th>
                                        <th>Status</th>
                                        <th>Biaya</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($asset->maintenances as $maintenance)
                                        <tr>
                                            <td>{{ $maintenance->title }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $maintenance->maintenance_type }}</span>
                                            </td>
                                            <td>{{ $maintenance->scheduled_date->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $maintenance->status == 'selesai' ? 'success' : ($maintenance->status == 'dijadwalkan' ? 'warning' : 'secondary') }}">
                                                    {{ $maintenance->status }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($maintenance->cost)
                                                    Rp {{ number_format($maintenance->cost, 0, ',', '.') }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('opd.transactions.show', ['maintenance', $maintenance->maintenance_id]) }}" 
                                                   class="btn btn-sm btn-info">
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
                            <i class="fas fa-wrench fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada riwayat pemeliharaan</h5>
                            <p class="text-muted">Jadwalkan pemeliharaan untuk menjaga kondisi aset</p>
                            <a href="{{ route('opd.transactions.create', ['type' => 'maintenance', 'asset_id' => $asset->asset_id]) }}" 
                               class="btn btn-primary">
                                <i class="fas fa-plus"></i> Jadwalkan Pemeliharaan
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Mutations Tab -->
        <div class="tab-pane fade" id="mutations" role="tabpanel" aria-labelledby="mutations-tab">
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Mutasi</h6>
                </div>
                <div class="card-body">
                    @if($asset->mutations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal Mutasi</th>
                                        <th>Dari OPD</th>
                                        <th>Ke OPD</th>
                                        <th>Status</th>
                                        <th>Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($asset->mutations as $mutation)
                                        <tr>
                                            <td>{{ $mutation->mutation_date->format('d/m/Y') }}</td>
                                            <td>{{ $mutation->fromOpdUnit->nama_opd ?? '-' }}</td>
                                            <td>{{ $mutation->toOpdUnit->nama_opd ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $mutation->status == 'selesai' ? 'success' : ($mutation->status == 'disetujui' ? 'info' : 'warning') }}">
                                                    {{ $mutation->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('opd.transactions.show', ['mutation', $mutation->mutation_id]) }}" 
                                                   class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> Detail
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
                            <h5 class="text-muted">Belum ada riwayat mutasi</h5>
                            <p class="text-muted">Aset ini belum pernah dimutasi</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Audits Tab -->
        <div class="tab-pane fade" id="audits" role="tabpanel" aria-labelledby="audits-tab">
            <div class="card shadow mt-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Audit</h6>
                </div>
                <div class="card-body">
                    @if($asset->audits->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tanggal Audit</th>
                                        <th>Auditor</th>
                                        <th>Temuan</th>
                                        <th>Status</th>
                                        <th>Detail</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($asset->audits as $audit)
                                        <tr>
                                            <td>{{ $audit->audit_date->format('d/m/Y') }}</td>
                                            <td>{{ $audit->auditor->name ?? '-' }}</td>
                                            <td>
                                                @if($audit->findings)
                                                    {{ Str::limit($audit->findings, 50) }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $audit->status == 'selesai' ? 'success' : ($audit->status == 'dalam_proses' ? 'warning' : 'info') }}">
                                                    {{ $audit->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        onclick="showAuditDetails({{ $audit->audit_id }})">
                                                    <i class="fas fa-eye"></i> Detail
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Belum ada riwayat audit</h5>
                            <p class="text-muted">Aset ini belum pernah diaudit</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upload Document Modal -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1" aria-labelledby="uploadDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="uploadDocumentForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="uploadDocumentModalLabel">Upload Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="document_type" class="form-label">Jenis Dokumen</label>
                        <select class="form-control" id="document_type" name="document_type" required>
                            <option value="pengadaan">Dokumen Pengadaan</option>
                            <option value="mutasi">Dokumen Mutasi</option>
                            <option value="penghapusan">Dokumen Penghapusan</option>
                            <option value="pemeliharaan">Dokumen Pemeliharaan</option>
                            <option value="lainnya">Dokumen Lainnya</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="document" class="form-label">File Dokumen</label>
                        <input type="file" class="form-control" id="document" name="document" required
                               accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                        <div class="form-text">Maksimal 5MB. Format: PDF, JPG, PNG, DOC, XLS</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi (opsional)</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Field Modal (dynamic) -->
<div class="modal fade" id="updateFieldModal" tabindex="-1" aria-labelledby="updateFieldModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="updateFieldForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="updateFieldModalLabel">Update Data</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="updateFieldModalBody">
                    <!-- Dynamic content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
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
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    
    .timeline-content {
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
        border-left: 3px solid #dee2e6;
    }
    
    .nav-tabs .nav-link {
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }
    
    pre {
        font-size: 12px;
        max-height: 200px;
        overflow-y: auto;
    }
</style>
@endpush

@push('scripts')
<script>
    // Tab functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Activate tab based on URL hash
        const hash = window.location.hash;
        if (hash) {
            const tab = new bootstrap.Tab(document.querySelector(hash + '-tab'));
            tab.show();
        }
        
        // Update URL hash when tab changes
        const tabEls = document.querySelectorAll('#assetTab button[data-bs-toggle="tab"]');
        tabEls.forEach(tab => {
            tab.addEventListener('shown.bs.tab', function (event) {
                const target = event.target.getAttribute('data-bs-target');
                window.location.hash = target;
            });
        });
    });
    
    // Update asset field (status, condition, location)
    function updateAssetField(field) {
        let title = '';
        let content = '';
        
        switch(field) {
            case 'status':
                title = 'Ubah Status Aset';
                content = `
                    <div class="mb-3">
                        <label for="value" class="form-label">Status Baru</label>
                        <select class="form-control" id="value" name="value" required>
                            <option value="aktif" {{ $asset->status == 'aktif' ? 'selected' : '' }}>Aktif</option>
                            <option value="nonaktif" {{ $asset->status == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                            <option value="dalam_perbaikan" {{ $asset->status == 'dalam_perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                        </select>
                    </div>
                `;
                break;
                
            case 'condition':
                title = 'Ubah Kondisi Aset';
                content = `
                    <div class="mb-3">
                        <label for="value" class="form-label">Kondisi Baru</label>
                        <select class="form-control" id="value" name="value" required>
                            <option value="Baik" {{ $asset->condition == 'Baik' ? 'selected' : '' }}>Baik</option>
                            <option value="Rusak Ringan" {{ $asset->condition == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                            <option value="Rusak Berat" {{ $asset->condition == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                        </select>
                    </div>
                `;
                break;
                
            case 'location_id':
                title = 'Pindah Lokasi Aset';
                content = `
                    <div class="mb-3">
                        <label for="value" class="form-label">Lokasi Baru</label>
                        <select class="form-control" id="value" name="value" required>
                            <option value="">Pilih Lokasi</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->location_id }}" {{ $asset->location_id == $location->location_id ? 'selected' : '' }}>
                                    {{ $location->name }} ({{ $location->type }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                `;
                break;
        }
        
        content += `
            <div class="mb-3">
                <label for="notes" class="form-label">Catatan (opsional)</label>
                <textarea class="form-control" id="notes" name="notes" rows="3" 
                          placeholder="Alasan perubahan..."></textarea>
            </div>
            <input type="hidden" name="field" value="${field}">
        `;
        
        $('#updateFieldModalLabel').text(title);
        $('#updateFieldModalBody').html(content);
        
        const modal = new bootstrap.Modal(document.getElementById('updateFieldModal'));
        modal.show();
    }
    
    // Submit update field form
    $('#updateFieldForm').submit(function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        
        $.ajax({
            url: '{{ route("opd.assets.updateField", $asset) }}',
            method: 'POST',
            data: formData,
            beforeSend: function() {
                Swal.fire({
                    title: 'Memperbarui...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                    
                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('updateFieldModal')).hide();
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'Terjadi kesalahan';
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: error,
                    confirmButtonText: 'OK'
                });
            }
        });
    });
    
    // Upload document
    $('#uploadDocumentForm').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: '{{ route("opd.assets.uploadDocument", $asset) }}',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                Swal.fire({
                    title: 'Mengupload...',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                    
                    // Close modal
                    bootstrap.Modal.getInstance(document.getElementById('uploadDocumentModal')).hide();
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.message || 'Terjadi kesalahan';
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    text: error,
                    confirmButtonText: 'OK'
                });
            }
        });
    });
    
    // Delete document
    function deleteDocument(documentId) {
        Swal.fire({
            title: 'Hapus Dokumen?',
            text: "Dokumen yang dihapus tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("opd/assets/documents") }}/' + documentId,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: response.message,
                                confirmButtonText: 'OK'
                            }).then(() => {
                                location.reload();
                            });
                        }
                    },
                    error: function(xhr) {
                        const error = xhr.responseJSON?.message || 'Terjadi kesalahan';
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: error,
                            confirmButtonText: 'OK'
                        });
                    }
                });
            }
        });
    }
    
    // Show audit details
    function showAuditDetails(auditId) {
        Swal.fire({
            icon: 'info',
            title: 'Fitur Detail Audit',
            text: 'Fitur ini sedang dalam pengembangan',
            confirmButtonText: 'Mengerti'
        });
    }
</script>
@endpush