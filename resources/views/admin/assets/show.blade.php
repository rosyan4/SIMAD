@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-0">
                    <i class="fas fa-cube me-2"></i>Detail Aset
                    <small class="text-muted">{{ $asset->asset_code }}</small>
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.assets.index') }}">Aset</a></li>
                        <li class="breadcrumb-item active">{{ $asset->asset_code }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.assets.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                @if($asset->document_verification_status == 'belum_diverifikasi')
                    <button type="button" class="btn btn-warning btn-sm" 
                            data-bs-toggle="modal" data-bs-target="#verifyModal">
                        <i class="fas fa-check-circle me-1"></i> Verifikasi
                    </button>
                @endif
                @if($asset->validation_status == 'belum_divalidasi')
                    <button type="button" class="btn btn-info btn-sm"
                            data-bs-toggle="modal" data-bs-target="#validateModal">
                        <i class="fas fa-clipboard-check me-1"></i> Validasi
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<ul class="nav nav-tabs mb-3">
    @foreach($allowedTabs as $tabName)
        <li class="nav-item">
            <button class="nav-link {{ $tab == $tabName ? 'active' : '' }}"
                    onclick="switchTab('{{ $tabName }}', this)">
                @php
                    $icon = match($tabName) {
                        'detail' => 'info-circle',
                        'history' => 'history',
                        'documents' => 'file-alt',
                        'audits' => 'clipboard-check',
                        'maintenance' => 'tools',
                        'mutations' => 'exchange-alt',
                        default => 'circle'
                    };
                @endphp
                <i class="fas fa-{{ $icon }} me-1"></i>
                {{ ucfirst($tabName) }}
            </button>
        </li>
    @endforeach
</ul>

<!-- Tab Content -->
<div class="tab-content">
    <!-- Detail Tab -->
    <div class="tab-pane fade {{ $tab == 'detail' ? 'show active' : '' }}" id="detail">
        <div class="row">
            <!-- Basic Information -->
            <div class="col-lg-8">
                <div class="card-custom mb-4">
                    <div class="card-header">
                        <i class="fas fa-info-circle me-2"></i> Informasi Dasar
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Kode Aset</label>
                                <p class="mb-0"><strong>{{ $asset->asset_code }}</strong></p>
                                @if($asset->asset_code_old)
                                    <small class="text-muted">Kode lama: {{ $asset->asset_code_old }}</small>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Nama Aset</label>
                                <p class="mb-0"><strong>{{ $asset->name }}</strong></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Kategori</label>
                                <p class="mb-0">
                                    {{ $asset->category->kib_code ?? '-' }} - {{ $asset->category->name ?? '-' }}
                                    <br>
                                    <small class="text-muted">Sub Kategori: {{ $asset->sub_category_name }}</small>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">OPD Pemilik</label>
                                <p class="mb-0">
                                    <strong>{{ $asset->opdUnit->kode_opd ?? '-' }} - {{ $asset->opdUnit->nama_opd ?? '-' }}</strong>
                                    <br>
                                    <small class="text-muted">Kepala: {{ $asset->opdUnit->kepala_opd ?? '-' }}</small>
                                </p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Lokasi</label>
                                <p class="mb-0">{{ $asset->location->name ?? 'Belum ditentukan' }}</p>
                                @if($asset->location)
                                    <small class="text-muted">{{ $asset->location->address }}</small>
                                @endif
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Tahun Perolehan</label>
                                <p class="mb-0">{{ $asset->acquisition_year }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Nilai Aset</label>
                                <p class="mb-0"><strong>{{ $asset->formatted_value }}</strong></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Kondisi</label>
                                @php
                                    $conditionClass = match($asset->condition) {
                                        'Baik' => 'success',
                                        'Rusak Ringan' => 'warning',
                                        'Rusak Berat' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <span class="badge bg-{{ $conditionClass }}">{{ $asset->condition }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- KIB Data -->
                @if($asset->kib_data && is_array($asset->kib_data))
                    <div class="card-custom">
                        <div class="card-header">
                            <i class="fas fa-database me-2"></i> Data KIB {{ $asset->category->kib_code ?? '' }}
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($asset->kib_data as $key => $value)
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label text-muted">{{ ucwords(str_replace('_', ' ', $key)) }}</label>
                                        <p class="mb-0">{{ $value ?? '-' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Status & Verification -->
            <div class="col-lg-4">
                <div class="card-custom mb-4">
                    <div class="card-header">
                        <i class="fas fa-tags me-2"></i> Status & Verifikasi
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label class="form-label text-muted">Status Aset</label>
                            @php
                                $statusClass = match($asset->status) {
                                    'aktif' => 'success',
                                    'dimutasi' => 'warning',
                                    'dihapus' => 'danger',
                                    'dalam_perbaikan' => 'info',
                                    default => 'secondary'
                                };
                            @endphp
                            <div class="d-flex align-items-center">
                                <span class="badge bg-{{ $statusClass }} fs-6">{{ ucfirst($asset->status) }}</span>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-muted">Verifikasi Dokumen</label>
                            @php
                                $verifClass = match($asset->document_verification_status) {
                                    'valid' => 'success',
                                    'tidak_valid' => 'danger',
                                    default => 'warning'
                                };
                            @endphp
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="badge bg-{{ $verifClass }} fs-6">
                                    {{ str_replace('_', ' ', $asset->document_verification_status) }}
                                </span>
                                @if($asset->document_verification_status == 'belum_diverifikasi')
                                    <button type="button" class="btn btn-warning btn-sm" 
                                            data-bs-toggle="modal" data-bs-target="#verifyModal">
                                        Verifikasi
                                    </button>
                                @endif
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label text-muted">Validasi Aset</label>
                            @php
                                $validClass = match($asset->validation_status) {
                                    'disetujui' => 'success',
                                    'revisi' => 'warning',
                                    'ditolak' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="badge bg-{{ $validClass }} fs-6">
                                    {{ str_replace('_', ' ', $asset->validation_status) }}
                                </span>
                                @if($asset->validation_status == 'belum_divalidasi')
                                    <button type="button" class="btn btn-info btn-sm"
                                            data-bs-toggle="modal" data-bs-target="#validateModal">
                                        Validasi
                                    </button>
                                @endif
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Dibuat Oleh</label>
                            <p class="mb-0">{{ $asset->creator->name ?? 'System' }}</p>
                            <small class="text-muted">{{ $asset->created_at->translatedFormat('d F Y H:i') }}</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label text-muted">Terakhir Diupdate</label>
                            <p class="mb-0">{{ $asset->updated_at->diffForHumans() }}</p>
                            <small class="text-muted">{{ $asset->updated_at->translatedFormat('d F Y H:i') }}</small>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div class="card-custom">
                    <div class="card-header">
                        <i class="fas fa-chart-bar me-2"></i> Statistik Singkat
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Mutasi</span>
                            <span class="badge bg-info">{{ $asset->mutations->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Maintenance</span>
                            <span class="badge bg-warning">{{ $asset->maintenances->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Audit</span>
                            <span class="badge bg-success">{{ $asset->audits->count() }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted">Dokumen</span>
                            <span class="badge bg-secondary">{{ $asset->documents->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- History Tab -->
    <div class="tab-pane fade {{ $tab == 'history' ? 'show active' : '' }}" id="history">
        <div class="card-custom">
            <div class="card-header">
                <i class="fas fa-history me-2"></i> Riwayat Perubahan
            </div>
            <div class="card-body">
                <div class="timeline">
                    @forelse($asset->histories as $history)
                        <div class="timeline-item mb-4">
                            <div class="d-flex">
                                <div class="timeline-icon me-3">
                                    @php
                                        $historyIcon = match($history->action) {
                                            'create' => 'plus-circle text-success',
                                            'update' => 'edit text-primary',
                                            'delete' => 'trash text-danger',
                                            'restore' => 'undo text-info',
                                            'verifikasi' => 'check-circle text-warning',
                                            'validasi' => 'clipboard-check text-info',
                                            default => 'circle text-secondary'
                                        };
                                    @endphp
                                    <i class="fas fa-{{ $historyIcon }} fa-lg"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1">{{ $history->description }}</h6>
                                        <small class="text-muted">{{ $history->change_date->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1 text-muted small">
                                        <i class="fas fa-user me-1"></i>{{ $history->changer->name ?? 'System' }}
                                        <i class="fas fa-desktop ms-3 me-1"></i>{{ $history->ip_address }}
                                    </p>
                                    
                                    @if($history->old_value && $history->new_value)
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <small class="text-muted d-block">Sebelum:</small>
                                                <pre class="bg-light p-2 rounded small mb-0">{{ json_encode($history->old_value, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted d-block">Sesudah:</small>
                                                <pre class="bg-light p-2 rounded small mb-0">{{ json_encode($history->new_value, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-3"></i>
                            <p>Tidak ada riwayat perubahan</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <!-- Documents Tab -->
    <div class="tab-pane fade {{ $tab == 'documents' ? 'show active' : '' }}" id="documents">
        <div class="card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-file-alt me-2"></i> Dokumen Aset
                </div>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                    <i class="fas fa-upload me-1"></i> Upload Dokumen
                </button>
            </div>
            <div class="card-body">
                @forelse($asset->documents as $document)
                    <div class="document-item d-flex justify-content-between align-items-center mb-3 p-3 border rounded">
                        <div>
                            <h6 class="mb-1">{{ $document->file_type }} - {{ $document->document_type }}</h6>
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i>{{ $document->uploader->name ?? 'Unknown' }}
                                <i class="fas fa-calendar ms-3 me-1"></i>{{ $document->uploaded_at->translatedFormat('d F Y H:i') }}
                            </small>
                        </div>
                        <div>
                            <span class="badge {{ $document->verified_status == 'verified' ? 'bg-success' : 'bg-warning' }}">
                                {{ $document->verified_status }}
                            </span>
                            <a href="#" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-file-alt fa-2x mb-3"></i>
                        <p>Belum ada dokumen untuk aset ini</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Audits Tab -->
    <div class="tab-pane fade {{ $tab == 'audits' ? 'show active' : '' }}" id="audits">
        <div class="card-custom">
            <div class="card-header">
                <i class="fas fa-clipboard-check me-2"></i> Laporan Audit
            </div>
            <div class="card-body">
                @forelse($asset->audits as $audit)
                    <div class="audit-item mb-4 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1">Audit {{ $audit->audit_date->translatedFormat('d F Y') }}</h6>
                                <small class="text-muted">
                                    <i class="fas fa-user-check me-1"></i>{{ $audit->auditor->name ?? 'Unknown' }}
                                </small>
                            </div>
                            <span class="badge {{ match($audit->status) {
                                'completed' => 'bg-success',
                                'follow_up' => 'bg-warning',
                                'draft' => 'bg-secondary',
                                default => 'bg-info'
                            } }}">
                                {{ $audit->status_display }}
                            </span>
                        </div>
                        <p class="mb-2">{{ $audit->findings_summary }}</p>
                        @if($audit->follow_up)
                            <div class="alert alert-warning small mb-0">
                                <strong>Tindak Lanjut:</strong> {{ $audit->follow_up }}
                                @if($audit->follow_up_deadline)
                                    <br><small>Batas: {{ $audit->follow_up_deadline->translatedFormat('d F Y') }}</small>
                                @endif
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-clipboard-list fa-2x mb-3"></i>
                        <p>Belum ada laporan audit untuk aset ini</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Maintenance Tab -->
    <div class="tab-pane fade {{ $tab == 'maintenance' ? 'show active' : '' }}" id="maintenance">
        <div class="card-custom">
            <div class="card-header">
                <i class="fas fa-tools me-2"></i> Riwayat Perawatan
            </div>
            <div class="card-body">
                @forelse($asset->maintenances as $maintenance)
                    <div class="maintenance-item mb-4 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1">{{ $maintenance->title }}</h6>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    {{ $maintenance->scheduled_date->translatedFormat('d F Y') }}
                                    @if($maintenance->actual_date)
                                        → {{ $maintenance->actual_date->translatedFormat('d F Y') }}
                                    @endif
                                </small>
                            </div>
                            <span class="badge {{ match($maintenance->status) {
                                'selesai' => 'bg-success',
                                'dalam_pengerjaan' => 'bg-warning',
                                'dijadwalkan' => 'bg-info',
                                default => 'bg-secondary'
                            } }}">
                                {{ ucfirst($maintenance->status) }}
                            </span>
                        </div>
                        <p class="mb-2">{{ $maintenance->description }}</p>
                        <div class="d-flex justify-content-between">
                            <small class="text-muted">
                                <i class="fas fa-dollar-sign me-1"></i>Biaya: Rp {{ number_format($maintenance->cost ?? 0, 0, ',', '.') }}
                            </small>
                            <small class="text-muted">
                                Vendor: {{ $maintenance->vendor ?? '-' }}
                            </small>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-tools fa-2x mb-3"></i>
                        <p>Belum ada riwayat perawatan untuk aset ini</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <!-- Mutations Tab -->
    <div class="tab-pane fade {{ $tab == 'mutations' ? 'show active' : '' }}" id="mutations">
        <div class="card-custom">
            <div class="card-header">
                <i class="fas fa-exchange-alt me-2"></i> Riwayat Mutasi
            </div>
            <div class="card-body">
                @forelse($asset->mutations as $mutation)
                    <div class="mutation-item mb-4 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6 class="mb-1">Mutasi #{{ $mutation->mutation_id }}</h6>
                                <small class="text-muted">
                                    {{ $mutation->mutation_date->translatedFormat('d F Y') }}
                                    <i class="fas fa-user ms-3 me-1"></i>{{ $mutation->mutator->name ?? 'Unknown' }}
                                </small>
                            </div>
                            <span class="badge {{ match($mutation->status) {
                                'selesai' => 'bg-success',
                                'disetujui' => 'bg-info',
                                'diusulkan' => 'bg-warning',
                                'ditolak' => 'bg-danger',
                                default => 'bg-secondary'
                            } }}">
                                {{ ucfirst($mutation->status) }}
                            </span>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-5">
                                <div class="text-center">
                                    <small class="text-muted d-block">Dari</small>
                                    <strong>{{ $mutation->fromOpdUnit->nama_opd ?? '-' }}</strong>
                                    <br>
                                    <small>{{ $mutation->fromLocation->name ?? '-' }}</small>
                                </div>
                            </div>
                            <div class="col-md-2 text-center">
                                <i class="fas fa-arrow-right fa-2x text-muted my-2"></i>
                            </div>
                            <div class="col-md-5">
                                <div class="text-center">
                                    <small class="text-muted d-block">Ke</small>
                                    <strong>{{ $mutation->toOpdUnit->nama_opd ?? '-' }}</strong>
                                    <br>
                                    <small>{{ $mutation->toLocation->name ?? '-' }}</small>
                                </div>
                            </div>
                        </div>
                        
                        @if($mutation->notes)
                            <div class="mt-3 p-2 bg-light rounded">
                                <small class="text-muted">Catatan:</small>
                                <p class="mb-0 small">{{ $mutation->notes }}</p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-exchange-alt fa-2x mb-3"></i>
                        <p>Belum ada riwayat mutasi untuk aset ini</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Verification Modal -->
<div class="modal fade" id="verifyModal" tabindex="-1">
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
<div class="modal fade" id="validateModal" tabindex="-1">
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

<!-- Upload Document Modal -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Upload Dokumen Aset</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jenis Dokumen</label>
                        <select name="document_type" class="form-select" required>
                            <option value="">Pilih Jenis</option>
                            <option value="sertifikat">Sertifikat</option>
                            <option value="faktur">Faktur</option>
                            <option value="berita_acara">Berita Acara</option>
                            <option value="gambar">Gambar</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">File</label>
                        <input type="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                        <small class="text-muted">Format: PDF, JPG, PNG, DOC (Max: 5MB)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
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

@push('scripts')
<script>
    function switchTab(tabId, element) {
        // Update URL parameter
        const url = new URL(window.location);
        url.searchParams.set('tab', tabId);
        window.history.pushState({}, '', url);
        
        // Reload page to load appropriate data
        window.location.href = url.toString();
    }
</script>
@endpush
@endsection