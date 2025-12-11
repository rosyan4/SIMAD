@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-0">
                    <i class="fas {{ $type == 'mutation' ? 'fa-exchange-alt' : 'fa-trash' }} me-2"></i>
                    Detail Proposal {{ $type == 'mutation' ? 'Mutasi' : 'Penghapusan' }}
                    <small class="text-muted">
                        @if($type == 'mutation')
                            MUT-{{ str_pad($proposal->mutation_id, 6, '0', STR_PAD_LEFT) }}
                        @else
                            DEL-{{ str_pad($proposal->deletion_id, 6, '0', STR_PAD_LEFT) }}
                        @endif
                    </small>
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.proposals.index') }}">Proposal</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.proposals.index', ['type' => $type == 'mutation' ? 'mutations' : 'deletions']) }}">
                            {{ $type == 'mutation' ? 'Mutasi' : 'Penghapusan' }}
                        </a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.proposals.index', ['type' => $type == 'mutation' ? 'mutations' : 'deletions']) }}" 
                   class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                
                <!-- Action buttons based on status -->
                @if($proposal->status == 'diusulkan')
                    @if($type == 'deletion' && !$proposal->verified_by)
                        <button type="button" class="btn btn-warning btn-sm" 
                                data-bs-toggle="modal" data-bs-target="#verifyModal">
                            <i class="fas fa-check me-1"></i> Verifikasi
                        </button>
                    @endif
                    
                    <button type="button" class="btn btn-success btn-sm"
                            data-bs-toggle="modal" data-bs-target="#approveModal">
                        <i class="fas fa-thumbs-up me-1"></i> Setujui
                    </button>
                    
                    <button type="button" class="btn btn-danger btn-sm"
                            data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="fas fa-thumbs-down me-1"></i> Tolak
                    </button>
                    
                    @if($type == 'deletion')
                        <button type="button" class="btn btn-secondary btn-sm"
                                data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="fas fa-times me-1"></i> Batalkan
                        </button>
                    @endif
                @endif
                
                @if($type == 'deletion' && $proposal->status == 'disetujui')
                    <button type="button" class="btn btn-primary btn-sm"
                            data-bs-toggle="modal" data-bs-target="#completeModal">
                        <i class="fas fa-check-double me-1"></i> Selesaikan
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Tabs Navigation -->
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <button class="nav-link {{ ($tab ?? 'detail') == 'detail' ? 'active' : '' }}"
                onclick="switchTab('detail', this)">
            <i class="fas fa-info-circle me-1"></i> Detail
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link {{ ($tab ?? '') == 'timeline' ? 'active' : '' }}"
                onclick="switchTab('timeline', this)">
            <i class="fas fa-history me-1"></i> Timeline
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link {{ ($tab ?? '') == 'documents' ? 'active' : '' }}"
                onclick="switchTab('documents', this)">
            <i class="fas fa-file-alt me-1"></i> Dokumen
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content">
    <!-- Detail Tab -->
    <div class="tab-pane fade {{ ($tab ?? 'detail') == 'detail' ? 'show active' : '' }}" id="detail">
        <div class="row">
            <!-- Left Column: Proposal Details -->
            <div class="col-lg-8">
                @if($type == 'mutation')
                    <!-- Mutation Details -->
                    <div class="card-custom mb-4">
                        <div class="card-header">
                            <i class="fas fa-exchange-alt me-2"></i> Informasi Mutasi
                        </div>
                        <div class="card-body">
                            <div class="row mb-4">
                                <div class="col-md-5">
                                    <div class="text-center p-3 border rounded bg-light">
                                        <small class="text-muted d-block">Dari</small>
                                        <h5 class="mt-2">{{ $proposal->fromOpdUnit->nama_opd ?? '-' }}</h5>
                                        <p class="mb-1">{{ $proposal->fromLocation->name ?? '-' }}</p>
                                        <small class="text-muted">{{ $proposal->fromLocation->address ?? '' }}</small>
                                    </div>
                                </div>
                                <div class="col-md-2 text-center d-flex align-items-center justify-content-center">
                                    <i class="fas fa-arrow-right fa-2x text-primary"></i>
                                </div>
                                <div class="col-md-5">
                                    <div class="text-center p-3 border rounded bg-light">
                                        <small class="text-muted d-block">Ke</small>
                                        <h5 class="mt-2">{{ $proposal->toOpdUnit->nama_opd ?? '-' }}</h5>
                                        <p class="mb-1">{{ $proposal->toLocation->name ?? '-' }}</p>
                                        <small class="text-muted">{{ $proposal->toLocation->address ?? '' }}</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Tanggal Mutasi</label>
                                    <p class="mb-0">{{ $proposal->mutation_date->translatedFormat('d F Y') }}</p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Status</label>
                                    @php
                                        $statusClass = match($proposal->status) {
                                            'selesai' => 'success',
                                            'disetujui' => 'info',
                                            'diusulkan' => 'warning',
                                            'ditolak' => 'danger',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">{{ ucfirst($proposal->status) }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Diusulkan Oleh</label>
                                    <p class="mb-0">{{ $proposal->mutator->name ?? '-' }}</p>
                                    <small class="text-muted">{{ $proposal->created_at->translatedFormat('d F Y H:i') }}</small>
                                </div>
                                @if($proposal->approved_at)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Disetujui Pada</label>
                                        <p class="mb-0">{{ $proposal->approved_at->translatedFormat('d F Y H:i') }}</p>
                                    </div>
                                @endif
                            </div>
                            
                            @if($proposal->notes)
                                <div class="mt-3 p-3 bg-light rounded">
                                    <label class="form-label text-muted">Catatan</label>
                                    <p class="mb-0">{{ $proposal->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <!-- Deletion Details -->
                    <div class="card-custom mb-4">
                        <div class="card-header">
                            <i class="fas fa-trash me-2"></i> Informasi Penghapusan
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Alasan Penghapusan</label>
                                    <p class="mb-0">
                                        <span class="badge bg-light text-dark">{{ $proposal->deletion_reason_display }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Status</label>
                                    @php
                                        $statusClass = match($proposal->status) {
                                            'selesai' => 'success',
                                            'disetujui' => 'info',
                                            'diusulkan' => 'warning',
                                            'ditolak' => 'danger',
                                            'dibatalkan' => 'secondary',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusClass }}">{{ ucfirst($proposal->status) }}</span>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Metode Penghapusan</label>
                                    <p class="mb-0">{{ $proposal->deletion_method ? ucfirst($proposal->deletion_method) : '-' }}</p>
                                </div>
                                @if($proposal->sale_value)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Nilai Penjualan</label>
                                        <p class="mb-0"><strong>Rp {{ number_format($proposal->sale_value, 0, ',', '.') }}</strong></p>
                                    </div>
                                @endif
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Diusulkan Oleh</label>
                                    <p class="mb-0">{{ $proposal->proposer->name ?? '-' }}</p>
                                    <small class="text-muted">{{ $proposal->proposed_at->translatedFormat('d F Y H:i') }}</small>
                                </div>
                                @if($proposal->verified_by)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Diverifikasi Oleh</label>
                                        <p class="mb-0">{{ $proposal->verifier->name ?? '-' }}</p>
                                        <small class="text-muted">{{ $proposal->verified_at->translatedFormat('d F Y H:i') }}</small>
                                    </div>
                                @endif
                                @if($proposal->approved_by)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Disetujui Oleh</label>
                                        <p class="mb-0">{{ $proposal->approver->name ?? '-' }}</p>
                                        <small class="text-muted">{{ $proposal->approved_at->translatedFormat('d F Y H:i') }}</small>
                                    </div>
                                @endif
                                @if($proposal->recipient)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-muted">Penerima</label>
                                        <p class="mb-0">{{ $proposal->recipient }}</p>
                                    </div>
                                @endif
                            </div>
                            
                            @if($proposal->reason_details)
                                <div class="mb-3">
                                    <label class="form-label text-muted">Detail Alasan</label>
                                    <div class="p-3 bg-light rounded">
                                        {{ $proposal->reason_details }}
                                    </div>
                                </div>
                            @endif
                            
                            @if($proposal->notes)
                                <div class="mt-3">
                                    <label class="form-label text-muted">Catatan Tambahan</label>
                                    <div class="p-3 bg-light rounded">
                                        {{ $proposal->notes }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
                
                <!-- Supporting Documents -->
                @if(($type == 'mutation' && $proposal->supporting_documents) || ($type == 'deletion' && ($proposal->proposal_documents || $proposal->approval_documents)))
                    <div class="card-custom">
                        <div class="card-header">
                            <i class="fas fa-file-alt me-2"></i> Dokumen Pendukung
                        </div>
                        <div class="card-body">
                            @if($type == 'mutation' && $proposal->supporting_documents)
                                <h6 class="mb-3">Dokumen Mutasi</h6>
                                <div class="row">
                                    @foreach($proposal->supporting_documents as $index => $doc)
                                        <div class="col-md-6 mb-2">
                                            <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                                                <div>
                                                    <i class="fas fa-file me-2"></i>
                                                    <span>Dokumen {{ $index + 1 }}</span>
                                                </div>
                                                <a href="#" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            
                            @if($type == 'deletion')
                                @if($proposal->proposal_documents)
                                    <h6 class="mb-3 mt-4">Dokumen Proposal</h6>
                                    <div class="row">
                                        @foreach($proposal->proposal_documents as $index => $doc)
                                            <div class="col-md-6 mb-2">
                                                <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                                                    <div>
                                                        <i class="fas fa-file me-2"></i>
                                                        <span>{{ $doc }}</span>
                                                    </div>
                                                    <a href="#" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                                
                                @if($proposal->approval_documents)
                                    <h6 class="mb-3 mt-4">Dokumen Persetujuan</h6>
                                    <div class="row">
                                        @foreach($proposal->approval_documents as $index => $doc)
                                            <div class="col-md-6 mb-2">
                                                <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                                                    <div>
                                                        <i class="fas fa-file-check me-2"></i>
                                                        <span>{{ $doc }}</span>
                                                    </div>
                                                    <a href="#" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download"></i>
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Right Column: Asset Information & Actions -->
            <div class="col-lg-4">
                <!-- Asset Information -->
                <div class="card-custom mb-4">
                    <div class="card-header">
                        <i class="fas fa-cube me-2"></i> Informasi Aset
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            @if($proposal->asset)
                                <h5>{{ $proposal->asset->name }}</h5>
                                <p class="text-muted mb-2">{{ $proposal->asset->asset_code }}</p>
                                <span class="badge bg-info">{{ $proposal->asset->category->name ?? '-' }}</span>
                            @else
                                <p class="text-danger">Aset tidak ditemukan</p>
                            @endif
                        </div>
                        
                        @if($proposal->asset)
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <label class="form-label text-muted small">OPD Pemilik</label>
                                    <p class="mb-0">{{ $proposal->asset->opdUnit->nama_opd ?? '-' }}</p>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label text-muted small">Nilai Aset</label>
                                    <p class="mb-0"><strong>{{ $proposal->asset->formatted_value }}</strong></p>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label text-muted small">Kondisi</label>
                                    <span class="badge {{ match($proposal->asset->condition) {
                                        'Baik' => 'bg-success',
                                        'Rusak Ringan' => 'bg-warning',
                                        'Rusak Berat' => 'bg-danger',
                                        default => 'bg-secondary'
                                    } }}">
                                        {{ $proposal->asset->condition }}
                                    </span>
                                </div>
                                <div class="col-12 mb-2">
                                    <label class="form-label text-muted small">Status</label>
                                    <span class="badge {{ match($proposal->asset->status) {
                                        'aktif' => 'bg-success',
                                        'dimutasi' => 'bg-warning',
                                        'dihapus' => 'bg-danger',
                                        default => 'bg-secondary'
                                    } }}">
                                        {{ ucfirst($proposal->asset->status) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <a href="{{ route('admin.assets.show', $proposal->asset) }}" 
                                   class="btn btn-outline-primary btn-sm w-100">
                                    <i class="fas fa-external-link-alt me-1"></i> Lihat Detail Aset
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Status Timeline -->
                <div class="card-custom">
                    <div class="card-header">
                        <i class="fas fa-stream me-2"></i> Status Saat Ini
                    </div>
                    <div class="card-body">
                        <div class="timeline-vertical">
                            @if($type == 'mutation')
                                <!-- Mutation Timeline -->
                                <div class="timeline-step {{ $proposal->status == 'diusulkan' ? 'active' : '' }} {{ in_array($proposal->status, ['disetujui', 'selesai', 'ditolak']) ? 'completed' : '' }}">
                                    <div class="timeline-icon">
                                        <i class="fas fa-paper-plane"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>Diusulkan</h6>
                                        <small class="text-muted">{{ $proposal->created_at->translatedFormat('d F Y H:i') }}</small>
                                    </div>
                                </div>
                                
                                <div class="timeline-step {{ $proposal->status == 'disetujui' ? 'active' : '' }} {{ in_array($proposal->status, ['selesai']) ? 'completed' : '' }}">
                                    <div class="timeline-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>Disetujui</h6>
                                        @if($proposal->status == 'disetujui' || $proposal->status == 'selesai')
                                            <small class="text-muted">{{ $proposal->updated_at->translatedFormat('d F Y H:i') }}</small>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="timeline-step {{ $proposal->status == 'selesai' ? 'active' : '' }}">
                                    <div class="timeline-icon">
                                        <i class="fas fa-check-double"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>Selesai</h6>
                                        @if($proposal->status == 'selesai')
                                            <small class="text-muted">Proses selesai</small>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($proposal->status == 'ditolak')
                                    <div class="timeline-step active rejected">
                                        <div class="timeline-icon">
                                            <i class="fas fa-times-circle"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6>Ditolak</h6>
                                            <small class="text-muted">{{ $proposal->updated_at->translatedFormat('d F Y H:i') }}</small>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <!-- Deletion Timeline -->
                                <div class="timeline-step {{ $proposal->status == 'diusulkan' ? 'active' : '' }} {{ in_array($proposal->status, ['disetujui', 'selesai', 'ditolak', 'dibatalkan']) ? 'completed' : '' }}">
                                    <div class="timeline-icon">
                                        <i class="fas fa-paper-plane"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>Diusulkan</h6>
                                        <small class="text-muted">{{ $proposal->proposed_at->translatedFormat('d F Y H:i') }}</small>
                                    </div>
                                </div>
                                
                                <div class="timeline-step {{ $proposal->status == 'diusulkan' && $proposal->verified_by ? 'completed' : '' }} {{ $proposal->status == 'diusulkan' && !$proposal->verified_by ? 'pending' : '' }}">
                                    <div class="timeline-icon">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>Diverifikasi</h6>
                                        @if($proposal->verified_by)
                                            <small class="text-muted">{{ $proposal->verified_at->translatedFormat('d F Y H:i') }}</small>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="timeline-step {{ $proposal->status == 'disetujui' ? 'active' : '' }} {{ $proposal->status == 'selesai' ? 'completed' : '' }}">
                                    <div class="timeline-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>Disetujui</h6>
                                        @if($proposal->approved_by)
                                            <small class="text-muted">{{ $proposal->approved_at->translatedFormat('d F Y H:i') }}</small>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="timeline-step {{ $proposal->status == 'selesai' ? 'active' : '' }}">
                                    <div class="timeline-icon">
                                        <i class="fas fa-check-double"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <h6>Selesai</h6>
                                        @if($proposal->status == 'selesai')
                                            <small class="text-muted">{{ $proposal->deleted_at->translatedFormat('d F Y H:i') }}</small>
                                        @endif
                                    </div>
                                </div>
                                
                                @if($proposal->status == 'ditolak')
                                    <div class="timeline-step active rejected">
                                        <div class="timeline-icon">
                                            <i class="fas fa-times-circle"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6>Ditolak</h6>
                                            <small class="text-muted">{{ $proposal->updated_at->translatedFormat('d F Y H:i') }}</small>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($proposal->status == 'dibatalkan')
                                    <div class="timeline-step active rejected">
                                        <div class="timeline-icon">
                                            <i class="fas fa-ban"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <h6>Dibatalkan</h6>
                                            <small class="text-muted">{{ $proposal->updated_at->translatedFormat('d F Y H:i') }}</small>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Timeline Tab -->
    <div class="tab-pane fade {{ ($tab ?? '') == 'timeline' ? 'show active' : '' }}" id="timeline">
        <div class="card-custom">
            <div class="card-header">
                <i class="fas fa-history me-2"></i> Timeline Proses
            </div>
            <div class="card-body">
                <div class="timeline">
                    @if($type == 'deletion' && isset($timeline))
                        @foreach($timeline as $item)
                            <div class="timeline-item mb-4">
                                <div class="d-flex">
                                    <div class="timeline-icon me-3">
                                        @php
                                            $icon = match($item['action']) {
                                                'Diusulkan' => 'paper-plane text-primary',
                                                'Diverifikasi' => 'check text-warning',
                                                'Disetujui' => 'check-circle text-success',
                                                'Selesai' => 'check-double text-info',
                                                default => 'circle text-secondary'
                                            };
                                        @endphp
                                        <i class="fas fa-{{ $icon }} fa-lg"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="mb-1">{{ $item['action'] }}</h6>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($item['date'])->translatedFormat('d F Y H:i') }}</small>
                                        </div>
                                        <p class="mb-1 text-muted small">
                                            <i class="fas fa-user me-1"></i>{{ $item['by'] }}
                                        </p>
                                        <p class="mb-0">{{ $item['description'] }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-history fa-2x mb-3"></i>
                            <p>Timeline tidak tersedia</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Documents Tab -->
    <div class="tab-pane fade {{ ($tab ?? '') == 'documents' ? 'show active' : '' }}" id="documents">
        <div class="card-custom">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <i class="fas fa-file-alt me-2"></i> Dokumen Terkait
                </div>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#uploadDocumentModal">
                    <i class="fas fa-upload me-1"></i> Upload Dokumen
                </button>
            </div>
            <div class="card-body">
                @if($type == 'mutation')
                    @if($proposal->supporting_documents && count($proposal->supporting_documents) > 0)
                        <div class="row">
                            @foreach($proposal->supporting_documents as $index => $doc)
                                <div class="col-md-6 mb-3">
                                    <div class="document-card p-3 border rounded">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <h6 class="mb-0">Dokumen {{ $index + 1 }}</h6>
                                                <small class="text-muted">Format: PDF</small>
                                            </div>
                                            <div class="btn-group">
                                                <a href="#" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm btn-outline-success">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar" style="width: 100%;"></div>
                                        </div>
                                        <small class="text-muted d-block mt-2">Uploaded: {{ $proposal->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-file-alt fa-2x mb-3"></i>
                            <p>Belum ada dokumen untuk proposal ini</p>
                        </div>
                    @endif
                @else
                    <!-- Deletion Documents -->
                    <div class="row">
                        <!-- Proposal Documents -->
                        <div class="col-md-6 mb-4">
                            <div class="card-custom h-100">
                                <div class="card-header">
                                    <i class="fas fa-file-upload me-2"></i> Dokumen Proposal
                                </div>
                                <div class="card-body">
                                    @if($proposal->proposal_documents && count($proposal->proposal_documents) > 0)
                                        @foreach($proposal->proposal_documents as $index => $doc)
                                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                                <div>
                                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                                    <span>{{ $doc }}</span>
                                                </div>
                                                <a href="#" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-3 text-muted">
                                            <i class="fas fa-file-alt fa-2x mb-2"></i>
                                            <p>Tidak ada dokumen proposal</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Approval Documents -->
                        <div class="col-md-6 mb-4">
                            <div class="card-custom h-100">
                                <div class="card-header">
                                    <i class="fas fa-file-check me-2"></i> Dokumen Persetujuan
                                </div>
                                <div class="card-body">
                                    @if($proposal->approval_documents && count($proposal->approval_documents) > 0)
                                        @foreach($proposal->approval_documents as $index => $doc)
                                            <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                                                <div>
                                                    <i class="fas fa-file-contract text-success me-2"></i>
                                                    <span>{{ $doc }}</span>
                                                </div>
                                                <a href="#" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="text-center py-3 text-muted">
                                            <i class="fas fa-file-check fa-2x mb-2"></i>
                                            <p>Tidak ada dokumen persetujuan</p>
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
</div>

<!-- Action Modals -->
@if($proposal->status == 'diusulkan')
    @if($type == 'deletion' && !$proposal->verified_by)
        <!-- Verification Modal -->
        <div class="modal fade" id="verifyModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.proposals.verify', ['type' => 'deletion', 'id' => $proposal->deletion_id]) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Verifikasi Proposal Penghapusan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Anda yakin ingin memverifikasi proposal penghapusan aset ini?</p>
                            <div class="alert alert-info">
                                <strong>Aset:</strong> {{ $proposal->asset->name }}<br>
                                <strong>Kode:</strong> {{ $proposal->asset->asset_code }}<br>
                                <strong>Nilai:</strong> {{ $proposal->asset->formatted_value }}<br>
                                <strong>Alasan:</strong> {{ $proposal->deletion_reason_display }}
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Verifikasi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.proposals.approve', ['type' => $type, 'id' => $type == 'mutation' ? $proposal->mutation_id : $proposal->deletion_id]) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Setujui Proposal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Anda yakin ingin menyetujui proposal ini?</p>
                        
                        @if($type == 'deletion')
                            <div class="mb-3">
                                <label class="form-label">Dokumen Persetujuan (opsional)</label>
                                <div id="approvalDocuments">
                                    <input type="text" name="approval_documents[]" class="form-control form-control-sm mb-2" placeholder="Nama dokumen">
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary mt-2" onclick="addApprovalDocument()">
                                    <i class="fas fa-plus me-1"></i> Tambah Dokumen
                                </button>
                                <small class="text-muted d-block mt-1">Tambahkan dokumen pendukung persetujuan</small>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success">Setujui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.proposals.reject', ['type' => $type, 'id' => $type == 'mutation' ? $proposal->mutation_id : $proposal->deletion_id]) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Tolak Proposal</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan</label>
                            <textarea name="rejection_reason" class="form-control" rows="3" required 
                                      placeholder="Berikan alasan penolakan proposal..."></textarea>
                            <small class="text-muted">Minimal 10 karakter</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Tolak</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    @if($type == 'deletion')
        <!-- Cancel Modal -->
        <div class="modal fade" id="cancelModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.proposals.cancel', ['type' => 'deletion', 'id' => $proposal->deletion_id]) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Batalkan Proposal Penghapusan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Alasan Pembatalan</label>
                                <textarea name="cancellation_reason" class="form-control" rows="3" required 
                                          placeholder="Berikan alasan pembatalan proposal..."></textarea>
                                <small class="text-muted">Minimal 10 karakter</small>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-secondary">Batalkan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endif

@if($type == 'deletion' && $proposal->status == 'disetujui')
    <!-- Complete Modal -->
    <div class="modal fade" id="completeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.proposals.complete', ['type' => 'deletion', 'id' => $proposal->deletion_id]) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Selesaikan Penghapusan Aset</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Metode Penghapusan</label>
                            <select name="deletion_method" class="form-select" required>
                                <option value="">Pilih Metode</option>
                                <option value="jual">Dijual</option>
                                <option value="hibah">Dihibahkan</option>
                                <option value="musnah">Musnah</option>
                                <option value="scrap">Scrap/Daur Ulang</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nilai Penjualan (jika dijual)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="sale_value" class="form-control" 
                                       placeholder="0" min="0">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Penerima (jika dihibahkan/dijual)</label>
                            <input type="text" name="recipient" class="form-control" 
                                   placeholder="Nama penerima">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Catatan Penyelesaian</label>
                            <textarea name="completion_notes" class="form-control" rows="3" 
                                      placeholder="Catatan proses penyelesaian..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Selesaikan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<!-- Upload Document Modal -->
<div class="modal fade" id="uploadDocumentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="#" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Upload Dokumen</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Jenis Dokumen</label>
                        <select name="document_type" class="form-select" required>
                            <option value="">Pilih Jenis</option>
                            <option value="pendukung">Dokumen Pendukung</option>
                            <option value="persetujuan">Dokumen Persetujuan</option>
                            <option value="laporan">Laporan</option>
                            <option value="lainnya">Lainnya</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">File</label>
                        <input type="file" name="file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" required>
                        <small class="text-muted">Format: PDF, JPG, PNG, DOC (Max: 5MB)</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2" 
                                  placeholder="Deskripsi dokumen..."></textarea>
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

@push('styles')
<style>
    .timeline-vertical {
        position: relative;
        padding-left: 30px;
    }
    
    .timeline-vertical::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #e9ecef;
    }
    
    .timeline-step {
        position: relative;
        margin-bottom: 20px;
    }
    
    .timeline-step:last-child {
        margin-bottom: 0;
    }
    
    .timeline-step.completed .timeline-icon {
        background-color: #28a745;
        color: white;
    }
    
    .timeline-step.active .timeline-icon {
        background-color: #007bff;
        color: white;
        box-shadow: 0 0 0 5px rgba(0, 123, 255, 0.2);
    }
    
    .timeline-step.pending .timeline-icon {
        background-color: #6c757d;
        color: white;
    }
    
    .timeline-step.rejected .timeline-icon {
        background-color: #dc3545;
        color: white;
    }
    
    .timeline-icon {
        position: absolute;
        left: -30px;
        top: 0;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #e9ecef;
        color: #6c757d;
        z-index: 1;
    }
    
    .timeline-content {
        padding-left: 10px;
    }
    
    .document-card {
        transition: all 0.3s;
    }
    
    .document-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
</style>
@endpush

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
    
    function addApprovalDocument() {
        const container = document.getElementById('approvalDocuments');
        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'approval_documents[]';
        input.className = 'form-control form-control-sm mb-2';
        input.placeholder = 'Nama dokumen';
        container.appendChild(input);
    }
</script>
@endpush
@endsection