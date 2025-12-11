@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-3 mb-4 border-bottom">
        <h1 class="h3">
            @if($tab == 'deletions')
                <i class="fas fa-trash-alt me-2"></i> Manajemen Penghapusan Aset
            @elseif($tab == 'mutations')
                <i class="fas fa-exchange-alt me-2"></i> Manajemen Mutasi Aset
            @else
                <i class="fas fa-tools me-2"></i> Manajemen Pemeliharaan
            @endif
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="{{ route('opd.transactions.create', ['type' => $tab]) }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus me-1"></i> Buat Baru
                </a>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportTransactions()">
                    <i class="fas fa-file-export me-1"></i> Ekspor
                </button>
            </div>
        </div>
    </div>

    <!-- Transaction Tabs -->
    <ul class="nav nav-tabs mb-4" id="transactionTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'deletions' ? 'active' : '' }}" 
                    onclick="window.location.href='{{ route('opd.transactions.index', ['tab' => 'deletions', 'status' => $status]) }}'">
                <i class="fas fa-trash-alt me-1"></i> Penghapusan
                @if(isset($data['deletions']) && $data['deletions']->where('status', 'diusulkan')->count() > 0)
                    <span class="badge bg-danger ms-1">{{ $data['deletions']->where('status', 'diusulkan')->count() }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'mutations' ? 'active' : '' }}" 
                    onclick="window.location.href='{{ route('opd.transactions.index', ['tab' => 'mutations', 'status' => $status]) }}'">
                <i class="fas fa-exchange-alt me-1"></i> Mutasi
                @if(isset($data['mutations']) && $data['mutations']->where('status', 'diusulkan')->count() > 0)
                    <span class="badge bg-warning ms-1">{{ $data['mutations']->where('status', 'diusulkan')->count() }}</span>
                @endif
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'maintenances' ? 'active' : '' }}" 
                    onclick="window.location.href='{{ route('opd.transactions.index', ['tab' => 'maintenances', 'status' => $status]) }}'">
                <i class="fas fa-tools me-1"></i> Pemeliharaan
                @if(isset($data['overdueMaintenances']) && $data['overdueMaintenances']->count() > 0)
                    <span class="badge bg-danger ms-1">{{ $data['overdueMaintenances']->count() }}</span>
                @endif
            </button>
        </li>
    </ul>

    <!-- Status Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('opd.transactions.index', ['tab' => $tab]) }}" 
                   class="btn btn-sm {{ !$status ? 'btn-primary' : 'btn-outline-primary' }}">
                    Semua
                </a>
                
                @if($tab == 'deletions')
                    <a href="{{ route('opd.transactions.index', ['tab' => $tab, 'status' => 'diusulkan']) }}" 
                       class="btn btn-sm {{ $status == 'diusulkan' ? 'btn-warning' : 'btn-outline-warning' }}">
                        Diusulkan
                    </a>
                    <a href="{{ route('opd.transactions.index', ['tab' => $tab, 'status' => 'disetujui']) }}" 
                       class="btn btn-sm {{ $status == 'disetujui' ? 'btn-info' : 'btn-outline-info' }}">
                        Disetujui
                    </a>
                    <a href="{{ route('opd.transactions.index', ['tab' => $tab, 'status' => 'selesai']) }}" 
                       class="btn btn-sm {{ $status == 'selesai' ? 'btn-success' : 'btn-outline-success' }}">
                        Selesai
                    </a>
                    <a href="{{ route('opd.transactions.index', ['tab' => $tab, 'status' => 'ditolak']) }}" 
                       class="btn btn-sm {{ $status == 'ditolak' ? 'btn-danger' : 'btn-outline-danger' }}">
                        Ditolak
                    </a>
                @elseif($tab == 'mutations')
                    <a href="{{ route('opd.transactions.index', ['tab' => $tab, 'status' => 'diusulkan']) }}" 
                       class="btn btn-sm {{ $status == 'diusulkan' ? 'btn-warning' : 'btn-outline-warning' }}">
                        Diusulkan
                    </a>
                    <a href="{{ route('opd.transactions.index', ['tab' => $tab, 'status' => 'disetujui']) }}" 
                       class="btn btn-sm {{ $status == 'disetujui' ? 'btn-info' : 'btn-outline-info' }}">
                        Disetujui
                    </a>
                    <a href="{{ route('opd.transactions.index', ['tab' => $tab, 'status' => 'selesai']) }}" 
                       class="btn btn-sm {{ $status == 'selesai' ? 'btn-success' : 'btn-outline-success' }}">
                        Selesai
                    </a>
                @elseif($tab == 'maintenances')
                    <a href="{{ route('opd.transactions.index', ['tab' => $tab, 'status' => 'dijadwalkan']) }}" 
                       class="btn btn-sm {{ $status == 'dijadwalkan' ? 'btn-warning' : 'btn-outline-warning' }}">
                        Dijadwalkan
                    </a>
                    <a href="{{ route('opd.transactions.index', ['tab' => $tab, 'status' => 'dalam_pengerjaan']) }}" 
                       class="btn btn-sm {{ $status == 'dalam_pengerjaan' ? 'btn-info' : 'btn-outline-info' }}">
                        Dalam Pengerjaan
                    </a>
                    <a href="{{ route('opd.transactions.index', ['tab' => $tab, 'status' => 'selesai']) }}" 
                       class="btn btn-sm {{ $status == 'selesai' ? 'btn-success' : 'btn-outline-success' }}">
                        Selesai
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card" style="background-color: #e3f2fd;">
                <i class="fas fa-list text-primary"></i>
                <div class="stat-value" id="totalTransactions">0</div>
                <div class="stat-label">Total</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background-color: #fff3cd;">
                <i class="fas fa-clock text-warning"></i>
                <div class="stat-value" id="pendingTransactions">0</div>
                <div class="stat-label">Tertunda</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background-color: #d4edda;">
                <i class="fas fa-check-circle text-success"></i>
                <div class="stat-value" id="completedTransactions">0</div>
                <div class="stat-label">Selesai</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background-color: #f8d7da;">
                <i class="fas fa-times-circle text-danger"></i>
                <div class="stat-value" id="rejectedTransactions">0</div>
                <div class="stat-label">Ditolak</div>
            </div>
        </div>
    </div>

    <!-- Content based on Tab -->
    @if($tab == 'deletions')
        <!-- Deletions Content -->
        <div class="row">
            @if(isset($data['incomingMutations']) && $data['incomingMutations']->count() > 0)
                <div class="col-md-12 mb-4">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-white">
                            <i class="fas fa-download me-2"></i> Mutasi Masuk Menunggu Konfirmasi
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Aset</th>
                                            <th>Dari OPD</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['incomingMutations'] as $mutation)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('opd.assets.show', $mutation->asset_id) }}" class="text-decoration-none">
                                                        {{ $mutation->asset->name }}
                                                    </a>
                                                    <br>
                                                    <small class="text-muted">{{ $mutation->asset->asset_code }}</small>
                                                </td>
                                                <td>{{ $mutation->fromOpdUnit->nama_opd ?? '-' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($mutation->mutation_date)->format('d/m/Y') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $mutation->status == 'disetujui' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($mutation->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($mutation->status == 'disetujui')
                                                        <button class="btn btn-sm btn-success" 
                                                                onclick="acceptMutation({{ $mutation->mutation_id }})">
                                                            <i class="fas fa-check me-1"></i> Terima
                                                        </button>
                                                    @endif
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
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-trash-alt me-2"></i> Daftar Proposal Penghapusan
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshData()">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(isset($data['deletions']) && $data['deletions']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Aset</th>
                                            <th>Alasan Penghapusan</th>
                                            <th>Status</th>
                                            <th>Diusulkan</th>
                                            <th>Disetujui</th>
                                            <th>Timeline</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['deletions'] as $deletion)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>
                                                    <a href="{{ route('opd.assets.show', $deletion->asset_id) }}" class="text-decoration-none">
                                                        {{ $deletion->asset->name }}
                                                    </a>
                                                    <br>
                                                    <small class="text-muted">
                                                        Kode: {{ $deletion->asset->asset_code }} | 
                                                        Nilai: {{ $deletion->asset->formatted_value }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <strong>{{ $deletion->getDeletionReasonDisplayAttribute() }}</strong>
                                                    @if($deletion->reason_details)
                                                        <br>
                                                        <small class="text-muted">{{ Str::limit($deletion->reason_details, 100) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="badge bg-{{ getDeletionStatusColor($deletion->status) }}">
                                                        {{ $deletion->getStatusDisplayAttribute() }}
                                                    </span>
                                                </td>
                                                <td>
                                                    {{ $deletion->proposed_at ? \Carbon\Carbon::parse($deletion->proposed_at)->format('d/m/Y') : '-' }}
                                                    <br>
                                                    <small class="text-muted">Oleh: {{ $deletion->proposer->name ?? '-' }}</small>
                                                </td>
                                                <td>
                                                    @if($deletion->approved_at)
                                                        {{ \Carbon\Carbon::parse($deletion->approved_at)->format('d/m/Y') }}
                                                        <br>
                                                        <small class="text-muted">Oleh: {{ $deletion->approver->name ?? '-' }}</small>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="timeline-mini">
                                                        @if($deletion->proposed_at)
                                                            <div class="timeline-item">
                                                                <div class="timeline-marker bg-primary"></div>
                                                                <div class="timeline-content">
                                                                    <small>Diusulkan</small>
                                                                    <div>{{ \Carbon\Carbon::parse($deletion->proposed_at)->format('d/m') }}</div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if($deletion->verified_at)
                                                            <div class="timeline-item">
                                                                <div class="timeline-marker bg-info"></div>
                                                                <div class="timeline-content">
                                                                    <small>Diverifikasi</small>
                                                                    <div>{{ \Carbon\Carbon::parse($deletion->verified_at)->format('d/m') }}</div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if($deletion->approved_at)
                                                            <div class="timeline-item">
                                                                <div class="timeline-marker bg-success"></div>
                                                                <div class="timeline-content">
                                                                    <small>Disetujui</small>
                                                                    <div>{{ \Carbon\Carbon::parse($deletion->approved_at)->format('d/m') }}</div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ route('opd.transactions.show', ['deletion', $deletion->deletion_id]) }}" 
                                                           class="btn btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($deletion->status == 'diusulkan' && Auth::user()->isAdminUtama())
                                                            <button type="button" class="btn btn-outline-success" 
                                                                    onclick="approveDeletion({{ $deletion->deletion_id }})">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-danger" 
                                                                    onclick="rejectDeletion({{ $deletion->deletion_id }})">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        @endif
                                                        @if(in_array($deletion->status, ['diusulkan', 'disetujui']) && $deletion->proposed_by == Auth::id())
                                                            <button type="button" class="btn btn-outline-warning" 
                                                                    onclick="cancelDeletion({{ $deletion->deletion_id }})">
                                                                <i class="fas fa-ban"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $data['deletions']->appends(['tab' => $tab, 'status' => $status])->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-trash-alt fa-3x text-muted mb-3"></i>
                                <h5>Tidak ada proposal penghapusan</h5>
                                <p class="text-muted">Mulai dengan mengajukan penghapusan aset</p>
                                <a href="{{ route('opd.transactions.create', ['type' => 'deletion']) }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Ajukan Penghapusan
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
    @elseif($tab == 'mutations')
        <!-- Mutations Content -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-exchange-alt me-2"></i> Daftar Mutasi Aset
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshData()">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Outgoing Mutations -->
                        <h6 class="mb-3">Mutasi Keluar</h6>
                        @if(isset($data['mutations']) && $data['mutations']->count() > 0)
                            <div class="table-responsive mb-4">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Aset</th>
                                            <th>Dari OPD</th>
                                            <th>Ke OPD</th>
                                            <th>Tanggal Mutasi</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['mutations'] as $mutation)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('opd.assets.show', $mutation->asset_id) }}" class="text-decoration-none">
                                                        {{ $mutation->asset->name }}
                                                    </a>
                                                    <br>
                                                    <small class="text-muted">{{ $mutation->asset->asset_code }}</small>
                                                </td>
                                                <td>
                                                    {{ $mutation->fromOpdUnit->nama_opd ?? 'OPD Saat Ini' }}
                                                    @if($mutation->fromLocation)
                                                        <br>
                                                        <small class="text-muted">Lokasi: {{ $mutation->fromLocation->name }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $mutation->toOpdUnit->nama_opd ?? '-' }}
                                                    @if($mutation->toLocation)
                                                        <br>
                                                        <small class="text-muted">Lokasi: {{ $mutation->toLocation->name }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($mutation->mutation_date)->format('d/m/Y') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ getMutationStatusColor($mutation->status) }}">
                                                        {{ ucfirst($mutation->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ route('opd.transactions.show', ['mutation', $mutation->mutation_id]) }}" 
                                                           class="btn btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($mutation->status == 'diusulkan' && Auth::user()->isAdminUtama())
                                                            <button type="button" class="btn btn-outline-success" 
                                                                    onclick="approveMutation({{ $mutation->mutation_id }})">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-danger" 
                                                                    onclick="rejectMutation({{ $mutation->mutation_id }})">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        @endif
                                                        @if($mutation->status == 'diusulkan' && $mutation->from_opd_unit_id == Auth::user()->opd_unit_id)
                                                            <button type="button" class="btn btn-outline-warning" 
                                                                    onclick="cancelMutation({{ $mutation->mutation_id }})">
                                                                <i class="fas fa-ban"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-center">
                                {{ $data['mutations']->appends(['tab' => $tab, 'status' => $status])->links() }}
                            </div>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Tidak ada mutasi keluar
                            </div>
                        @endif
                        
                        <!-- Incoming Mutations -->
                        @if(isset($data['incomingMutations']) && $data['incomingMutations']->count() > 0)
                            <hr class="my-4">
                            <h6 class="mb-3">Mutasi Masuk</h6>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Aset</th>
                                            <th>Dari OPD</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['incomingMutations'] as $mutation)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('opd.assets.show', $mutation->asset_id) }}" class="text-decoration-none">
                                                        {{ $mutation->asset->name }}
                                                    </a>
                                                    <br>
                                                    <small class="text-muted">{{ $mutation->asset->asset_code }}</small>
                                                </td>
                                                <td>{{ $mutation->fromOpdUnit->nama_opd ?? '-' }}</td>
                                                <td>{{ \Carbon\Carbon::parse($mutation->mutation_date)->format('d/m/Y') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $mutation->status == 'disetujui' ? 'success' : 'warning' }}">
                                                        {{ ucfirst($mutation->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($mutation->status == 'disetujui')
                                                        <button class="btn btn-sm btn-success" 
                                                                onclick="acceptMutation({{ $mutation->mutation_id }})">
                                                            <i class="fas fa-check me-1"></i> Terima
                                                        </button>
                                                    @endif
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
                            
                            <!-- Pagination for incoming -->
                            <div class="d-flex justify-content-center">
                                {{ $data['incomingMutations']->appends(['tab' => $tab, 'status' => $status, 'page' => 'incoming_page'])->links() }}
                            </div>
                        @endif
                        
                        @if(!isset($data['mutations']) || $data['mutations']->count() == 0)
                            <div class="text-center py-5">
                                <i class="fas fa-exchange-alt fa-3x text-muted mb-3"></i>
                                <h5>Tidak ada data mutasi</h5>
                                <p class="text-muted">Mulai dengan mengajukan mutasi aset</p>
                                <a href="{{ route('opd.transactions.create', ['type' => 'mutation']) }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Ajukan Mutasi
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
    @elseif($tab == 'maintenances')
        <!-- Maintenances Content -->
        <div class="row">
            @if(isset($data['overdueMaintenances']) && $data['overdueMaintenances']->count() > 0)
                <div class="col-md-12 mb-4">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <i class="fas fa-exclamation-triangle me-2"></i> Pemeliharaan Terlambat
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Aset</th>
                                            <th>Jenis</th>
                                            <th>Judul</th>
                                            <th>Tanggal Terjadwal</th>
                                            <th>Keterlambatan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['overdueMaintenances'] as $maintenance)
                                            @php
                                                $overdueDays = now()->diffInDays(\Carbon\Carbon::parse($maintenance->scheduled_date));
                                            @endphp
                                            <tr>
                                                <td>
                                                    <a href="{{ route('opd.assets.show', $maintenance->asset_id) }}" class="text-decoration-none">
                                                        {{ $maintenance->asset->name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    @php
                                                        $types = [
                                                            'rutin' => 'Rutin',
                                                            'perbaikan' => 'Perbaikan',
                                                            'kalibrasi' => 'Kalibrasi',
                                                            'penggantian' => 'Penggantian',
                                                            'lainnya' => 'Lainnya'
                                                        ];
                                                    @endphp
                                                    {{ $types[$maintenance->maintenance_type] ?? $maintenance->maintenance_type }}
                                                </td>
                                                <td>{{ $maintenance->title }}</td>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($maintenance->scheduled_date)->format('d/m/Y') }}
                                                    <br>
                                                    <small class="text-danger">Terlambat {{ $overdueDays }} hari</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-danger">OVERDUE</span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('opd.transactions.show', ['maintenance', $maintenance->maintenance_id]) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye me-1"></i> Detail
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-warning" 
                                                            onclick="startMaintenance({{ $maintenance->maintenance_id }})">
                                                        <i class="fas fa-play me-1"></i> Mulai
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
            @endif
            
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-tools me-2"></i> Daftar Pemeliharaan
                        </div>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="refreshData()">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        @if(isset($data['maintenances']) && $data['maintenances']->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Aset</th>
                                            <th>Jenis</th>
                                            <th>Judul</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                            <th>Biaya</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['maintenances'] as $maintenance)
                                            <tr>
                                                <td>
                                                    <a href="{{ route('opd.assets.show', $maintenance->asset_id) }}" class="text-decoration-none">
                                                        {{ $maintenance->asset->name }}
                                                    </a>
                                                    <br>
                                                    <small class="text-muted">{{ $maintenance->asset->asset_code }}</small>
                                                </td>
                                                <td>
                                                    @php
                                                        $types = [
                                                            'rutin' => 'Rutin',
                                                            'perbaikan' => 'Perbaikan',
                                                            'kalibrasi' => 'Kalibrasi',
                                                            'penggantian' => 'Penggantian',
                                                            'lainnya' => 'Lainnya'
                                                        ];
                                                    @endphp
                                                    {{ $types[$maintenance->maintenance_type] ?? $maintenance->maintenance_type }}
                                                </td>
                                                <td>{{ $maintenance->title }}</td>
                                                <td>
                                                    @if($maintenance->actual_date)
                                                        {{ \Carbon\Carbon::parse($maintenance->actual_date)->format('d/m/Y') }}
                                                        <br>
                                                        <small class="text-muted">Aktual</small>
                                                    @else
                                                        {{ \Carbon\Carbon::parse($maintenance->scheduled_date)->format('d/m/Y') }}
                                                        <br>
                                                        <small class="text-muted">Terjadwal</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    @php
                                                        $statusBadges = [
                                                            'dijadwalkan' => 'warning',
                                                            'dalam_pengerjaan' => 'info',
                                                            'selesai' => 'success',
                                                            'dibatalkan' => 'danger',
                                                            'ditunda' => 'secondary'
                                                        ];
                                                    @endphp
                                                    <span class="badge bg-{{ $statusBadges[$maintenance->status] ?? 'secondary' }}">
                                                        {{ ucfirst(str_replace('_', ' ', $maintenance->status)) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($maintenance->cost)
                                                        <span class="fw-bold">Rp {{ number_format($maintenance->cost, 0, ',', '.') }}</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <a href="{{ route('opd.transactions.show', ['maintenance', $maintenance->maintenance_id]) }}" 
                                                           class="btn btn-outline-primary">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        @if($maintenance->status == 'dijadwalkan')
                                                            <button type="button" class="btn btn-outline-info" 
                                                                    onclick="startMaintenance({{ $maintenance->maintenance_id }})">
                                                                <i class="fas fa-play"></i>
                                                            </button>
                                                            <button type="button" class="btn btn-outline-warning" 
                                                                    onclick="cancelMaintenance({{ $maintenance->maintenance_id }})">
                                                                <i class="fas fa-ban"></i>
                                                            </button>
                                                        @elseif($maintenance->status == 'dalam_pengerjaan')
                                                            <button type="button" class="btn btn-outline-success" 
                                                                    onclick="completeMaintenance({{ $maintenance->maintenance_id }})">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $data['maintenances']->appends(['tab' => $tab, 'status' => $status])->links() }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                                <h5>Tidak ada data pemeliharaan</h5>
                                <p class="text-muted">Mulai dengan menjadwalkan pemeliharaan aset</p>
                                <a href="{{ route('opd.transactions.create', ['type' => 'maintenance']) }}" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Jadwalkan Pemeliharaan
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Statistics Modal -->
    <div class="modal fade" id="statisticsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Statistik {{ ucfirst($tab) }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="statisticsContent">
                        <!-- Statistics will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .timeline-mini {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .timeline-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .timeline-marker {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        
        .timeline-content {
            font-size: 0.8em;
        }
        
        .timeline-content small {
            color: #6c757d;
        }
        
        .status-badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.85em;
        }
        
        .card-border-warning {
            border: 2px solid #ffc107;
        }
        
        .card-border-danger {
            border: 2px solid #dc3545;
        }
        
        .quick-action-btn {
            padding: 6px 12px;
            font-size: 0.85em;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Load initial statistics
            updateTransactionStats();
            
            // Refresh stats every 30 seconds
            setInterval(updateTransactionStats, 30000);
        });
        
        // Update transaction statistics
        function updateTransactionStats() {
            $.ajax({
                url: '{{ route("opd.transactions.statistics") }}',
                method: 'GET',
                data: { type: '{{ $tab }}' },
                success: function(response) {
                    if (response.success) {
                        const stats = response.stats['{{ $tab }}'] || {};
                        
                        $('#totalTransactions').text(stats.total || 0);
                        $('#pendingTransactions').text(stats.pending || 0);
                        $('#completedTransactions').text(stats.completed || 0);
                        
                        if ('{{ $tab }}' === 'deletions') {
                            $('#rejectedTransactions').text(stats.rejected || 0);
                        }
                    }
                }
            });
        }
        
        // Refresh data
        function refreshData() {
            location.reload();
        }
        
        // Export transactions
        function exportTransactions() {
            const format = prompt('Pilih format ekspor:\n1. Excel\n2. PDF\n3. CSV', 'excel');
            
            if (format) {
                // Implement export logic here
                showToast(`Sedang mengekspor data {{ $tab }} ke format ${format.toUpperCase()}...`, 'info');
            }
        }
        
        // Show statistics modal
        function showStatistics() {
            $.ajax({
                url: '{{ route("opd.transactions.statistics") }}',
                method: 'GET',
                data: { type: '{{ $tab }}', detailed: true },
                success: function(response) {
                    if (response.success) {
                        $('#statisticsContent').html(renderStatistics(response.stats));
                        $('#statisticsModal').modal('show');
                    }
                },
                error: handleAjaxError
            });
        }
        
        // Render statistics
        function renderStatistics(stats) {
            let html = '';
            
            if ('{{ $tab }}' === 'deletions') {
                const deletionStats = stats.deletions || {};
                html = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Ringkasan</h6>
                            <table class="table table-sm">
                                <tr><td>Total Proposal:</td><td class="text-end fw-bold">${deletionStats.total || 0}</td></tr>
                                <tr><td>Dalam Proses:</td><td class="text-end fw-bold">${deletionStats.pending || 0}</td></tr>
                                <tr><td>Disetujui:</td><td class="text-end fw-bold">${deletionStats.approved || 0}</td></tr>
                                <tr><td>Selesai:</td><td class="text-end fw-bold">${deletionStats.completed || 0}</td></tr>
                                <tr><td>Ditolak:</td><td class="text-end fw-bold">${deletionStats.rejected || 0}</td></tr>
                                <tr><td>Tingkat Penyelesaian:</td><td class="text-end fw-bold">${deletionStats.approval_rate || 0}%</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Distribusi Alasan</h6>
                            <div id="deletionReasonsChart" style="height: 200px;"></div>
                        </div>
                    </div>
                `;
            }
            
            return html;
        }
        
        // Deletion Actions
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
        
        // Mutation Actions
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
            
            $.ajax({
                url: '{{ url("opd/transactions/mutations") }}/' + mutationId + '/accept',
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
        
        // Maintenance Actions
        function startMaintenance(maintenanceId) {
            const actualDate = prompt('Tanggal mulai pemeliharaan (YYYY-MM-DD):', new Date().toISOString().split('T')[0]);
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
            
            const resultStatus = prompt('Status hasil (Baik/Perlu Perbaikan/Rusak):', 'Baik');
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
        
        // Helper function for status colors
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