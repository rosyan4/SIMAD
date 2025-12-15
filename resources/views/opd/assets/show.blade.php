@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <!-- Header dengan Navigation -->
    <div class="row mb-4">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('opd.dashboard.index') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('opd.assets.index') }}">Daftar Aset</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detail Aset</li>
                </ol>
            </nav>
            
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-lg me-3">
                                    @php
                                        $iconMap = [
                                            'A' => 'fas fa-mountain', // Tanah
                                            'B' => 'fas fa-cogs', // Peralatan
                                            'C' => 'fas fa-building', // Gedung
                                            'D' => 'fas fa-road', // Jalan
                                            'E' => 'fas fa-archive', // Lainnya
                                            'F' => 'fas fa-hard-hat' // Konstruksi
                                        ];
                                        $categoryCode = $asset->category->kib_code ?? 'E';
                                    @endphp
                                    <div class="avatar-title rounded-circle bg-primary bg-opacity-10 text-primary" 
                                         style="width: 60px; height: 60px; display: flex; align-items: center; justify-content: center;">
                                        <i class="{{ $iconMap[$categoryCode] ?? 'fas fa-box' }} fa-2x"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="card-title mb-0">{{ $asset->name }}</h4>
                                    <p class="text-muted mb-0">
                                        Kode: <strong>{{ $asset->asset_code }}</strong> | 
                                        Kategori: <strong>{{ $asset->category->name ?? '-' }}</strong>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="btn-group" role="group">
                                <a href="{{ route('opd.assets.edit', $asset) }}" 
                                   class="btn btn-warning {{ $asset->document_verification_status == 'valid' || $asset->validation_status == 'disetujui' ? 'disabled' : '' }}"
                                   title="{{ $asset->document_verification_status == 'valid' || $asset->validation_status == 'disetujui' ? 'Tidak dapat diedit karena sudah diverifikasi/divalidasi' : 'Edit' }}">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                                <button type="button" class="btn btn-danger delete-asset"
                                        data-id="{{ $asset->asset_id }}" 
                                        data-name="{{ $asset->name }}"
                                        {{ $asset->document_verification_status == 'valid' || $asset->validation_status == 'disetujui' ? 'disabled' : '' }}
                                        title="{{ $asset->document_verification_status == 'valid' || $asset->validation_status == 'disetujui' ? 'Tidak dapat dihapus karena sudah diverifikasi/divalidasi' : 'Hapus' }}">
                                    <i class="fas fa-trash"></i> Hapus
                                </button>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#quickUpdateModal">
                                            <i class="fas fa-sync-alt"></i> Update Cepat
                                        </a></li>
                                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#moveLocationModal">
                                            <i class="fas fa-exchange-alt"></i> Pindah Lokasi
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item" href="#">
                                            <i class="fas fa-print"></i> Cetak Detail
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" id="assetTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $tab == 'overview' ? 'active' : '' }}" 
                                    data-bs-toggle="tab" data-bs-target="#overview" type="button">
                                <i class="fas fa-info-circle me-1"></i> Overview
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $tab == 'documents' ? 'active' : '' }}" 
                                    data-bs-toggle="tab" data-bs-target="#documents" type="button">
                                <i class="fas fa-file me-1"></i> Dokumen
                                @if($asset->documents->count() > 0)
                                <span class="badge bg-primary rounded-pill ms-1">{{ $asset->documents->count() }}</span>
                                @endif
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $tab == 'history' ? 'active' : '' }}" 
                                    data-bs-toggle="tab" data-bs-target="#history" type="button">
                                <i class="fas fa-history me-1"></i> Riwayat
                                @if($asset->histories->count() > 0)
                                <span class="badge bg-primary rounded-pill ms-1">{{ $asset->histories->count() }}</span>
                                @endif
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $tab == 'maintenance' ? 'active' : '' }}" 
                                    data-bs-toggle="tab" data-bs-target="#maintenance" type="button">
                                <i class="fas fa-tools me-1"></i> Pemeliharaan
                                @if($asset->maintenances->count() > 0)
                                <span class="badge bg-primary rounded-pill ms-1">{{ $asset->maintenances->count() }}</span>
                                @endif
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link {{ $tab == 'transactions' ? 'active' : '' }}" 
                                    data-bs-toggle="tab" data-bs-target="#transactions" type="button">
                                <i class="fas fa-exchange-alt me-1"></i> Transaksi
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="assetTabContent">
                        
                        <!-- TAB 1: OVERVIEW -->
                        <div class="tab-pane fade {{ $tab == 'overview' ? 'show active' : '' }}" id="overview">
                            <div class="row">
                                <!-- Kolom Kiri: Informasi Dasar -->
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">Informasi Dasar</h5>
                                        </div>
                                        <div class="card-body">
                                            <table class="table table-borderless">
                                                <tbody>
                                                    <tr>
                                                        <td width="40%"><strong>Kode Aset</strong></td>
                                                        <td>
                                                            <span class="badge bg-info">{{ $asset->asset_code }}</span>
                                                            @if($asset->asset_code_old)
                                                            <br><small class="text-muted">Kode Lama: {{ $asset->asset_code_old }}</small>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Nama Aset</strong></td>
                                                        <td>{{ $asset->name }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Kategori</strong></td>
                                                        <td>
                                                            {{ $asset->category->kib_code ?? '-' }} - {{ $asset->category->name ?? '-' }}
                                                            <br><small class="text-muted">Sub Kategori: {{ $asset->sub_category_name }}</small>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Lokasi</strong></td>
                                                        <td>
                                                            @if($asset->location)
                                                            <i class="fas fa-map-marker-alt text-danger me-1"></i>
                                                            {{ $asset->location->name }}
                                                            <br><small class="text-muted">{{ $asset->location->address }}</small>
                                                            @else
                                                            <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>OPD Unit</strong></td>
                                                        <td>{{ $asset->opdUnit->nama_opd ?? '-' }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Dibuat Oleh</strong></td>
                                                        <td>
                                                            {{ $asset->creator->name ?? '-' }}
                                                            <br><small class="text-muted">{{ $asset->created_at->format('d/m/Y H:i') }}</small>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <!-- Status & Kondisi -->
                                    <div class="card mb-4">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">Status & Kondisi</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Status Aset</label>
                                                    @php
                                                        $statusColors = [
                                                            'aktif' => 'success',
                                                            'dimutasi' => 'info',
                                                            'dihapus' => 'secondary',
                                                            'dalam_perbaikan' => 'warning',
                                                            'nonaktif' => 'danger'
                                                        ];
                                                    @endphp
                                                    <div>
                                                        <span class="badge bg-{{ $statusColors[$asset->status] ?? 'secondary' }} fs-6">
                                                            {{ ucfirst(str_replace('_', ' ', $asset->status)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Kondisi Fisik</label>
                                                    @php
                                                        $conditionColors = [
                                                            'Baik' => 'success',
                                                            'Rusak Ringan' => 'warning',
                                                            'Rusak Berat' => 'danger'
                                                        ];
                                                    @endphp
                                                    <div>
                                                        <span class="badge bg-{{ $conditionColors[$asset->condition] ?? 'secondary' }} fs-6">
                                                            {{ $asset->condition }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Verifikasi Dokumen</label>
                                                    @php
                                                        $verifColors = [
                                                            'valid' => 'success',
                                                            'tidak_valid' => 'danger',
                                                            'belum_diverifikasi' => 'warning'
                                                        ];
                                                    @endphp
                                                    <div>
                                                        <span class="badge bg-{{ $verifColors[$asset->document_verification_status] ?? 'secondary' }} fs-6">
                                                            <i class="fas fa-file"></i> 
                                                            {{ ucfirst(str_replace('_', ' ', $asset->document_verification_status)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Status Validasi</label>
                                                    @php
                                                        $valColors = [
                                                            'disetujui' => 'success',
                                                            'ditolak' => 'danger',
                                                            'revisi' => 'warning',
                                                            'belum_divalidasi' => 'secondary'
                                                        ];
                                                    @endphp
                                                    <div>
                                                        <span class="badge bg-{{ $valColors[$asset->validation_status] ?? 'secondary' }} fs-6">
                                                            <i class="fas fa-check"></i> 
                                                            {{ ucfirst(str_replace('_', ' ', $asset->validation_status)) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Kolom Kanan: Nilai & Data KIB -->
                                <div class="col-md-6">
                                    <!-- Nilai & Tahun -->
                                    <div class="card mb-4">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">Nilai & Tahun</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center mb-4">
                                                <h2 class="text-success">{{ $asset->formatted_value }}</h2>
                                                <p class="text-muted">Nilai Aset</p>
                                            </div>
                                            <table class="table table-borderless">
                                                <tbody>
                                                    <tr>
                                                        <td width="40%"><strong>Tahun Perolehan</strong></td>
                                                        <td>{{ $asset->acquisition_year }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Tanggal Input</strong></td>
                                                        <td>{{ $asset->created_at->format('d/m/Y H:i') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td><strong>Terakhir Diupdate</strong></td>
                                                        <td>{{ $asset->updated_at->format('d/m/Y H:i') }}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    
                                    <!-- Data KIB -->
                                    <div class="card mb-4">
                                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                            <h5 class="mb-0">Data KIB {{ $asset->category->kib_code ?? '' }}</h5>
                                            <span class="badge bg-primary">{{ $asset->category->kib_code ?? '-' }}</span>
                                        </div>
                                        <div class="card-body">
                                            @if($asset->kib_data && count($asset->kib_data) > 0)
                                            <table class="table table-borderless">
                                                <tbody>
                                                    @foreach($asset->kib_data as $key => $value)
                                                    @if(!empty($value))
                                                    <tr>
                                                        <td width="40%"><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}</strong></td>
                                                        <td>
                                                            @if(in_array($key, ['sertifikat_tanggal', 'tanggal_mulai', 'tanggal_selesai']))
                                                                {{ \Carbon\Carbon::parse($value)->format('d/m/Y') }}
                                                            @else
                                                                {{ $value }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endif
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            @else
                                            <div class="text-center text-muted py-3">
                                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                                <p>Tidak ada data KIB</p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Quick Actions -->
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h5 class="mb-0">Aksi Cepat</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#quickUpdateModal">
                                                        <i class="fas fa-sync-alt me-1"></i> Update Status
                                                    </button>
                                                </div>
                                                <div class="col-6">
                                                    <button class="btn btn-outline-warning w-100" data-bs-toggle="modal" data-bs-target="#moveLocationModal">
                                                        <i class="fas fa-exchange-alt me-1"></i> Pindah Lokasi
                                                    </button>
                                                </div>
                                                <div class="col-6">
                                                    <a href="#" class="btn btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                                                        <i class="fas fa-upload me-1"></i> Upload Dokumen
                                                    </a>
                                                </div>
                                                <div class="col-6">
                                                    <a href="{{ route('opd.transactions.create', ['type' => 'maintenance', 'asset_id' => $asset->asset_id]) }}" 
                                                       class="btn btn-outline-success w-100">
                                                        <i class="fas fa-tools me-1"></i> Jadwalkan Perawatan
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- TAB 2: DOKUMEN -->
                        <div class="tab-pane fade {{ $tab == 'documents' ? 'show active' : '' }}" id="documents">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5>Dokumen Pendukung</h5>
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                                            <i class="fas fa-upload me-1"></i> Upload Dokumen Baru
                                        </button>
                                    </div>
                                    
                                    @if($asset->documents->count() > 0)
                                    <div class="row">
                                        @foreach($asset->documents as $document)
                                        <div class="col-md-4 mb-4">
                                            <div class="card h-100">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center mb-3">
                                                        @php
                                                            $fileIcons = [
                                                                'pdf' => 'fas fa-file-pdf text-danger',
                                                                'jpg' => 'fas fa-file-image text-success',
                                                                'jpeg' => 'fas fa-file-image text-success',
                                                                'png' => 'fas fa-file-image text-success',
                                                                'doc' => 'fas fa-file-word text-primary',
                                                                'docx' => 'fas fa-file-word text-primary',
                                                                'xls' => 'fas fa-file-excel text-success',
                                                                'xlsx' => 'fas fa-file-excel text-success'
                                                            ];
                                                            $icon = $fileIcons[$document->file_type] ?? 'fas fa-file text-secondary';
                                                        @endphp
                                                        <div class="me-3">
                                                            <i class="{{ $icon }} fa-3x"></i>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ Str::limit($document->description ?: 'Dokumen', 20) }}</h6>
                                                            <small class="text-muted">
                                                                {{ strtoupper($document->file_type) }} • 
                                                                {{ $document->document_type }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <span class="badge bg-info">{{ $document->document_type }}</span>
                                                        @if($document->verified_status)
                                                        <span class="badge bg-success">Verified</span>
                                                        @endif
                                                    </div>
                                                    <div class="d-flex justify-content-between">
                                                        <small class="text-muted">
                                                            <i class="fas fa-user me-1"></i>
                                                            {{ $document->uploader->name ?? 'System' }}
                                                            <br>
                                                            <i class="fas fa-calendar me-1"></i>
                                                            {{ $document->uploaded_at->format('d/m/Y') }}
                                                        </small>
                                                        <div class="btn-group">
                                                            <a href="{{ Storage::url($document->file_path) }}" 
                                                               target="_blank" class="btn btn-sm btn-outline-primary" title="Lihat">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ Storage::url($document->file_path) }}" 
                                                               download class="btn btn-sm btn-outline-success" title="Download">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                            <button type="button" class="btn btn-sm btn-outline-danger delete-document" 
                                                                    data-id="{{ $document->document_id }}" title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @else
                                    <div class="text-center py-5">
                                        <i class="fas fa-file-alt fa-4x text-muted mb-3"></i>
                                        <h5 class="text-muted">Belum ada dokumen</h5>
                                        <p class="text-muted">Upload dokumen untuk melengkapi data aset</p>
                                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                                            <i class="fas fa-upload me-1"></i> Upload Dokumen Pertama
                                        </button>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- TAB 3: RIWAYAT -->
                        <div class="tab-pane fade {{ $tab == 'history' ? 'show active' : '' }}" id="history">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="mb-4">Riwayat Perubahan</h5>
                                    
                                    @if($asset->histories->count() > 0)
                                    <div class="timeline">
                                        @foreach($asset->histories->sortByDesc('change_date') as $history)
                                        <div class="timeline-item">
                                            <div class="timeline-marker 
                                                @if($history->action == 'create') bg-success
                                                @elseif($history->action == 'update') bg-primary
                                                @elseif($history->action == 'delete') bg-danger
                                                @elseif($history->action == 'restore') bg-warning
                                                @else bg-secondary @endif">
                                            </div>
                                            <div class="timeline-content">
                                                <div class="d-flex justify-content-between">
                                                    <h6 class="mb-1">
                                                        @if($history->action == 'create') Aset Dibuat
                                                        @elseif($history->action == 'update') Data Diperbarui
                                                        @elseif($history->action == 'delete') Aset Dihapus
                                                        @elseif($history->action == 'restore') Aset Dipulihkan
                                                        @elseif($history->action == 'verifikasi') Dokumen Diverifikasi
                                                        @elseif($history->action == 'validasi') Aset Divalidasi
                                                        @else {{ ucfirst($history->action) }}
                                                        @endif
                                                    </h6>
                                                    <small class="text-muted">{{ $history->change_date->format('d/m/Y H:i') }}</small>
                                                </div>
                                                <p class="mb-1">{{ $history->description }}</p>
                                                <small class="text-muted">
                                                    <i class="fas fa-user me-1"></i>
                                                    {{ $history->changer->name ?? 'System' }}
                                                </small>
                                                
                                                @if($history->old_value || $history->new_value)
                                                <div class="mt-2">
                                                    <button class="btn btn-sm btn-outline-info" type="button" 
                                                            data-bs-toggle="collapse" data-bs-target="#historyDetails{{ $history->history_id }}">
                                                        <i class="fas fa-code-branch me-1"></i> Detail Perubahan
                                                    </button>
                                                    <div class="collapse mt-2" id="historyDetails{{ $history->history_id }}">
                                                        <div class="card card-body">
                                                            <div class="row">
                                                                @if($history->old_value)
                                                                <div class="col-md-6">
                                                                    <h6>Sebelum:</h6>
                                                                    <pre class="bg-light p-2" style="font-size: 12px;">{{ json_encode($history->old_value, JSON_PRETTY_PRINT) }}</pre>
                                                                </div>
                                                                @endif
                                                                @if($history->new_value)
                                                                <div class="col-md-6">
                                                                    <h6>Sesudah:</h6>
                                                                    <pre class="bg-light p-2" style="font-size: 12px;">{{ json_encode($history->new_value, JSON_PRETTY_PRINT) }}</pre>
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
                                        <i class="fas fa-history fa-4x text-muted mb-3"></i>
                                        <h5 class="text-muted">Belum ada riwayat perubahan</h5>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- TAB 4: PEMELIHARAAN -->
                        <div class="tab-pane fade {{ $tab == 'maintenance' ? 'show active' : '' }}" id="maintenance">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-4">
                                        <h5>Riwayat Pemeliharaan</h5>
                                        <a href="{{ route('opd.transactions.create', ['type' => 'maintenance', 'asset_id' => $asset->asset_id]) }}" 
                                           class="btn btn-primary">
                                            <i class="fas fa-plus-circle me-1"></i> Jadwalkan Pemeliharaan
                                        </a>
                                    </div>
                                    
                                    @if($asset->maintenances->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Jenis</th>
                                                    <th>Deskripsi</th>
                                                    <th>Status</th>
                                                    <th>Biaya</th>
                                                    <th>Vendor</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($asset->maintenances->sortByDesc('scheduled_date') as $maintenance)
                                                <tr>
                                                    <td>
                                                        <strong>{{ $maintenance->scheduled_date->format('d/m/Y') }}</strong>
                                                        @if($maintenance->actual_date)
                                                        <br><small>Selesai: {{ $maintenance->actual_date->format('d/m/Y') }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $typeLabels = [
                                                                'rutin' => 'Rutin',
                                                                'perbaikan' => 'Perbaikan',
                                                                'kalibrasi' => 'Kalibrasi',
                                                                'penggantian' => 'Penggantian',
                                                                'lainnya' => 'Lainnya'
                                                            ];
                                                        @endphp
                                                        {{ $typeLabels[$maintenance->maintenance_type] ?? $maintenance->maintenance_type }}
                                                    </td>
                                                    <td>
                                                        <strong>{{ $maintenance->title }}</strong>
                                                        @if($maintenance->description)
                                                        <br><small>{{ Str::limit($maintenance->description, 50) }}</small>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @php
                                                            $statusColors = [
                                                                'dijadwalkan' => 'warning',
                                                                'dalam_pengerjaan' => 'info',
                                                                'selesai' => 'success',
                                                                'ditunda' => 'secondary',
                                                                'dibatalkan' => 'danger'
                                                            ];
                                                        @endphp
                                                        <span class="badge bg-{{ $statusColors[$maintenance->status] ?? 'secondary' }}">
                                                            {{ ucfirst(str_replace('_', ' ', $maintenance->status)) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        @if($maintenance->cost > 0)
                                                        <strong class="text-success">Rp {{ number_format($maintenance->cost, 0, ',', '.') }}</strong>
                                                        @else
                                                        <span class="text-muted">-</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $maintenance->vendor ?: '-' }}</td>
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
                                        <i class="fas fa-tools fa-4x text-muted mb-3"></i>
                                        <h5 class="text-muted">Belum ada riwayat pemeliharaan</h5>
                                        <p class="text-muted">Jadwalkan pemeliharaan untuk menjaga kondisi aset</p>
                                        <a href="{{ route('opd.transactions.create', ['type' => 'maintenance', 'asset_id' => $asset->asset_id]) }}" 
                                           class="btn btn-primary">
                                            <i class="fas fa-plus-circle me-1"></i> Jadwalkan Pemeliharaan Pertama
                                        </a>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- TAB 5: TRANSAKSI -->
                        <div class="tab-pane fade {{ $tab == 'transactions' ? 'show active' : '' }}" id="transactions">
                            <div class="row">
                                <div class="col-md-12">
                                    <h5 class="mb-4">Transaksi Aset</h5>
                                    
                                    <!-- Mutasi -->
                                    <div class="card mb-4">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Riwayat Mutasi</h6>
                                        </div>
                                        <div class="card-body">
                                            @if($asset->mutations->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Tanggal</th>
                                                            <th>Dari</th>
                                                            <th>Ke</th>
                                                            <th>Status</th>
                                                            <th>Detail</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($asset->mutations->sortByDesc('mutation_date') as $mutation)
                                                        <tr>
                                                            <td>{{ $mutation->mutation_date->format('d/m/Y') }}</td>
                                                            <td>{{ $mutation->fromOpdUnit->nama_opd ?? '-' }}</td>
                                                            <td>{{ $mutation->toOpdUnit->nama_opd ?? '-' }}</td>
                                                            <td>
                                                                <span class="badge bg-{{ $mutation->status == 'selesai' ? 'success' : ($mutation->status == 'diusulkan' ? 'warning' : 'info') }}">
                                                                    {{ ucfirst($mutation->status) }}
                                                                </span>
                                                            </td>
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
                                            <div class="text-center py-3">
                                                <p class="text-muted mb-0">Belum ada riwayat mutasi</p>
                                            </div>
                                            @endif
                                            <div class="text-end mt-3">
                                                <a href="{{ route('opd.transactions.create', ['type' => 'mutation', 'asset_id' => $asset->asset_id]) }}" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-exchange-alt me-1"></i> Ajukan Mutasi
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Penghapusan -->
                                    <div class="card">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0">Riwayat Penghapusan</h6>
                                        </div>
                                        <div class="card-body">
                                            @if($asset->deletions->count() > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>Tanggal</th>
                                                            <th>Alasan</th>
                                                            <th>Status</th>
                                                            <th>Detail</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($asset->deletions->sortByDesc('proposed_at') as $deletion)
                                                        <tr>
                                                            <td>{{ $deletion->proposed_at->format('d/m/Y') }}</td>
                                                            <td>{{ $deletion->deletion_reason_display }}</td>
                                                            <td>
                                                                <span class="badge bg-{{ $deletion->status == 'selesai' ? 'success' : ($deletion->status == 'diusulkan' ? 'warning' : 'info') }}">
                                                                    {{ $deletion->status_display }}
                                                                </span>
                                                            </td>
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
                                            <div class="text-center py-3">
                                                <p class="text-muted mb-0">Belum ada riwayat penghapusan</p>
                                            </div>
                                            @endif
                                            @if($asset->status != 'dihapus')
                                            <div class="text-end mt-3">
                                                <a href="{{ route('opd.transactions.create', ['type' => 'deletion', 'asset_id' => $asset->asset_id]) }}" 
                                                   class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash me-1"></i> Ajukan Penghapusan
                                                </a>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL: Quick Update -->
<div class="modal fade" id="quickUpdateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="quickUpdateForm" method="POST" action="{{ route('opd.assets.updateField', $asset) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Update Cepat Aset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="update_field" class="form-label">Field</label>
                        <select class="form-select" id="update_field" name="field" required>
                            <option value="">Pilih Field</option>
                            <option value="status">Status</option>
                            <option value="condition">Kondisi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="update_value" class="form-label">Nilai Baru</label>
                        <select class="form-select" id="update_value" name="value" required disabled>
                            <option value="">Pilih Field terlebih dahulu</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="update_notes" class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="update_notes" name="notes" rows="3" 
                                  placeholder="Alasan perubahan..."></textarea>
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

<!-- MODAL: Move Location -->
<div class="modal fade" id="moveLocationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="moveLocationForm" method="POST" action="{{ route('opd.assets.updateField', $asset) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Pindah Lokasi Aset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="location_id" class="form-label">Lokasi Baru</label>
                        <select class="form-select" id="location_id" name="value" required>
                            <option value="">Pilih Lokasi</option>
                            @foreach($locations as $location)
                            <option value="{{ $location->location_id }}" 
                                    {{ $asset->location_id == $location->location_id ? 'selected' : '' }}>
                                {{ $location->name }} ({{ $location->type }})
                            </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="field" value="location_id">
                    </div>
                    <div class="mb-3">
                        <label for="location_notes" class="form-label">Alasan Pemindahan</label>
                        <textarea class="form-control" id="location_notes" name="notes" rows="3" 
                                  placeholder="Alasan pemindahan lokasi..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Pindah Lokasi</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL: Upload Document -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="uploadDocumentForm" method="POST" action="{{ route('opd.assets.uploadDocument', $asset) }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Upload Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="document_file" class="form-label">File Dokumen</label>
                        <input type="file" class="form-control" id="document_file" name="document" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx" required>
                        <small class="text-muted">Format: PDF, JPG, PNG, DOC, XLS (Max: 5MB)</small>
                    </div>
                    <div class="mb-3">
                        <label for="document_type" class="form-label">Jenis Dokumen</label>
                        <select class="form-select" id="document_type" name="document_type" required>
                            <option value="">Pilih Jenis</option>
                            @foreach($additionalData['documentTypes'] ?? ['pengadaan', 'mutasi', 'penghapusan', 'pemeliharaan', 'lainnya'] as $type)
                            <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="document_description" class="form-label">Deskripsi (Opsional)</label>
                        <textarea class="form-control" id="document_description" name="description" 
                                  rows="2" placeholder="Deskripsi dokumen..."></textarea>
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

<!-- MODAL: Delete Confirmation -->
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
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    .timeline-marker {
        position: absolute;
        left: -30px;
        top: 0;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 3px solid #fff;
    }
    .timeline-content {
        padding: 10px 15px;
        background: #f8f9fa;
        border-radius: 5px;
        border-left: 3px solid #dee2e6;
    }
    .avatar-title {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .nav-tabs .nav-link {
        border-top: none;
        border-left: none;
        border-right: none;
    }
    .nav-tabs .nav-link.active {
        border-bottom: 3px solid #0d6efd;
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Quick Update Modal
    $('#update_field').change(function() {
        const field = $(this).val();
        const $valueSelect = $('#update_value');
        
        $valueSelect.empty().prop('disabled', true);
        
        if (field === 'status') {
            $valueSelect.append('<option value="">Pilih Status</option>');
            $valueSelect.append('<option value="aktif">Aktif</option>');
            $valueSelect.append('<option value="dalam_perbaikan">Dalam Perbaikan</option>');
            $valueSelect.append('<option value="nonaktif">Nonaktif</option>');
            $valueSelect.prop('disabled', false);
        } else if (field === 'condition') {
            $valueSelect.append('<option value="">Pilih Kondisi</option>');
            $valueSelect.append('<option value="Baik">Baik</option>');
            $valueSelect.append('<option value="Rusak Ringan">Rusak Ringan</option>');
            $valueSelect.append('<option value="Rusak Berat">Rusak Berat</option>');
            $valueSelect.prop('disabled', false);
        }
    });
    
    // Delete asset confirmation
    $('.delete-asset').click(function() {
        if ($(this).prop('disabled')) return;
        
        const assetId = $(this).data('id');
        const assetName = $(this).data('name');
        
        $('#assetNameToDelete').text(assetName);
        $('#deleteForm').attr('action', "{{ url('opd/assets') }}/" + assetId);
        $('#deleteModal').modal('show');
    });
    
    // Delete document
    $('.delete-document').click(function() {
        const documentId = $(this).data('id');
        const documentElement = $(this).closest('.col-md-4');
        
        if (confirm('Hapus dokumen ini?')) {
            $.ajax({
                url: "{{ url('opd/assets/delete-document') }}/" + documentId,
                method: 'DELETE',
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        documentElement.fadeOut(300, function() {
                            $(this).remove();
                        });
                    } else {
                        alert('Gagal menghapus dokumen: ' + response.message);
                    }
                }
            });
        }
    });
    
    // Quick Update Form
    $('#quickUpdateForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#quickUpdateModal').modal('hide');
                    location.reload();
                } else {
                    alert('Gagal update: ' + response.message);
                }
            }
        });
    });
    
    // Move Location Form
    $('#moveLocationForm').submit(function(e) {
        e.preventDefault();
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    $('#moveLocationModal').modal('hide');
                    location.reload();
                } else {
                    alert('Gagal memindahkan lokasi: ' + response.message);
                }
            }
        });
    });
    
    // Upload Document Form
    $('#uploadDocumentForm').submit(function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    $('#uploadDocumentModal').modal('hide');
                    location.reload();
                } else {
                    alert('Gagal upload dokumen: ' + response.message);
                }
            }
        });
    });
});
</script>
@endpush