@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-3 mb-4 border-bottom">
        <h1 class="h3">
            @if($type == 'deletion')
                <i class="fas fa-trash-alt me-2"></i> Detail Proposal Penghapusan
            @elseif($type == 'mutation')
                <i class="fas fa-exchange-alt me-2"></i> Detail Mutasi Aset
            @else
                <i class="fas fa-tools me-2"></i> Detail Pemeliharaan
            @endif
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('opd.transactions.index', ['tab' => $type]) }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Main Information Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-info-circle me-2"></i> Informasi Utama
                </div>
                <div class="card-body">
                    @if($type == 'deletion')
                        <!-- Deletion Details -->
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Aset</h6>
                                <div class="d-flex align-items-start mb-3">
                                    <div class="flex-grow-1">
                                        <h5>{{ $data->asset->name }}</h5>
                                        <p class="mb-1">
                                            <strong>Kode:</strong> {{ $data->asset->asset_code }}
                                            @if($data->asset->asset_code_old)
                                                <br>
                                                <small class="text-muted">Lama: {{ $data->asset->asset_code_old }}</small>
                                            @endif
                                        </p>
                                        <p class="mb-1">
                                            <strong>Kategori:</strong> {{ $data->asset->category->name ?? '-' }}
                                            ({{ $data->asset->category->kib_code ?? '-' }})
                                        </p>
                                        <p class="mb-0">
                                            <strong>Nilai:</strong> {{ $data->asset->formatted_value }}
                                        </p>
                                    </div>
                                    <a href="{{ route('opd.assets.show', $data->asset) }}" class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6>Status Proposal</h6>
                                <div class="mb-3">
                                    <span class="badge bg-{{ getDeletionStatusColor($data->status) }} fs-6">
                                        {{ $data->getStatusDisplayAttribute() }}
                                    </span>
                                </div>
                                
                                <table class="table table-sm">
                                    <tr>
                                        <td>Diusulkan:</td>
                                        <td class="text-end">
                                            {{ $data->proposed_at ? \Carbon\Carbon::parse($data->proposed_at)->format('d/m/Y H:i') : '-' }}
                                            <br>
                                            <small class="text-muted">Oleh: {{ $data->proposer->name ?? '-' }}</small>
                                        </td>
                                    </tr>
                                    @if($data->verified_at)
                                        <tr>
                                            <td>Diverifikasi:</td>
                                            <td class="text-end">
                                                {{ \Carbon\Carbon::parse($data->verified_at)->format('d/m/Y H:i') }}
                                                <br>
                                                <small class="text-muted">Oleh: {{ $data->verifier->name ?? '-' }}</small>
                                            </td>
                                        </tr>
                                    @endif
                                    @if($data->approved_at)
                                        <tr>
                                            <td>Disetujui:</td>
                                            <td class="text-end">
                                                {{ \Carbon\Carbon::parse($data->approved_at)->format('d/m/Y H:i') }}
                                                <br>
                                                <small class="text-muted">Oleh: {{ $data->approver->name ?? '-' }}</small>
                                            </td>
                                        </tr>
                                    @endif
                                    @if($data->deleted_at)
                                        <tr>
                                            <td>Selesai:</td>
                                            <td class="text-end">
                                                {{ \Carbon\Carbon::parse($data->deleted_at)->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Alasan Penghapusan</h6>
                                <div class="alert alert-{{ $data->deletion_reason == 'rusak_berat' ? 'danger' : ($data->deletion_reason == 'hilang' ? 'warning' : 'info') }}">
                                    <h5 class="alert-heading">{{ $data->getDeletionReasonDisplayAttribute() }}</h5>
                                    <p class="mb-0">{{ $data->reason_details }}</p>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6>Detail Eksekusi</h6>
                                <table class="table table-sm">
                                    @if($data->deletion_method)
                                        <tr>
                                            <td>Metode:</td>
                                            <td class="text-end">
                                                @php
                                                    $methodNames = [
                                                        'jual' => 'Dijual',
                                                        'hibah' => 'Dihibahkan',
                                                        'musnah' => 'Dimusnahkan',
                                                        'scrap' => 'Dibuang/Scrap'
                                                    ];
                                                @endphp
                                                {{ $methodNames[$data->deletion_method] ?? $data->deletion_method }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if($data->sale_value)
                                        <tr>
                                            <td>Nilai Penjualan:</td>
                                            <td class="text-end fw-bold">{{ $data->getFormattedSaleValueAttribute() }}</td>
                                        </tr>
                                    @endif
                                    @if($data->recipient)
                                        <tr>
                                            <td>Penerima:</td>
                                            <td class="text-end">{{ $data->recipient }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                        
                    @elseif($type == 'mutation')
                        <!-- Mutation Details -->
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Aset</h6>
                                <div class="d-flex align-items-start mb-3">
                                    <div class="flex-grow-1">
                                        <h5>{{ $data->asset->name }}</h5>
                                        <p class="mb-1">
                                            <strong>Kode:</strong> {{ $data->asset->asset_code }}
                                        </p>
                                        <p class="mb-1">
                                            <strong>Kategori:</strong> {{ $data->asset->category->name ?? '-' }}
                                        </p>
                                        <p class="mb-0">
                                            <strong>Nilai:</strong> {{ $data->asset->formatted_value }}
                                        </p>
                                    </div>
                                    <a href="{{ route('opd.assets.show', $data->asset) }}" class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                                
                                <h6>Status Mutasi</h6>
                                <div class="mb-3">
                                    <span class="badge bg-{{ getMutationStatusColor($data->status) }} fs-6">
                                        {{ ucfirst($data->status) }}
                                    </span>
                                </div>
                                
                                <table class="table table-sm">
                                    <tr>
                                        <td>Diusulkan:</td>
                                        <td class="text-end">
                                            {{ \Carbon\Carbon::parse($data->mutation_date)->format('d/m/Y') }}
                                            <br>
                                            <small class="text-muted">Oleh: {{ $data->mutator->name ?? '-' }}</small>
                                        </td>
                                    </tr>
                                    @if($data->status == 'selesai')
                                        <tr>
                                            <td>Selesai:</td>
                                            <td class="text-end">
                                                {{ $data->updated_at->format('d/m/Y H:i') }}
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            
                            <div class="col-md-6">
                                <h6>Rute Mutasi</h6>
                                <div class="mutation-route">
                                    <div class="route-step">
                                        <div class="step-icon bg-primary">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div class="step-content">
                                            <h6>Dari OPD</h6>
                                            <p class="mb-1 fw-bold">{{ $data->fromOpdUnit->nama_opd }}</p>
                                            @if($data->fromLocation)
                                                <p class="mb-0 text-muted">{{ $data->fromLocation->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="route-arrow">
                                        <i class="fas fa-arrow-right"></i>
                                    </div>
                                    
                                    <div class="route-step">
                                        <div class="step-icon bg-success">
                                            <i class="fas fa-building"></i>
                                        </div>
                                        <div class="step-content">
                                            <h6>Ke OPD</h6>
                                            <p class="mb-1 fw-bold">{{ $data->toOpdUnit->nama_opd }}</p>
                                            @if($data->toLocation)
                                                <p class="mb-0 text-muted">{{ $data->toLocation->name }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        @if($canAccept && Auth::user()->opd_unit_id == $data->to_opd_unit_id)
                            <hr>
                            <div class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i> Tindakan Diperlukan</h6>
                                <p>Mutasi ini telah disetujui dan menunggu konfirmasi penerimaan dari OPD Anda.</p>
                                <button class="btn btn-success" onclick="acceptMutation({{ $data->mutation_id }})">
                                    <i class="fas fa-check me-1"></i> Terima Aset
                                </button>
                            </div>
                        @endif
                        
                    @elseif($type == 'maintenance')
                        <!-- Maintenance Details -->
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Aset</h6>
                                <div class="d-flex align-items-start mb-3">
                                    <div class="flex-grow-1">
                                        <h5>{{ $data->asset->name }}</h5>
                                        <p class="mb-1">
                                            <strong>Kode:</strong> {{ $data->asset->asset_code }}
                                        </p>
                                        <p class="mb-1">
                                            <strong>Kategori:</strong> {{ $data->asset->category->name ?? '-' }}
                                        </p>
                                        <p class="mb-0">
                                            <strong>Kondisi:</strong> 
                                            <span class="badge bg-{{ $data->asset->condition == 'Baik' ? 'success' : ($data->asset->condition == 'Rusak Ringan' ? 'warning' : 'danger') }}">
                                                {{ $data->asset->condition }}
                                            </span>
                                        </p>
                                    </div>
                                    <a href="{{ route('opd.assets.show', $data->asset) }}" class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                                
                                <h6>Status Pemeliharaan</h6>
                                <div class="mb-3">
                                    @php
                                        $statusBadges = [
                                            'dijadwalkan' => 'warning',
                                            'dalam_pengerjaan' => 'info',
                                            'selesai' => 'success',
                                            'dibatalkan' => 'danger',
                                            'ditunda' => 'secondary'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusBadges[$data->status] ?? 'secondary' }} fs-6">
                                        {{ ucfirst(str_replace('_', ' ', $data->status)) }}
                                    </span>
                                </div>
                                
                                <table class="table table-sm">
                                    <tr>
                                        <td>Dijadwalkan:</td>
                                        <td class="text-end">
                                            {{ \Carbon\Carbon::parse($data->scheduled_date)->format('d/m/Y') }}
                                            <br>
                                            <small class="text-muted">Oleh: {{ $data->recorder->name ?? '-' }}</small>
                                        </td>
                                    </tr>
                                    @if($data->actual_date)
                                        <tr>
                                            <td>Tanggal Aktual:</td>
                                            <td class="text-end">
                                                {{ \Carbon\Carbon::parse($data->actual_date)->format('d/m/Y') }}
                                            </td>
                                        </tr>
                                    @endif
                                    @if($data->approved_at)
                                        <tr>
                                            <td>Disetujui:</td>
                                            <td class="text-end">
                                                {{ \Carbon\Carbon::parse($data->approved_at)->format('d/m/Y H:i') }}
                                                <br>
                                                <small class="text-muted">Oleh: {{ $data->approver->name ?? '-' }}</small>
                                            </td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                            
                            <div class="col-md-6">
                                <h6>Detail Pemeliharaan</h6>
                                <table class="table">
                                    <tr>
                                        <td>Jenis:</td>
                                        <td class="fw-bold">
                                            @php
                                                $types = [
                                                    'rutin' => 'Pemeliharaan Rutin',
                                                    'perbaikan' => 'Perbaikan',
                                                    'kalibrasi' => 'Kalibrasi',
                                                    'penggantian' => 'Penggantian',
                                                    'lainnya' => 'Lainnya'
                                                ];
                                            @endphp
                                            {{ $types[$data->maintenance_type] ?? $data->maintenance_type }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Judul:</td>
                                        <td class="fw-bold">{{ $data->title }}</td>
                                    </tr>
                                    <tr>
                                        <td>Deskripsi:</td>
                                        <td>{{ $data->description ?: '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Biaya:</td>
                                        <td>
                                            @if($data->cost)
                                                <span class="fw-bold">Rp {{ number_format($data->cost, 0, ',', '.') }}</span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    @if($data->vendor)
                                        <tr>
                                            <td>Vendor:</td>
                                            <td>{{ $data->vendor }}</td>
                                        </tr>
                                    @endif
                                    @if($data->vendor_contact)
                                        <tr>
                                            <td>Kontak Vendor:</td>
                                            <td>{{ $data->vendor_contact }}</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                        
                        @if($data->result_status)
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <h6>Hasil Pemeliharaan</h6>
                                    <div class="alert alert-{{ $data->result_status == 'baik' ? 'success' : ($data->result_status == 'perlu_perbaikan' ? 'warning' : 'danger') }}">
                                        <h6 class="alert-heading">
                                            Status: 
                                            <span class="text-uppercase">{{ $data->result_status }}</span>
                                        </h6>
                                        @if($data->result_notes)
                                            <p class="mb-0">{{ $data->result_notes }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                    
                    @if($data->notes)
                        <hr>
                        <div class="row">
                            <div class="col-md-12">
                                <h6>Catatan</h6>
                                <div class="alert alert-light">
                                    {{ $data->notes }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Timeline Card -->
            @if($type == 'deletion' && isset($timeline))
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-history me-2"></i> Timeline
                    </div>
                    <div class="card-body">
                        <div class="timeline">
                            @foreach($timeline as $item)
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-{{ $item['action'] == 'Selesai' ? 'success' : ($item['action'] == 'Ditolak' ? 'danger' : 'primary') }}"></div>
                                    <div class="timeline-content">
                                        <div class="timeline-header">
                                            <h6 class="mb-0">{{ $item['action'] }}</h6>
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($item['date'])->format('d/m/Y H:i') }}</small>
                                        </div>
                                        <p class="mb-1">{{ $item['description'] }}</p>
                                        <small class="text-muted">Oleh: {{ $item['by'] }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Documents Card -->
            @if(($type == 'deletion' && ($data->proposal_documents || $data->approval_documents)) || 
                ($type == 'mutation' && $data->supporting_documents) ||
                ($type == 'maintenance' && $data->supporting_documents))
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-file-alt me-2"></i> Dokumen
                    </div>
                    <div class="card-body">
                        @if($type == 'deletion')
                            @if($data->proposal_documents)
                                <h6>Dokumen Proposal</h6>
                                <div class="row">
                                    @foreach($data->proposal_documents as $document)
                                        <div class="col-md-4 mb-3">
                                            <div class="card document-card">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-file-pdf fa-2x text-danger mb-2"></i>
                                                    <p class="mb-1">{{ basename($document) }}</p>
                                                    <a href="{{ Storage::url($document) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download me-1"></i> Unduh
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            
                            @if($data->approval_documents)
                                <hr>
                                <h6>Dokumen Persetujuan</h6>
                                <div class="row">
                                    @foreach($data->approval_documents as $document)
                                        <div class="col-md-4 mb-3">
                                            <div class="card document-card">
                                                <div class="card-body text-center">
                                                    <i class="fas fa-file-pdf fa-2x text-success mb-2"></i>
                                                    <p class="mb-1">{{ basename($document) }}</p>
                                                    <a href="{{ Storage::url($document) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-download me-1"></i> Unduh
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            
                        @elseif(($type == 'mutation' || $type == 'maintenance') && $data->supporting_documents)
                            <div class="row">
                                @foreach($data->supporting_documents as $document)
                                    <div class="col-md-4 mb-3">
                                        <div class="card document-card">
                                            <div class="card-body text-center">
                                                <i class="fas fa-file-{{ pathinfo($document, PATHINFO_EXTENSION) == 'pdf' ? 'pdf text-danger' : 'image text-primary' }} fa-2x mb-2"></i>
                                                <p class="mb-1">{{ basename($document) }}</p>
                                                <a href="{{ Storage::url($document) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-download me-1"></i> Unduh
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-lg-4">
            <!-- Actions Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-cogs me-2"></i> Tindakan
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($type == 'deletion')
                            @if($data->status == 'diusulkan' && Auth::user()->isAdminUtama())
                                <button type="button" class="btn btn-success" onclick="approveDeletion({{ $data->deletion_id }})">
                                    <i class="fas fa-check me-2"></i> Setujui
                                </button>
                                <button type="button" class="btn btn-danger" onclick="rejectDeletion({{ $data->deletion_id }})">
                                    <i class="fas fa-times me-2"></i> Tolak
                                </button>
                            @endif
                            
                            @if(in_array($data->status, ['diusulkan', 'disetujui']) && $data->proposed_by == Auth::id())
                                <button type="button" class="btn btn-warning" onclick="cancelDeletion({{ $data->deletion_id }})">
                                    <i class="fas fa-ban me-2"></i> Batalkan
                                </button>
                            @endif
                            
                            @if($data->status == 'disetujui' && Auth::user()->isAdminUtama())
                                <button type="button" class="btn btn-primary" onclick="completeDeletion({{ $data->deletion_id }})">
                                    <i class="fas fa-check-double me-2"></i> Tandai Selesai
                                </button>
                            @endif
                            
                        @elseif($type == 'mutation')
                            @if($data->status == 'diusulkan' && Auth::user()->isAdminUtama())
                                <button type="button" class="btn btn-success" onclick="approveMutation({{ $data->mutation_id }})">
                                    <i class="fas fa-check me-2"></i> Setujui
                                </button>
                                <button type="button" class="btn btn-danger" onclick="rejectMutation({{ $data->mutation_id }})">
                                    <i class="fas fa-times me-2"></i> Tolak
                                </button>
                            @endif
                            
                            @if($data->status == 'diusulkan' && $data->from_opd_unit_id == Auth::user()->opd_unit_id)
                                <button type="button" class="btn btn-warning" onclick="cancelMutation({{ $data->mutation_id }})">
                                    <i class="fas fa-ban me-2"></i> Batalkan
                                </button>
                            @endif
                            
                            @if($canAccept && Auth::user()->opd_unit_id == $data->to_opd_unit_id)
                                <button type="button" class="btn btn-primary" onclick="acceptMutation({{ $data->mutation_id }})">
                                    <i class="fas fa-check me-2"></i> Terima Aset
                                </button>
                            @endif
                            
                        @elseif($type == 'maintenance')
                            @if($data->status == 'dijadwalkan')
                                <button type="button" class="btn btn-info" onclick="startMaintenance({{ $data->maintenance_id }})">
                                    <i class="fas fa-play me-2"></i> Mulai Pengerjaan
                                </button>
                                <button type="button" class="btn btn-warning" onclick="cancelMaintenance({{ $data->maintenance_id }})">
                                    <i class="fas fa-ban me-2"></i> Batalkan
                                </button>
                            @elseif($data->status == 'dalam_pengerjaan')
                                <button type="button" class="btn btn-success" onclick="completeMaintenance({{ $data->maintenance_id }})">
                                    <i class="fas fa-check me-2"></i> Tandai Selesai
                                </button>
                            @endif
                        @endif
                        
                        <hr>
                        
                        <button type="button" class="btn btn-outline-secondary" onclick="printPage()">
                            <i class="fas fa-print me-2"></i> Cetak
                        </button>
                        
                        <button type="button" class="btn btn-outline-secondary" onclick="exportDetails()">
                            <i class="fas fa-file-export me-2"></i> Ekspor
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Related Transactions Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-link me-2"></i> Transaksi Terkait
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Histori Aset</span>
                            <a href="{{ route('opd.assets.show', $data->asset_id) }}?tab=histories" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-history"></i>
                            </a>
                        </li>
                        
                        @if($type == 'deletion')
                            @if($data->asset->mutations->count() > 0)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Mutasi Sebelumnya</span>
                                    <span class="badge bg-info">{{ $data->asset->mutations->count() }}</span>
                                </li>
                            @endif
                            
                            @if($data->asset->maintenances->count() > 0)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Riwayat Pemeliharaan</span>
                                    <span class="badge bg-warning">{{ $data->asset->maintenances->count() }}</span>
                                </li>
                            @endif
                            
                        @elseif($type == 'mutation')
                            @if($data->asset->deletions->where('status', '!=', 'ditolak')->count() > 0)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Proposal Penghapusan</span>
                                    <span class="badge bg-danger">{{ $data->asset->deletions->where('status', '!=', 'ditolak')->count() }}</span>
                                </li>
                            @endif
                        @endif
                    </ul>
                </div>
            </div>
            
            <!-- System Information Card -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-database me-2"></i> Informasi Sistem
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td>ID Transaksi:</td>
                            <td class="text-end">
                                @if($type == 'deletion')
                                    DEL-{{ str_pad($data->deletion_id, 6, '0', STR_PAD_LEFT) }}
                                @elseif($type == 'mutation')
                                    MUT-{{ str_pad($data->mutation_id, 6, '0', STR_PAD_LEFT) }}
                                @else
                                    MNT-{{ str_pad($data->maintenance_id, 6, '0', STR_PAD_LEFT) }}
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Dibuat:</td>
                            <td class="text-end">{{ $data->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td>Diperbarui:</td>
                            <td class="text-end">{{ $data->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @if($type == 'deletion' && $data->deleted_at)
                            <tr>
                                <td>Dihapus:</td>
                                <td class="text-end">{{ $data->deleted_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endif
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .timeline {
            position: relative;
            padding-left: 30px;
        }
        
        .timeline::before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #dee2e6;
        }
        
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        
        .timeline-marker {
            position: absolute;
            left: -26px;
            top: 0;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            border: 2px solid white;
            box-shadow: 0 0 0 3px #dee2e6;
        }
        
        .timeline-content {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .timeline-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
        }
        
        .mutation-route {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .route-step {
            text-align: center;
            flex: 1;
        }
        
        .step-icon {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
            color: white;
            font-size: 20px;
        }
        
        .route-arrow {
            padding: 0 20px;
            color: #6c757d;
            font-size: 24px;
        }
        
        .document-card {
            transition: transform 0.2s;
            height: 100%;
        }
        
        .document-card:hover {
            transform: translateY(-5px);
        }
        
        .document-card .card-body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 150px;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Print page
        function printPage() {
            window.print();
        }
        
        // Export details
        function exportDetails() {
            const format = prompt('Pilih format ekspor:\n1. PDF\n2. Excel', 'pdf');
            if (format) {
                showToast(`Mengekspor detail ke format ${format.toUpperCase()}...`, 'info');
                // Implement export logic here
            }
        }
        
        // Deletion actions
        function approveDeletion(deletionId) {
            if (!confirm('Setujui proposal penghapusan ini?')) return;
            
            $.ajax({
                url: '{{ url("opd/transactions/deletions") }}/' + deletionId + '/approve',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: handleAjaxError
            });
        }
        
        function rejectDeletion(deletionId) {
            const reason = prompt('Alasan penolakan:');
            if (!reason) return;
            
            $.ajax({
                url: '{{ url("opd/transactions/deletions") }}/' + deletionId + '/reject',
                method: 'POST',
                data: { 
                    reason: reason,
                    _token: '{{ csrf_token() }}' 
                },
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: handleAjaxError
            });
        }
        
        function cancelDeletion(deletionId) {
            const reason = prompt('Alasan pembatalan:');
            if (!reason) return;
            
            $.ajax({
                url: '{{ url("opd/transactions") }}/deletion/' + deletionId + '/cancel',
                method: 'POST',
                data: { 
                    cancellation_reason: reason,
                    _token: '{{ csrf_token() }}' 
                },
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: handleAjaxError
            });
        }
        
        function completeDeletion(deletionId) {
            const method = prompt('Metode penghapusan (jual/hibah/musnah/scrap):', 'musnah');
            if (!method) return;
            
            let details = {};
            if (method === 'jual') {
                details.sale_value = prompt('Nilai penjualan (Rp):', '0');
                details.recipient = prompt('Pembeli:');
            } else if (method === 'hibah') {
                details.recipient = prompt('Penerima hibah:');
            }
            
            details.notes = prompt('Catatan:');
            
            $.ajax({
                url: '{{ url("opd/transactions/deletions") }}/' + deletionId + '/complete',
                method: 'POST',
                data: { 
                    deletion_method: method,
                    details: details,
                    _token: '{{ csrf_token() }}' 
                },
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: handleAjaxError
            });
        }
        
        // Mutation actions
        function approveMutation(mutationId) {
            if (!confirm('Setujui proposal mutasi ini?')) return;
            
            $.ajax({
                url: '{{ url("opd/transactions/mutations") }}/' + mutationId + '/approve',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: handleAjaxError
            });
        }
        
        function rejectMutation(mutationId) {
            const reason = prompt('Alasan penolakan:');
            if (!reason) return;
            
            $.ajax({
                url: '{{ url("opd/transactions/mutations") }}/' + mutationId + '/reject',
                method: 'POST',
                data: { 
                    reason: reason,
                    _token: '{{ csrf_token() }}' 
                },
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: handleAjaxError
            });
        }
        
        function cancelMutation(mutationId) {
            const reason = prompt('Alasan pembatalan:');
            if (!reason) return;
            
            $.ajax({
                url: '{{ url("opd/transactions") }}/mutation/' + mutationId + '/cancel',
                method: 'POST',
                data: { 
                    cancellation_reason: reason,
                    _token: '{{ csrf_token() }}' 
                },
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: handleAjaxError
            });
        }
        
        function acceptMutation(mutationId) {
            if (!confirm('Terima aset dari mutasi ini?')) return;
            
            // Ask for target location
            const locationId = prompt('ID Lokasi tujuan (kosongkan untuk lokasi default):');
            
            $.ajax({
                url: '{{ url("opd/transactions/mutations") }}/' + mutationId + '/accept',
                method: 'POST',
                data: { 
                    location_id: locationId || '',
                    _token: '{{ csrf_token() }}' 
                },
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: handleAjaxError
            });
        }
        
        // Maintenance actions
        function startMaintenance(maintenanceId) {
            const actualDate = prompt('Tanggal mulai (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);
            if (!actualDate) return;
            
            $.ajax({
                url: '{{ url("opd/transactions/maintenances") }}/' + maintenanceId + '/update-status',
                method: 'POST',
                data: { 
                    status: 'dalam_pengerjaan',
                    actual_date: actualDate,
                    _token: '{{ csrf_token() }}' 
                },
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: handleAjaxError
            });
        }
        
        function completeMaintenance(maintenanceId) {
            const actualDate = prompt('Tanggal selesai (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);
            if (!actualDate) return;
            
            const resultStatus = prompt('Status hasil (baik/perlu_perbaikan/rusak):', 'baik');
            const resultNotes = prompt('Catatan hasil:');
            
            $.ajax({
                url: '{{ url("opd/transactions/maintenances") }}/' + maintenanceId + '/update-status',
                method: 'POST',
                data: { 
                    status: 'selesai',
                    actual_date: actualDate,
                    result_status: resultStatus,
                    result_notes: resultNotes,
                    _token: '{{ csrf_token() }}' 
                },
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: handleAjaxError
            });
        }
        
        function cancelMaintenance(maintenanceId) {
            const reason = prompt('Alasan pembatalan:');
            if (!reason) return;
            
            $.ajax({
                url: '{{ url("opd/transactions") }}/maintenance/' + maintenanceId + '/cancel',
                method: 'POST',
                data: { 
                    cancellation_reason: reason,
                    _token: '{{ csrf_token() }}' 
                },
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        setTimeout(() => location.reload(), 1000);
                    }
                },
                error: handleAjaxError
            });
        }
        
        // Helper functions for status colors
        @php
            function getDeletionStatusColor($status) {
                $colors = [
                    'diusulkan' => 'warning',
                    'disetujui' => 'info',
                    'selesai' => 'success',
                    'ditolak' => 'danger',
                    'dibatalkan' => 'secondary'
                ];
                return $colors[$status] ?? 'secondary';
            }
            
            function getMutationStatusColor($status) {
                $colors = [
                    'diusulkan' => 'warning',
                    'disetujui' => 'info',
                    'selesai' => 'success',
                    'ditolak' => 'danger',
                    'dibatalkan' => 'secondary'
                ];
                return $colors[$status] ?? 'secondary';
            }
        @endphp
    </script>
    @endpush
@endsection