@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0"><i class="fas fa-clipboard-check me-2"></i>{{ $title }}</h2>
            <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createAuditModal">
                <i class="fas fa-plus me-1"></i> Buat Audit
            </a>
        </div>
    </div>
</div>

<!-- Filter & Type Selector -->
<div class="card-custom mb-4">
    <div class="card-body">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.audits.index') }}" 
                       class="btn btn-outline-primary {{ $type == 'all' ? 'active' : '' }}">
                        Semua
                    </a>
                    <a href="{{ route('admin.audits.index', ['type' => 'follow-up']) }}" 
                       class="btn btn-outline-warning {{ $type == 'follow-up' ? 'active' : '' }}">
                        Follow Up
                    </a>
                    <a href="{{ route('admin.audits.index', ['type' => 'overdue']) }}" 
                       class="btn btn-outline-danger {{ $type == 'overdue' ? 'active' : '' }}">
                        Overdue
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <form method="GET" class="d-flex gap-2">
                    @if($type != 'all')
                        <input type="hidden" name="type" value="{{ $type }}">
                    @endif
                    <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        @foreach($statuses as $s)
                            <option value="{{ $s }}" {{ ($status ?? '') == $s ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $s)) }}
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Audits Table -->
<div class="card-custom">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-table me-2"></i> Daftar Laporan Audit
            <span class="badge bg-primary ms-2">{{ $audits->total() }} data</span>
        </div>
        <div class="text-muted">
            @if($type == 'follow-up')
                <i class="fas fa-exclamation-triangle me-1 text-warning"></i> Perlu Tindak Lanjut
            @elseif($type == 'overdue')
                <i class="fas fa-clock me-1 text-danger"></i> Melewati Batas Waktu
            @endif
        </div>
    </div>
    
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="50">#</th>
                        <th>Aset</th>
                        <th>Auditor</th>
                        <th>Tanggal Audit</th>
                        <th>Status</th>
                        <th>Tindak Lanjut</th>
                        <th>Temuan</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($audits as $index => $audit)
                        <tr>
                            <td>{{ $audits->firstItem() + $index }}</td>
                            <td>
                                <div>{{ $audit->asset->name ?? 'Aset tidak ditemukan' }}</div>
                                <small class="text-muted">{{ $audit->asset->asset_code ?? '' }}</small>
                            </td>
                            <td>{{ $audit->auditor->name ?? '-' }}</td>
                            <td>{{ $audit->audit_date->translatedFormat('d/m/Y') }}</td>
                            <td>
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
                                <span class="badge bg-{{ $statusClass }}">{{ $audit->status_display }}</span>
                            </td>
                            <td>
                                @if($audit->follow_up_deadline)
                                    <div>{{ $audit->follow_up_deadline->translatedFormat('d/m/Y') }}</div>
                                    @if($audit->follow_up_deadline->isPast() && $audit->status == 'follow_up')
                                        <small class="text-danger">Terlambat {{ $audit->follow_up_deadline->diffForHumans() }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ Str::limit($audit->findings, 50) }}</td>
                            <td class="table-actions">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.audits.show', $audit) }}" 
                                       class="btn btn-outline-primary" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($audit->report_file_path)
                                        <a href="{{ route('admin.audits.download-file', $audit) }}" 
                                           class="btn btn-outline-success" title="Download File">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif
                                    @if($audit->status == 'follow_up')
                                        <button type="button" class="btn btn-outline-warning"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#updateStatusModal{{ $audit->audit_id }}"
                                                title="Update Status">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        
                        <!-- Update Status Modal -->
                        @if($audit->status == 'follow_up')
                            <div class="modal fade" id="updateStatusModal{{ $audit->audit_id }}" tabindex="-1">
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
                                                        <option value="follow_up" {{ $audit->status == 'follow_up' ? 'selected' : '' }}>Follow Up</option>
                                                        <option value="completed">Completed</option>
                                                        <option value="cancelled">Cancelled</option>
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
                        @endif
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-3"></i>
                                <p>Tidak ada data laporan audit</p>
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
                Menampilkan {{ $audits->firstItem() }} sampai {{ $audits->lastItem() }} dari {{ $audits->total() }} data
            </div>
            <div>
                {{ $audits->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Create Audit Modal -->
<div class="modal fade" id="createAuditModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admin.audits.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Buat Laporan Audit Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pilih Aset</label>
                            <select name="asset_id" class="form-select" required>
                                <option value="">Pilih Aset</option>
                                <!-- Options will be loaded via AJAX -->
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Audit</label>
                            <input type="date" name="audit_date" class="form-control" 
                                   value="{{ date('Y-m-d') }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="draft">Draft</option>
                                <option value="submitted">Submitted</option>
                                <option value="follow_up">Follow Up</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Upload File Laporan (Opsional)</label>
                            <input type="file" name="report_file" class="form-control" accept=".pdf,.doc,.docx">
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">Temuan Audit</label>
                            <textarea name="findings" class="form-control" rows="5" required 
                                      placeholder="Tuliskan temuan audit..."></textarea>
                        </div>
                        
                        <div class="col-12 mb-3">
                            <label class="form-label">Tindak Lanjut (Opsional)</label>
                            <textarea name="follow_up" class="form-control" rows="3" 
                                      placeholder="Rekomendasi tindak lanjut..."></textarea>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Deadline Tindak Lanjut (Opsional)</label>
                            <input type="date" name="follow_up_deadline" class="form-control">
                        </div>
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
    // Load assets for select
    $(document).ready(function() {
        $.ajax({
            url: '{{ route("admin.assets.index") }}?per_page=1000',
            method: 'GET',
            success: function(response) {
                const select = $('#createAuditModal select[name="asset_id"]');
                select.empty().append('<option value="">Pilih Aset</option>');
                
                // Parse assets from response (adjust based on your actual response structure)
                if (response.data && response.data.length > 0) {
                    response.data.forEach(function(asset) {
                        select.append(`<option value="${asset.asset_id}">${asset.asset_code} - ${asset.name}</option>`);
                    });
                }
            }
        });
    });
</script>
@endpush
@endsection