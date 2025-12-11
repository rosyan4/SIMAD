@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0">
                <i class="fas fa-exchange-alt me-2"></i>{{ $title }}
            </h2>
            <div class="d-flex gap-2">
                @if($status == 'diusulkan')
                    <a href="{{ route('admin.proposals.bulk-approval', ['type' => $type]) }}" 
                       class="btn btn-primary btn-sm">
                        <i class="fas fa-bolt me-1"></i> Persetujuan Massal
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Filter & Type Selector -->
<div class="card-custom mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.proposals.index', ['type' => 'mutations']) }}" 
                       class="btn btn-outline-primary {{ $type == 'mutations' ? 'active' : '' }}">
                        <i class="fas fa-exchange-alt me-1"></i> Mutasi
                    </a>
                    <a href="{{ route('admin.proposals.index', ['type' => 'deletions']) }}" 
                       class="btn btn-outline-danger {{ $type == 'deletions' ? 'active' : '' }}">
                        <i class="fas fa-trash me-1"></i> Penghapusan
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <form method="GET" class="d-flex gap-2">
                    <input type="hidden" name="type" value="{{ $type }}">
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s }}" {{ ($status ?? '') == $s ? 'selected' : '' }}>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Proposals Table -->
<div class="card-custom">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-table me-2"></i> Daftar Proposal
            <span class="badge bg-primary ms-2">{{ $proposals->total() }} data</span>
        </div>
        <div class="text-muted">
            @if($type == 'mutations')
                <i class="fas fa-exchange-alt me-1"></i> Mutasi Aset
            @else
                <i class="fas fa-trash me-1"></i> Penghapusan Aset
            @endif
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>ID Proposal</th>
                        @if($type == 'mutations')
                            <th>Aset</th>
                            <th>Dari OPD</th>
                            <th>Ke OPD</th>
                            <th>Tanggal</th>
                        @else
                            <th>Aset</th>
                            <th>Alasan</th>
                            <th>Pengusul</th>
                            <th>Tanggal Usul</th>
                        @endif
                        <th>Status</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($proposals as $index => $proposal)
                        <tr>
                            <td>{{ $proposals->firstItem() + $index }}</td>
                            <td>
                                <strong>
                                    @if($type == 'mutations')
                                        MUT-{{ str_pad($proposal->mutation_id, 6, '0', STR_PAD_LEFT) }}
                                    @else
                                        DEL-{{ str_pad($proposal->deletion_id, 6, '0', STR_PAD_LEFT) }}
                                    @endif
                                </strong>
                            </td>
                            
                            @if($type == 'mutations')
                                <td>
                                    <div>{{ $proposal->asset->name ?? 'Aset tidak ditemukan' }}</div>
                                    <small class="text-muted">{{ $proposal->asset->asset_code ?? '' }}</small>
                                </td>
                                <td>{{ $proposal->fromOpdUnit->nama_opd ?? '-' }}</td>
                                <td>{{ $proposal->toOpdUnit->nama_opd ?? '-' }}</td>
                                <td>{{ $proposal->mutation_date->translatedFormat('d/m/Y') }}</td>
                            @else
                                <td>
                                    <div>{{ $proposal->asset->name ?? 'Aset tidak ditemukan' }}</div>
                                    <small class="text-muted">{{ $proposal->asset->asset_code ?? '' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $proposal->deletion_reason_display }}</span>
                                    @if($proposal->reason_details)
                                        <div class="mt-1">
                                            <small class="text-muted">{{ Str::limit($proposal->reason_details, 50) }}</small>
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $proposal->proposer->name ?? '-' }}</td>
                                <td>{{ $proposal->proposed_at->translatedFormat('d/m/Y') }}</td>
                            @endif
                            
                            <td>
                                @php
                                    if($type == 'mutations') {
                                        $statusClass = match($proposal->status) {
                                            'selesai' => 'success',
                                            'disetujui' => 'info',
                                            'diusulkan' => 'warning',
                                            'ditolak' => 'danger',
                                            default => 'secondary'
                                        };
                                    } else {
                                        $statusClass = match($proposal->status) {
                                            'selesai' => 'success',
                                            'disetujui' => 'info',
                                            'diusulkan' => 'warning',
                                            'ditolak' => 'danger',
                                            'dibatalkan' => 'secondary',
                                            default => 'secondary'
                                        };
                                    }
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($proposal->status) }}</span>
                            </td>
                            
                            <td class="table-actions">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.proposals.show', ['type' => $type == 'mutations' ? 'mutation' : 'deletion', 'id' => $proposal->{$type == 'mutations' ? 'mutation_id' : 'deletion_id'}]) }}" 
                                       class="btn btn-outline-primary" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    @if($proposal->status == 'diusulkan')
                                        @if($type == 'deletions' && !$proposal->verified_by)
                                            <button type="button" class="btn btn-outline-warning" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#verifyModal{{ $proposal->deletion_id }}"
                                                    title="Verifikasi">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        
                                        <button type="button" class="btn btn-outline-success"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#approveModal{{ $proposal->{$type == 'mutations' ? 'mutation_id' : 'deletion_id'} }}"
                                                title="Setujui">
                                            <i class="fas fa-thumbs-up"></i>
                                        </button>
                                        
                                        <button type="button" class="btn btn-outline-danger"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#rejectModal{{ $proposal->{$type == 'mutations' ? 'mutation_id' : 'deletion_id'} }}"
                                                title="Tolak">
                                            <i class="fas fa-thumbs-down"></i>
                                        </button>
                                    @endif
                                    
                                    @if($type == 'deletions' && $proposal->status == 'disetujui')
                                        <button type="button" class="btn btn-outline-info"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#completeModal{{ $proposal->deletion_id }}"
                                                title="Selesaikan">
                                            <i class="fas fa-check-double"></i>
                                        </button>
                                    @endif
                                    
                                    @if($type == 'deletions' && $proposal->status == 'diusulkan')
                                        <button type="button" class="btn btn-outline-secondary"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#cancelModal{{ $proposal->deletion_id }}"
                                                title="Batalkan">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $type == 'mutations' ? 9 : 8 }}" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-3"></i>
                                <p>Tidak ada data proposal</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="card-footer">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                Menampilkan {{ $proposals->firstItem() }} sampai {{ $proposals->lastItem() }} dari {{ $proposals->total() }} data
            </div>
            <div>
                {{ $proposals->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modals will be dynamically generated for each proposal -->
@foreach($proposals as $proposal)
    @if($type == 'deletions' && $proposal->status == 'diusulkan' && !$proposal->verified_by)
        <!-- Verification Modal -->
        <div class="modal fade" id="verifyModal{{ $proposal->deletion_id }}" tabindex="-1">
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
    @if($proposal->status == 'diusulkan')
        <div class="modal fade" id="approveModal{{ $proposal->{$type == 'mutations' ? 'mutation_id' : 'deletion_id'} }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.proposals.approve', ['type' => $type == 'mutations' ? 'mutation' : 'deletion', 'id' => $proposal->{$type == 'mutations' ? 'mutation_id' : 'deletion_id'}]) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">Setujui Proposal</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>Anda yakin ingin menyetujui proposal ini?</p>
                            
                            @if($type == 'deletions')
                                <div class="mb-3">
                                    <label class="form-label">Dokumen Persetujuan (opsional)</label>
                                    <input type="text" name="approval_documents[]" class="form-control form-control-sm mb-2" placeholder="Nama dokumen 1">
                                    <input type="text" name="approval_documents[]" class="form-control form-control-sm" placeholder="Nama dokumen 2">
                                    <small class="text-muted">Tambahkan dokumen pendukung persetujuan</small>
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
        <div class="modal fade" id="rejectModal{{ $proposal->{$type == 'mutations' ? 'mutation_id' : 'deletion_id'} }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('admin.proposals.reject', ['type' => $type == 'mutations' ? 'mutation' : 'deletion', 'id' => $proposal->{$type == 'mutations' ? 'mutation_id' : 'deletion_id'}]) }}" method="POST">
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
    @endif
    
    <!-- Complete Modal (for deletions only) -->
    @if($type == 'deletions' && $proposal->status == 'disetujui')
        <div class="modal fade" id="completeModal{{ $proposal->deletion_id }}" tabindex="-1">
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
                                <input type="number" name="sale_value" class="form-control" 
                                       placeholder="Rp 0" min="0">
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
    
    <!-- Cancel Modal (for deletions only) -->
    @if($type == 'deletions' && $proposal->status == 'diusulkan')
        <div class="modal fade" id="cancelModal{{ $proposal->deletion_id }}" tabindex="-1">
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
@endforeach
@endsection