@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">Manajemen Transaksi</h4>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-plus me-1"></i> Ajukan Baru
                </button>
                <div class="dropdown-menu">
                    <a class="dropdown-item" href="{{ route('opd.transactions.create', ['type' => 'deletion']) }}">
                        <i class="fas fa-trash me-2"></i> Penghapusan Aset
                    </a>
                    <a class="dropdown-item" href="{{ route('opd.transactions.create', ['type' => 'mutation']) }}">
                        <i class="fas fa-exchange-alt me-2"></i> Mutasi Aset
                    </a>
                    <a class="dropdown-item" href="{{ route('opd.transactions.create', ['type' => 'maintenance']) }}">
                        <i class="fas fa-tools me-2"></i> Pemeliharaan
                    </a>
                </div>
            </div>
        </div>
        
        <ul class="nav nav-tabs">
            <li class="nav-item">
                <a class="nav-link {{ $tab == 'deletions' ? 'active' : '' }}" 
                   href="{{ route('opd.transactions.index', ['tab' => 'deletions']) }}">
                    <i class="fas fa-trash me-1"></i> Penghapusan
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab == 'mutations' ? 'active' : '' }}" 
                   href="{{ route('opd.transactions.index', ['tab' => 'mutations']) }}">
                    <i class="fas fa-exchange-alt me-1"></i> Mutasi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $tab == 'maintenances' ? 'active' : '' }}" 
                   href="{{ route('opd.transactions.index', ['tab' => 'maintenances']) }}">
                    <i class="fas fa-tools me-1"></i> Pemeliharaan
                </a>
            </li>
        </ul>
        
        <div class="tab-content mt-4">
            @if($tab == 'deletions')
                <!-- Deletions Tab -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <form method="GET" class="row g-3">
                                    <input type="hidden" name="tab" value="deletions">
                                    <div class="col-md-3">
                                        <select name="status" class="form-select">
                                            <option value="">Semua Status</option>
                                            <option value="diusulkan" {{ $status == 'diusulkan' ? 'selected' : '' }}>Diusulkan</option>
                                            <option value="diverifikasi" {{ $status == 'diverifikasi' ? 'selected' : '' }}>Diverifikasi</option>
                                            <option value="disetujui" {{ $status == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                            <option value="selesai" {{ $status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                            <option value="ditolak" {{ $status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-filter"></i> Filter
                                        </button>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="{{ route('opd.transactions.index', ['tab' => 'deletions']) }}" class="btn btn-outline-secondary w-100">
                                            Reset
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table data-table">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Aset</th>
                                                <th>Alasan</th>
                                                <th>Diajukan Oleh</th>
                                                <th>Status</th>
                                                <th>Nilai Aset</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data['deletions'] as $deletion)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($deletion->proposed_at)->format('d/m/Y') }}</td>
                                                <td>
                                                    <strong>{{ $deletion->asset->name }}</strong>
                                                    <br><small class="text-muted">{{ $deletion->asset->asset_code }}</small>
                                                </td>
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
                                                <td>{{ $deletion->proposer->name ?? '-' }}</td>
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
                                                <td>Rp {{ number_format($deletion->asset->value, 0, ',', '.') }}</td>
                                                <td>
                                                    <a href="{{ route('opd.transactions.show', ['deletion', $deletion->deletion_id]) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($deletion->status == 'diusulkan' && $deletion->proposed_by == auth()->id())
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="cancelTransaction('deletion', {{ $deletion->deletion_id }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="d-flex justify-content-center">
                                    {{ $data['deletions']->appends(request()->all())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
            @elseif($tab == 'mutations')
                <!-- Mutations Tab -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <form method="GET" class="row g-3">
                                    <input type="hidden" name="tab" value="mutations">
                                    <div class="col-md-3">
                                        <select name="status" class="form-select">
                                            <option value="">Semua Status</option>
                                            <option value="diusulkan" {{ $status == 'diusulkan' ? 'selected' : '' }}>Diusulkan</option>
                                            <option value="disetujui" {{ $status == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                            <option value="selesai" {{ $status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                            <option value="ditolak" {{ $status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-filter"></i> Filter
                                        </button>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="{{ route('opd.transactions.index', ['tab' => 'mutations']) }}" class="btn btn-outline-secondary w-100">
                                            Reset
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Mutasi Keluar (Aset dari OPD Anda)</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table data-table">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Aset</th>
                                                <th>Ke OPD</th>
                                                <th>Status</th>
                                                <th>Nilai</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data['mutations'] as $mutation)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::parse($mutation->mutation_date)->format('d/m/Y') }}</td>
                                                <td>
                                                    <strong>{{ $mutation->asset->name }}</strong>
                                                    <br><small class="text-muted">{{ $mutation->asset->asset_code }}</small>
                                                </td>
                                                <td>{{ $mutation->toOpdUnit->nama_opd ?? '-' }}</td>
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
                                                <td>Rp {{ number_format($mutation->asset->value, 0, ',', '.') }}</td>
                                                <td>
                                                    <a href="{{ route('opd.transactions.show', ['mutation', $mutation->mutation_id]) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($mutation->status == 'diusulkan' && $mutation->mutated_by == auth()->id())
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="cancelTransaction('mutation', {{ $mutation->mutation_id }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="d-flex justify-content-center">
                                    {{ $data['mutations']->appends(request()->all())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0">Mutasi Masuk</h6>
                            </div>
                            <div class="card-body">
                                @if($data['incomingMutations']->count() > 0)
                                <div class="list-group">
                                    @foreach($data['incomingMutations'] as $mutation)
                                    <div class="list-group-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ $mutation->asset->name }}</h6>
                                            <span class="badge bg-{{ $mutation->status == 'disetujui' ? 'success' : 'warning' }}">
                                                {{ ucfirst($mutation->status) }}
                                            </span>
                                        </div>
                                        <p class="mb-1">
                                            <small>Dari: {{ $mutation->fromOpdUnit->nama_opd ?? '-' }}</small>
                                        </p>
                                        <small class="text-muted">{{ \Carbon\Carbon::parse($mutation->mutation_date)->format('d/m/Y') }}</small>
                                        <div class="mt-2">
                                            <a href="{{ route('opd.transactions.show', ['mutation', $mutation->mutation_id]) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Lihat
                                            </a>
                                            @if($mutation->status == 'disetujui')
                                            <button type="button" class="btn btn-sm btn-success" 
                                                    onclick="acceptMutation({{ $mutation->mutation_id }})">
                                                <i class="fas fa-check"></i> Terima
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $data['incomingMutations']->links() }}
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <i class="fas fa-inbox fa-2x text-muted mb-3"></i>
                                    <p class="text-muted">Tidak ada mutasi masuk</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
            @elseif($tab == 'maintenances')
                <!-- Maintenances Tab -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <form method="GET" class="row g-3">
                                    <input type="hidden" name="tab" value="maintenances">
                                    <div class="col-md-3">
                                        <select name="status" class="form-select">
                                            <option value="">Semua Status</option>
                                            <option value="dijadwalkan" {{ $status == 'dijadwalkan' ? 'selected' : '' }}>Dijadwalkan</option>
                                            <option value="dalam_pengerjaan" {{ $status == 'dalam_pengerjaan' ? 'selected' : '' }}>Dalam Pengerjaan</option>
                                            <option value="selesai" {{ $status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                                            <option value="dibatalkan" {{ $status == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-filter"></i> Filter
                                        </button>
                                    </div>
                                    <div class="col-md-2">
                                        <a href="{{ route('opd.transactions.index', ['tab' => 'maintenances']) }}" class="btn btn-outline-secondary w-100">
                                            Reset
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Jadwal Pemeliharaan</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table data-table">
                                        <thead>
                                            <tr>
                                                <th>Tanggal</th>
                                                <th>Aset</th>
                                                <th>Jenis</th>
                                                <th>Judul</th>
                                                <th>Vendor</th>
                                                <th>Biaya</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($data['maintenances'] as $maintenance)
                                            <tr>
                                                <td>
                                                    <small class="d-block">{{ \Carbon\Carbon::parse($maintenance->scheduled_date)->format('d/m/Y') }}</small>
                                                    @if($maintenance->actual_date)
                                                    <small class="text-muted">Aktual: {{ \Carbon\Carbon::parse($maintenance->actual_date)->format('d/m/Y') }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <strong>{{ $maintenance->asset->name }}</strong>
                                                    <br><small class="text-muted">{{ $maintenance->asset->asset_code }}</small>
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
                                                    @if($maintenance->status == 'dijadwalkan')
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="cancelTransaction('maintenance', {{ $maintenance->maintenance_id }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                    @endif
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="d-flex justify-content-center">
                                    {{ $data['maintenances']->appends(request()->all())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header bg-warning text-white">
                                <h6 class="mb-0">Pemeliharaan Terlambat</h6>
                            </div>
                            <div class="card-body">
                                @if($data['overdueMaintenances']->count() > 0)
                                <div class="list-group">
                                    @foreach($data['overdueMaintenances'] as $maintenance)
                                    <div class="list-group-item list-group-item-warning">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1">{{ $maintenance->asset->name }}</h6>
                                            <span class="badge bg-danger">Terlambat</span>
                                        </div>
                                        <p class="mb-1">
                                            <small>{{ $maintenance->title }}</small>
                                        </p>
                                        <small class="text-muted">
                                            Jadwal: {{ \Carbon\Carbon::parse($maintenance->scheduled_date)->format('d/m/Y') }}
                                            <br>
                                            Terlambat: {{ \Carbon\Carbon::parse($maintenance->scheduled_date)->diffForHumans() }}
                                        </small>
                                        <div class="mt-2">
                                            <a href="{{ route('opd.transactions.show', ['maintenance', $maintenance->maintenance_id]) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Lihat
                                            </a>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $data['overdueMaintenances']->links() }}
                                </div>
                                @else
                                <div class="text-center py-4">
                                    <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
                                    <p class="text-success">Tidak ada pemeliharaan terlambat</p>
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

<!-- Cancel Transaction Modal -->
<div class="modal fade" id="cancelTransactionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Batalkan Transaksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="cancelTransactionForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="cancellation_reason" class="form-label">Alasan Pembatalan *</label>
                        <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3" required 
                                  placeholder="Berikan alasan pembatalan transaksi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Konfirmasi Pembatalan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentTransactionType = '';
let currentTransactionId = '';

function cancelTransaction(type, id) {
    currentTransactionType = type;
    currentTransactionId = id;
    
    // Set form action
    $('#cancelTransactionForm').attr('action', `/opd/transactions/${type}/${id}/cancel`);
    
    // Show modal
    new bootstrap.Modal(document.getElementById('cancelTransactionModal')).show();
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

$('#cancelTransactionForm').on('submit', function(e) {
    e.preventDefault();
    const formData = $(this).serialize();
    
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: formData,
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
});
</script>
@endpush
@endsection