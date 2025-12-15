@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
    <div class="col-md-12">
        <!-- Transaction Header -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h4 class="mb-1">{{ $title }}</h4>
                        @if($type == 'deletion')
                        <p class="text-muted mb-0">Proposal Penghapusan Aset</p>
                        @elseif($type == 'mutation')
                        <p class="text-muted mb-0">Proses Mutasi Aset</p>
                        @elseif($type == 'maintenance')
                        <p class="text-muted mb-0">Pemeliharaan Aset</p>
                        @endif
                    </div>
                    <div>
                        <a href="{{ route('opd.transactions.index', ['tab' => $type . 's']) }}" class="btn btn-secondary me-2">
                            <i class="fas fa-arrow-left me-1"></i> Kembali
                        </a>
                        @if($type == 'deletion' && $data->status == 'diusulkan' && $data->proposed_by == auth()->id())
                        <button type="button" class="btn btn-danger" onclick="cancelTransaction('deletion', {{ $data->deletion_id }})">
                            <i class="fas fa-times me-1"></i> Batalkan
                        </button>
                        @elseif($type == 'mutation' && $data->status == 'diusulkan' && $data->mutated_by == auth()->id())
                        <button type="button" class="btn btn-danger" onclick="cancelTransaction('mutation', {{ $data->mutation_id }})">
                            <i class="fas fa-times me-1"></i> Batalkan
                        </button>
                        @endif
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-3">
                        <small class="text-muted d-block">Status</small>
                        @php
                            $statusColors = [
                                'diusulkan' => 'warning',
                                'diverifikasi' => 'info',
                                'disetujui' => 'primary',
                                'selesai' => 'success',
                                'ditolak' => 'danger',
                                'dibatalkan' => 'secondary',
                                'dijadwalkan' => 'warning',
                                'dalam_pengerjaan' => 'info'
                            ];
                        @endphp
                        <span class="badge bg-{{ $statusColors[$data->status] ?? 'secondary' }}">
                            {{ ucfirst($data->status) }}
                        </span>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted d-block">Tanggal</small>
                        @if($type == 'deletion')
                        <strong>{{ \Carbon\Carbon::parse($data->proposed_at)->format('d/m/Y H:i') }}</strong>
                        @elseif($type == 'mutation')
                        <strong>{{ \Carbon\Carbon::parse($data->mutation_date)->format('d/m/Y') }}</strong>
                        @elseif($type == 'maintenance')
                        <strong>{{ \Carbon\Carbon::parse($data->scheduled_date)->format('d/m/Y') }}</strong>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Kode Referensi</small>
                        <code>
                            @if($type == 'deletion')
                            DEL-{{ str_pad($data->deletion_id, 6, '0', STR_PAD_LEFT) }}
                            @elseif($type == 'mutation')
                            MUT-{{ str_pad($data->mutation_id, 6, '0', STR_PAD_LEFT) }}
                            @elseif($type == 'maintenance')
                            MNT-{{ str_pad($data->maintenance_id, 6, '0', STR_PAD_LEFT) }}
                            @endif
                        </code>
                    </div>
                </div>
            </div>
        </div>
        
        @if($type == 'deletion')
            <!-- Deletion Detail -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Detail Aset</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Nama Aset</th>
                                            <td>{{ $data->asset->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kode Aset</th>
                                            <td><code>{{ $data->asset->asset_code }}</code></td>
                                        </tr>
                                        <tr>
                                            <th>Kategori</th>
                                            <td>{{ $data->asset->category->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Lokasi</th>
                                            <td>{{ $data->asset->location->name ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Nilai Aset</th>
                                            <td>Rp {{ number_format($data->asset->value, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kondisi</th>
                                            <td>
                                                <span class="badge bg-{{ $data->asset->condition == 'Baik' ? 'success' : ($data->asset->condition == 'Rusak Ringan' ? 'warning' : 'danger') }}">
                                                    {{ $data->asset->condition }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Tahun Perolehan</th>
                                            <td>{{ $data->asset->acquisition_year }}</td>
                                        </tr>
                                        <tr>
                                            <th>Status Aset</th>
                                            <td>
                                                <span class="badge bg-{{ $data->asset->status == 'aktif' ? 'success' : 'warning' }}">
                                                    {{ ucfirst(str_replace('_', ' ', $data->asset->status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Alasan Penghapusan</h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <strong>Alasan:</strong>
                                @php
                                    $reasons = [
                                        'rusak_berat' => 'Rusak Berat',
                                        'hilang' => 'Hilang',
                                        'jual' => 'Dijual',
                                        'hibah' => 'Dihibahkan',
                                        'musnah' => 'Musnah',
                                        'lainnya' => 'Lainnya'
                                    ];
                                @endphp
                                <span class="badge bg-info">{{ $reasons[$data->deletion_reason] ?? $data->deletion_reason }}</span>
                            </div>
                            
                            <div class="mb-3">
                                <strong>Detail Alasan:</strong>
                                <p class="mt-2">{{ $data->reason_details }}</p>
                            </div>
                            
                            @if($data->notes)
                            <div class="mb-3">
                                <strong>Catatan Tambahan:</strong>
                                <p class="mt-2">{{ $data->notes }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Proses Penghapusan</h6>
                        </div>
                        <div class="card-body">
                            <div class="timeline">
                                @if(isset($timeline))
                                    @foreach($timeline as $event)
                                    <div class="timeline-item mb-3">
                                        <div class="timeline-marker bg-primary"></div>
                                        <div class="timeline-content">
                                            <h6 class="mb-1">{{ $event['action'] }}</h6>
                                            <p class="mb-1 text-muted">
                                                <small><i class="fas fa-user me-1"></i> {{ $event['by'] }}</small>
                                                <br>
                                                <small><i class="fas fa-calendar me-1"></i> {{ \Carbon\Carbon::parse($event['date'])->format('d/m/Y H:i') }}</small>
                                            </p>
                                            <p class="mb-0"><small>{{ $event['description'] }}</small></p>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                                
                                @if($data->status == 'diusulkan')
                                <div class="timeline-item mb-3">
                                    <div class="timeline-marker bg-light border"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1 text-muted">Menunggu Verifikasi</h6>
                                        <p class="mb-0"><small>Proposal menunggu verifikasi oleh admin utama</small></p>
                                    </div>
                                </div>
                                
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-light border"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1 text-muted">Persetujuan</h6>
                                        <p class="mb-0"><small>Menunggu persetujuan akhir</small></p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Informasi Pengajuan</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th>Diajukan Oleh</th>
                                    <td>{{ $data->proposer->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Pengajuan</th>
                                    <td>{{ \Carbon\Carbon::parse($data->proposed_at)->format('d/m/Y H:i') }}</td>
                                </tr>
                                @if($data->verified_at)
                                <tr>
                                    <th>Diverifikasi Oleh</th>
                                    <td>{{ $data->verifier->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Verifikasi</th>
                                    <td>{{ \Carbon\Carbon::parse($data->verified_at)->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endif
                                @if($data->approved_at)
                                <tr>
                                    <th>Disetujui Oleh</th>
                                    <td>{{ $data->approver->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Persetujuan</th>
                                    <td>{{ \Carbon\Carbon::parse($data->approved_at)->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
        @elseif($type == 'mutation')
            <!-- Mutation Detail -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Detail Aset</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Nama Aset</th>
                                            <td>{{ $data->asset->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kode Aset</th>
                                            <td><code>{{ $data->asset->asset_code }}</code></td>
                                        </tr>
                                        <tr>
                                            <th>Kategori</th>
                                            <td>{{ $data->asset->category->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Nilai Aset</th>
                                            <td>Rp {{ number_format($data->asset->value, 0, ',', '.') }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Status Aset</th>
                                            <td>
                                                <span class="badge bg-{{ $data->asset->status == 'aktif' ? 'success' : ($data->asset->status == 'dimutasi' ? 'warning' : 'secondary') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $data->asset->status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Kondisi</th>
                                            <td>
                                                <span class="badge bg-{{ $data->asset->condition == 'Baik' ? 'success' : ($data->asset->condition == 'Rusak Ringan' ? 'warning' : 'danger') }}">
                                                    {{ $data->asset->condition }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Lokasi Asal</th>
                                            <td>{{ $data->fromLocation->name ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Dibuat Tanggal</th>
                                            <td>{{ $data->asset->created_at->format('d/m/Y') }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Detail Mutasi</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card bg-light">
                                        <div class="card-body text-center">
                                            <h6 class="text-muted">Dari</h6>
                                            <h4 class="mb-2">{{ $data->fromOpdUnit->nama_opd ?? '-' }}</h4>
                                            <p class="mb-1">
                                                <small>Lokasi: {{ $data->fromLocation->name ?? '-' }}</small>
                                            </p>
                                            <p class="mb-0">
                                                <small>Kode OPD: {{ $data->fromOpdUnit->kode_opd ?? '-' }}</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h6>Ke</h6>
                                            <h4 class="mb-2">{{ $data->toOpdUnit->nama_opd ?? '-' }}</h4>
                                            <p class="mb-1">
                                                <small>Lokasi: {{ $data->toLocation->name ?? 'Belum ditentukan' }}</small>
                                            </p>
                                            <p class="mb-0">
                                                <small>Kode OPD: {{ $data->toOpdUnit->kode_opd ?? '-' }}</small>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="30%">Tanggal Mutasi</th>
                                            <td>{{ \Carbon\Carbon::parse($data->mutation_date)->format('d/m/Y') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Diajukan Oleh</th>
                                            <td>{{ $data->mutator->name ?? '-' }}</td>
                                        </tr>
                                        @if($data->notes)
                                        <tr>
                                            <th>Catatan</th>
                                            <td>{{ $data->notes }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Status Mutasi</h6>
                        </div>
                        <div class="card-body">
                            <div class="progress mb-3" style="height: 25px;">
                                @php
                                    $progress = [
                                        'diusulkan' => 33,
                                        'disetujui' => 66,
                                        'selesai' => 100,
                                        'ditolak' => 100,
                                        'dibatalkan' => 100
                                    ];
                                    $currentProgress = $progress[$data->status] ?? 0;
                                @endphp
                                <div class="progress-bar bg-{{ $data->status == 'selesai' ? 'success' : ($data->status == 'ditolak' ? 'danger' : 'primary') }}" 
                                     role="progressbar" style="width: {{ $currentProgress }}%">
                                    {{ ucfirst($data->status) }}
                                </div>
                            </div>
                            
                            <div class="list-group list-group-flush">
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Pengajuan</span>
                                    <i class="fas fa-check-circle text-success"></i>
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Persetujuan OPD Tujuan</span>
                                    @if($data->status == 'disetujui' || $data->status == 'selesai')
                                    <i class="fas fa-check-circle text-success"></i>
                                    @elseif($data->status == 'ditolak')
                                    <i class="fas fa-times-circle text-danger"></i>
                                    @else
                                    <i class="fas fa-clock text-warning"></i>
                                    @endif
                                </div>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Penyerahan Aset</span>
                                    @if($data->status == 'selesai')
                                    <i class="fas fa-check-circle text-success"></i>
                                    @else
                                    <i class="fas fa-clock text-warning"></i>
                                    @endif
                                </div>
                            </div>
                            
                            @if($canAccept)
                            <div class="mt-4">
                                <button type="button" class="btn btn-success w-100" onclick="acceptMutation({{ $data->mutation_id }})">
                                    <i class="fas fa-check me-1"></i> Terima Aset
                                </button>
                                <small class="text-muted d-block mt-2">
                                    Klik untuk menerima aset dan menyelesaikan proses mutasi
                                </small>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Aksi</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('opd.assets.show', $data->asset) }}" class="btn btn-outline-primary">
                                    <i class="fas fa-box me-2"></i> Lihat Detail Aset
                                </a>
                                @if($data->status == 'diusulkan' && $data->mutated_by == auth()->id())
                                <button type="button" class="btn btn-outline-danger" 
                                        onclick="cancelTransaction('mutation', {{ $data->mutation_id }})">
                                    <i class="fas fa-times me-2"></i> Batalkan Mutasi
                                </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        @elseif($type == 'maintenance')
            <!-- Maintenance Detail -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Detail Pemeliharaan</h6>
                            @if($data->status == 'dijadwalkan' || $data->status == 'dalam_pengerjaan')
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                                <i class="fas fa-edit me-1"></i> Update Status
                            </button>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Judul</th>
                                            <td>{{ $data->title }}</td>
                                        </tr>
                                        <tr>
                                            <th>Jenis</th>
                                            <td>
                                                @php
                                                    $types = [
                                                        'rutin' => 'Pemeliharaan Rutin',
                                                        'perbaikan' => 'Perbaikan',
                                                        'kalibrasi' => 'Kalibrasi',
                                                        'penggantian' => 'Penggantian'
                                                    ];
                                                @endphp
                                                {{ $types[$data->maintenance_type] ?? $data->maintenance_type }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Tanggal Dijadwalkan</th>
                                            <td>{{ \Carbon\Carbon::parse($data->scheduled_date)->format('d/m/Y') }}</td>
                                        </tr>
                                        @if($data->actual_date)
                                        <tr>
                                            <th>Tanggal Aktual</th>
                                            <td>{{ \Carbon\Carbon::parse($data->actual_date)->format('d/m/Y') }}</td>
                                        </tr>
                                        @endif
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <th width="40%">Status</th>
                                            <td>
                                                <span class="badge bg-{{ $data->status == 'selesai' ? 'success' : ($data->status == 'dijadwalkan' ? 'warning' : 'info') }}">
                                                    {{ ucfirst(str_replace('_', ' ', $data->status)) }}
                                                </span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Biaya</th>
                                            <td>Rp {{ number_format($data->cost, 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <th>Vendor</th>
                                            <td>{{ $data->vendor ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <th>Kontak Vendor</th>
                                            <td>{{ $data->vendor_contact ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            
                            @if($data->description)
                            <div class="mt-3">
                                <strong>Deskripsi:</strong>
                                <p class="mt-2">{{ $data->description }}</p>
                            </div>
                            @endif
                            
                            @if($data->result_notes)
                            <div class="mt-3">
                                <strong>Hasil Pemeliharaan:</strong>
                                <p class="mt-2">{{ $data->result_notes }}</p>
                            </div>
                            @endif
                            
                            @if($data->result_status)
                            <div class="mt-3">
                                <strong>Status Hasil:</strong>
                                <span class="badge bg-{{ $data->result_status == 'baik' ? 'success' : ($data->result_status == 'perlu_perbaikan' ? 'warning' : 'danger') }}">
                                    {{ ucfirst(str_replace('_', ' ', $data->result_status)) }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>
                    
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Detail Aset</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 60px; height: 60px;">
                                        <i class="fas fa-box text-white" style="font-size: 1.5rem;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 class="mb-1">{{ $data->asset->name }}</h5>
                                    <p class="mb-1">
                                        <code>{{ $data->asset->asset_code }}</code>
                                        <span class="ms-2 badge bg-{{ $data->asset->condition == 'Baik' ? 'success' : ($data->asset->condition == 'Rusak Ringan' ? 'warning' : 'danger') }}">
                                            {{ $data->asset->condition }}
                                        </span>
                                    </p>
                                    <p class="mb-0 text-muted">
                                        <small>
                                            Lokasi: {{ $data->asset->location->name ?? '-' }} | 
                                            Nilai: Rp {{ number_format($data->asset->value, 0, ',', '.') }}
                                        </small>
                                    </p>
                                </div>
                                <div class="flex-shrink-0">
                                    <a href="{{ route('opd.assets.show', $data->asset) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i> Lihat
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0">Informasi Pemeliharaan</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th>Dicatat Oleh</th>
                                    <td>{{ $data->recorder->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Dicatat</th>
                                    <td>{{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y H:i') }}</td>
                                </tr>
                                @if($data->approved_at)
                                <tr>
                                    <th>Disetujui Oleh</th>
                                    <td>{{ $data->approver->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Disetujui</th>
                                    <td>{{ \Carbon\Carbon::parse($data->approved_at)->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Aksi</h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                @if($data->status == 'dijadwalkan')
                                <button type="button" class="btn btn-outline-danger" 
                                        onclick="cancelTransaction('maintenance', {{ $data->maintenance_id }})">
                                    <i class="fas fa-times me-2"></i> Batalkan Pemeliharaan
                                </button>
                                @endif
                                
                                <a href="{{ route('opd.transactions.index', ['tab' => 'maintenances']) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-list me-2"></i> Kembali ke Daftar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Modals -->
<div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Status Pemeliharaan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('opd.transactions.update-maintenance-status', $data->maintenance_id) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status *</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="dalam_pengerjaan">Dalam Pengerjaan</option>
                            <option value="selesai">Selesai</option>
                            <option value="ditunda">Ditunda</option>
                            <option value="dibatalkan">Dibatalkan</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="actual_date" class="form-label">Tanggal Aktual</label>
                        <input type="date" class="form-control" id="actual_date" name="actual_date" 
                               value="{{ date('Y-m-d') }}">
                    </div>
                    
                    <div class="mb-3">
                        <label for="result_status" class="form-label">Status Hasil</label>
                        <select class="form-select" id="result_status" name="result_status">
                            <option value="">Pilih Status</option>
                            <option value="baik">Baik</option>
                            <option value="perlu_perbaikan">Perlu Perbaikan</option>
                            <option value="rusak">Rusak</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="result_notes" class="form-label">Catatan Hasil</label>
                        <textarea class="form-control" id="result_notes" name="result_notes" rows="3"></textarea>
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

@push('scripts')
<script>
function cancelTransaction(type, id) {
    const reason = prompt('Masukkan alasan pembatalan:');
    if (reason && reason.trim().length >= 10) {
        $.ajax({
            url: `/opd/transactions/${type}/${id}/cancel`,
            type: 'POST',
            data: {
                cancellation_reason: reason,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success || response.redirect) {
                    window.location.reload();
                } else {
                    alert(response.message || 'Gagal membatalkan transaksi');
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Gagal membatalkan transaksi');
            }
        });
    } else if (reason !== null) {
        alert('Alasan pembatalan minimal 10 karakter');
    }
}

function acceptMutation(mutationId) {
    if (confirm('Apakah Anda yakin ingin menerima mutasi ini?')) {
        $.ajax({
            url: `/opd/transactions/mutations/${mutationId}/accept`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message || 'Gagal menerima mutasi');
                }
            },
            error: function(xhr) {
                alert(xhr.responseJSON?.message || 'Gagal menerima mutasi');
            }
        });
    }
}
</script>
@endpush

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
@endsection