@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-0">
                    <i class="fas fa-clipboard-check me-2"></i>Detail Laporan Audit
                    <small class="text-muted">#{{ $audit->audit_id }}</small>
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.audits.index') }}">Audit</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.audits.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
                @if($audit->report_file_path)
                    <a href="{{ route('admin.audits.download-file', $audit) }}" 
                       class="btn btn-outline-success btn-sm">
                        <i class="fas fa-download me-1"></i> Download File
                    </a>
                @endif
                <button type="button" class="btn btn-primary btn-sm" 
                        data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                    <i class="fas fa-sync-alt me-1"></i> Update Status
                </button>
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
                        'findings' => 'search',
                        'timeline' => 'history',
                        default => 'circle'
                    };
                @endphp
                <i class="fas fa-{{ $icon }} me-1"></i>
                {{ ucfirst(str_replace('_', ' ', $tabName)) }}
            </button>
        </li>
    @endforeach
</ul>

<!-- Tab Content -->
<div class="tab-content">
    <!-- Detail Tab -->
    <div class="tab-pane fade {{ $tab == 'detail' ? 'show active' : '' }}" id="detail">
        <div class="row">
            <!-- Left Column: Audit Details -->
            <div class="col-lg-8">
                <div class="card-custom mb-4">
                    <div class="card-header">
                        <i class="fas fa-info-circle me-2"></i> Informasi Audit
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Aset yang Diaudit</label>
                                <p class="mb-0">
                                    <strong>{{ $audit->asset->name ?? 'Aset tidak ditemukan' }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $audit->asset->asset_code ?? '' }}</small>
                                </p>
                                @if($audit->asset)
                                    <a href="{{ route('admin.assets.show', $audit->asset) }}" 
                                       class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="fas fa-external-link-alt me-1"></i> Lihat Aset
                                    </a>
                                @endif
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Status Audit</label>
                                @php
                                    $statusClass = match($audit->status) {
                                        'completed' => 'success',
                                        'follow_up' => 'warning',
                                        'submitted' => 'info',
                                        'reviewed' => 'primary',
                                        'draft' => 'secondary',
                                        'cancelled' => 'danger',
                                        default => 'secondary'
                                    };
                                @endphp
                                <p class="mb-0">
                                    <span class="badge bg-{{ $statusClass }} fs-6">{{ $audit->status_display }}</span>
                                </p>
                                @if($audit->follow_up_deadline)
                                    <small class="text-muted d-block mt-1">
                                        Deadline: {{ $audit->follow_up_deadline->translatedFormat('d F Y') }}
                                        @if($audit->follow_up_deadline->isPast() && $audit->status == 'follow_up')
                                            <span class="text-danger"> (Terlambat)</span>
                                        @endif
                                    </small>
                                @endif
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Auditor</label>
                                <p class="mb-0">{{ $audit->auditor->name ?? '-' }}</p>
                                <small class="text-muted">ID: {{ $audit->auditor_id }}</small>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted">Tanggal Audit</label>
                                <p class="mb-0">{{ $audit->audit_date->translatedFormat('d F Y') }}</p>
                                <small class="text-muted">{{ $audit->created_at->diffForHumans() }}</small>
                            </div>
                            
                            @if($audit->report_file_path)
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">File Laporan</label>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-file-pdf fa-2x text-danger me-3"></i>
                                        <div>
                                            <p class="mb-0">Laporan Audit.pdf</p>
                                            <small class="text-muted">{{ $audit->created_at->translatedFormat('d F Y') }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Asset Information -->
                @if($audit->asset)
                    <div class="card-custom mb-4">
                        <div class="card-header">
                            <i class="fas fa-cube me-2"></i> Informasi Aset
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">OPD Pemilik</label>
                                    <p class="mb-0">{{ $audit->asset->opdUnit->nama_opd ?? '-' }}</p>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Kategori</label>
                                    <p class="mb-0">{{ $audit->asset->category->name ?? '-' }}</p>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Nilai Aset</label>
                                    <p class="mb-0"><strong>{{ $audit->asset->formatted_value }}</strong></p>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Kondisi</label>
                                    <span class="badge {{ match($audit->asset->condition) {
                                        'Baik' => 'bg-success',
                                        'Rusak Ringan' => 'bg-warning',
                                        'Rusak Berat' => 'bg-danger',
                                        default => 'bg-secondary'
                                    } }}">
                                        {{ $audit->asset->condition }}
                                    </span>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Status Aset</label>
                                    <span class="badge {{ match($audit->asset->status) {
                                        'aktif' => 'bg-success',
                                        'dimutasi' => 'bg-warning',
                                        'dihapus' => 'bg-danger',
                                        default => 'bg-secondary'
                                    } }}">
                                        {{ ucfirst($audit->asset->status) }}
                                    </span>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted">Verifikasi Dokumen</label>
                                    <span class="badge {{ match($audit->asset->document_verification_status) {
                                        'valid' => 'bg-success',
                                        'tidak_valid' => 'bg-danger',
                                        default => 'bg-warning'
                                    } }}">
                                        {{ str_replace('_', ' ', $audit->asset->document_verification_status) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Right Column: Actions & Timeline -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card-custom mb-4">
                    <div class="card-header">
                        <i class="fas fa-bolt me-2"></i> Aksi Cepat
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if($audit->status == 'follow_up')
                                <button type="button" class="btn btn-warning"
                                        data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                                    <i class="fas fa-check-circle me-1"></i> Tandai Selesai
                                </button>
                            @endif
                            
                            @if($audit->report_file_path)
                                <a href="{{ route('admin.audits.download-file', $audit) }}" 
                                   class="btn btn-outline-success">
                                    <i class="fas fa-download me-1"></i> Download File
                                </a>
                            @endif
                            
                            <a href="{{ route('admin.assets.show', $audit->asset) }}" 
                               class="btn btn-outline-primary">
                                <i class="fas fa-external-link-alt me-1"></i> Lihat Aset
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Follow Up Information -->
                @if($audit->follow_up || $audit->follow_up_deadline)
                    <div class="card-custom">
                        <div class="card-header">
                            <i class="fas fa-exclamation-triangle me-2"></i> Tindak Lanjut
                        </div>
                        <div class="card-body">
                            @if($audit->follow_up)
                                <div class="mb-3">
                                    <label class="form-label text-muted">Rekomendasi</label>
                                    <p class="mb-0">{{ $audit->follow_up }}</p>
                                </div>
                            @endif
                            
                            @if($audit->follow_up_deadline)
                                <div class="mb-3">
                                    <label class="form-label text-muted">Deadline</label>
                                    <p class="mb-0 {{ $audit->follow_up_deadline->isPast() && $audit->status == 'follow_up' ? 'text-danger' : '' }}">
                                        {{ $audit->follow_up_deadline->translatedFormat('d F Y') }}
                                        @if($audit->follow_up_deadline->isPast() && $audit->status == 'follow_up')
                                            <br><small class="text-danger">Terlambat {{ $audit->follow_up_deadline->diffForHumans() }}</small>
                                        @endif
                                    </p>
                                </div>
                            @endif
                            
                            @if($audit->status == 'follow_up')
                                <div class="alert alert-warning small mb-0">
                                    <i class="fas fa-clock me-1"></i>
                                    Audit memerlukan tindak lanjut
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Findings Tab -->
    <div class="tab-pane fade {{ $tab == 'findings' ? 'show active' : '' }}" id="findings">
        <div class="card-custom">
            <div class="card-header">
                <i class="fas fa-search me-2"></i> Temuan Audit
            </div>
            <div class="card-body">
                <div class="findings-content">
                    {!! nl2br(e($audit->findings)) !!}
                </div>
                
                @if($audit->follow_up)
                    <div class="mt-4 pt-4 border-top">
                        <h5><i class="fas fa-exclamation-triangle me-2 text-warning"></i> Rekomendasi Tindak Lanjut</h5>
                        <div class="alert alert-warning">
                            {!! nl2br(e($audit->follow_up)) !!}
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Timeline Tab -->
    <div class="tab-pane fade {{ $tab == 'timeline' ? 'show active' : '' }}" id="timeline">
        <div class="card-custom">
            <div class="card-header">
                <i class="fas fa-history me-2"></i> Timeline Audit
            </div>
            <div class="card-body">
                <div class="timeline">
                    @if($audit->timeline && count($audit->timeline) > 0)
                        @foreach($audit->timeline as $item)
                            <div class="timeline-item mb-4">
                                <div class="d-flex">
                                    <div class="timeline-icon me-3">
                                        @php
                                            $icon = match($item['action']) {
                                                'Dibuat' => 'plus-circle text-success',
                                                'Diupdate' => 'edit text-primary',
                                                'Diselesaikan' => 'check-circle text-info',
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
                                            <i class="fas fa-user me-1"></i>{{ $item['user'] ?? 'System' }}
                                        </p>
                                        @if($item['description'])
                                            <p class="mb-0">{{ $item['description'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="timeline-item">
                            <div class="d-flex">
                                <div class="timeline-icon me-3">
                                    <i class="fas fa-plus-circle text-success fa-lg"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1">Dibuat</h6>
                                        <small class="text-muted">{{ $audit->created_at->translatedFormat('d F Y H:i') }}</small>
                                    </div>
                                    <p class="mb-1 text-muted small">
                                        <i class="fas fa-user me-1"></i>{{ $audit->auditor->name ?? 'System' }}
                                    </p>
                                    <p class="mb-0">Laporan audit dibuat</p>
                                </div>
                            </div>
                        </div>
                        
                        @if($audit->updated_at != $audit->created_at)
                            <div class="timeline-item">
                                <div class="d-flex">
                                    <div class="timeline-icon me-3">
                                        <i class="fas fa-edit text-primary fa-lg"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between">
                                            <h6 class="mb-1">Diupdate</h6>
                                            <small class="text-muted">{{ $audit->updated_at->translatedFormat('d F Y H:i') }}</small>
                                        </div>
                                        <p class="mb-0">Status: {{ $audit->status_display }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.audits.update-status', $audit) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Update Status Audit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="draft" {{ $audit->status == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="submitted" {{ $audit->status == 'submitted' ? 'selected' : '' }}>Submitted</option>
                            <option value="follow_up" {{ $audit->status == 'follow_up' ? 'selected' : '' }}>Follow Up</option>
                            <option value="completed" {{ $audit->status == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ $audit->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan Tindak Lanjut</label>
                        <textarea name="follow_up" class="form-control" rows="3">{{ $audit->follow_up ?? '' }}</textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Deadline Tindak Lanjut</label>
                        <input type="date" name="follow_up_deadline" class="form-control" 
                               value="{{ $audit->follow_up_deadline ? $audit->follow_up_deadline->format('Y-m-d') : '' }}">
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

@push('styles')
<style>
    .findings-content {
        line-height: 1.8;
        font-size: 15px;
    }
    
    .findings-content h1, .findings-content h2, .findings-content h3, .findings-content h4 {
        margin-top: 1.5rem;
        margin-bottom: 1rem;
        color: #2c3e50;
    }
    
    .findings-content p {
        margin-bottom: 1rem;
    }
    
    .findings-content ul, .findings-content ol {
        padding-left: 2rem;
        margin-bottom: 1rem;
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
</script>
@endpush
@endsection