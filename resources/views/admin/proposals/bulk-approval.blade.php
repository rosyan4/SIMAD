@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-0">
                    <i class="fas fa-bolt me-2"></i>{{ $title }}
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.proposals.index') }}">Proposal</a></li>
                        <li class="breadcrumb-item active">Persetujuan Massal</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.proposals.index', ['type' => $type, 'status' => 'diusulkan']) }}" 
               class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card-custom">
            <div class="card-header">
                <i class="fas fa-list-check me-2"></i> Pilih Proposal untuk Persetujuan Massal
            </div>
            <div class="card-body">
                <form id="bulkApprovalForm" action="{{ route('admin.proposals.process-bulk-approval') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    
                    <!-- Filter Options -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Filter OPD</label>
                            <select class="form-select" id="opdFilter">
                                <option value="">Semua OPD</option>
                                @php
                                    $opds = [];
                                    foreach($proposals as $proposal) {
                                        if($type == 'mutations') {
                                            $opdName = $proposal->fromOpdUnit->nama_opd ?? 'Unknown';
                                        } else {
                                            $opdName = $proposal->proposer->opdUnit->nama_opd ?? 'Unknown';
                                        }
                                        if(!in_array($opdName, $opds)) {
                                            $opds[] = $opdName;
                                        }
                                    }
                                    sort($opds);
                                @endphp
                                
                                @foreach($opds as $opdName)
                                    <option value="{{ $opdName }}">{{ $opdName }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Urutkan Berdasarkan</label>
                            <select class="form-select" id="sortFilter">
                                <option value="date_desc">Tanggal (Terbaru)</option>
                                <option value="date_asc">Tanggal (Terlama)</option>
                                <option value="value_desc">Nilai (Tertinggi)</option>
                                <option value="value_asc">Nilai (Terendah)</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Proposals List -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="badge bg-primary">{{ count($proposals) }} proposal tersedia</span>
                                <small class="text-muted ms-2">Pilih proposal untuk disetujui</small>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label" for="selectAll">
                                    Pilih Semua
                                </label>
                            </div>
                        </div>
                        
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-hover" id="proposalsTable">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th width="30">
                                            <input type="checkbox" id="selectAllCheckbox">
                                        </th>
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
                                        <th>Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($proposals as $proposal)
                                        <tr class="proposal-row" 
                                            data-opd="{{ $type == 'mutations' ? ($proposal->fromOpdUnit->nama_opd ?? '') : ($proposal->proposer->opdUnit->nama_opd ?? '') }}"
                                            data-value="{{ $proposal->asset->value ?? 0 }}"
                                            data-date="{{ $type == 'mutations' ? $proposal->mutation_date->timestamp : $proposal->proposed_at->timestamp }}">
                                            <td>
                                                <input type="checkbox" name="proposal_ids[]" 
                                                       value="{{ $type == 'mutations' ? $proposal->mutation_id : $proposal->deletion_id }}" 
                                                       class="proposal-checkbox">
                                            </td>
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
                                                </td>
                                                <td>{{ $proposal->proposer->name ?? '-' }}</td>
                                                <td>{{ $proposal->proposed_at->translatedFormat('d/m/Y') }}</td>
                                            @endif
                                            
                                            <td>
                                                @if($proposal->asset)
                                                    {{ $proposal->asset->formatted_value }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="{{ $type == 'mutations' ? 8 : 8 }}" class="text-center py-4 text-muted">
                                                <i class="fas fa-inbox fa-2x mb-3"></i>
                                                <p>Tidak ada proposal yang perlu persetujuan</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if(count($proposals) > 0)
                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <span id="selectedCount" class="badge bg-success">0</span> proposal terpilih
                                    <span id="totalValue" class="badge bg-info ms-2">Rp 0</span>
                                </div>
                                <div>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="clearSelection()">
                                        <i class="fas fa-times me-1"></i> Bersihkan Pilihan
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    @if(count($proposals) > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Perhatian:</strong> Aksi ini akan menyetujui semua proposal yang dipilih. 
                            @if($type == 'deletions')
                                Pastikan proposal sudah diverifikasi sebelum disetujui.
                            @endif
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.proposals.index', ['type' => $type, 'status' => 'diusulkan']) }}" 
                               class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                <i class="fas fa-thumbs-up me-1"></i> Setujui 
                                <span id="processCount"></span>
                            </button>
                        </div>
                    @endif
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Summary Card -->
        <div class="card-custom mb-4">
            <div class="card-header">
                <i class="fas fa-chart-bar me-2"></i> Ringkasan
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted">Total Proposal Tersedia</label>
                    <h3 class="mb-0">{{ count($proposals) }}</h3>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted">Total Nilai Aset</label>
                    <h4 class="mb-0">
                        @php
                            $totalValue = 0;
                            foreach($proposals as $proposal) {
                                if($proposal->asset) {
                                    $totalValue += $proposal->asset->value;
                                }
                            }
                        @endphp
                        Rp {{ number_format($totalValue, 0, ',', '.') }}
                    </h4>
                </div>
                
                @if($type == 'deletions')
                    <div class="mb-3">
                        <label class="form-label text-muted">Per Alasan Penghapusan</label>
                        <div class="list-group list-group-flush">
                            @php
                                $reasonCounts = [];
                                foreach($proposals as $proposal) {
                                    $reason = $proposal->deletion_reason_display;
                                    $reasonCounts[$reason] = ($reasonCounts[$reason] ?? 0) + 1;
                                }
                                arsort($reasonCounts);
                            @endphp
                            
                            @foreach($reasonCounts as $reason => $count)
                                <div class="list-group-item d-flex justify-content-between px-0 py-2">
                                    <span class="text-truncate" style="max-width: 70%;">{{ $reason }}</span>
                                    <span class="badge bg-primary">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
                
                <div class="mb-3">
                    <label class="form-label text-muted">Statistik OPD</label>
                    <div class="progress mb-2" style="height: 20px;">
                        @php
                            $opdStats = [];
                            foreach($proposals as $proposal) {
                                if($type == 'mutations') {
                                    $opdName = $proposal->fromOpdUnit->nama_opd ?? 'Unknown';
                                } else {
                                    $opdName = $proposal->proposer->opdUnit->nama_opd ?? 'Unknown';
                                }
                                $opdStats[$opdName] = ($opdStats[$opdName] ?? 0) + 1;
                            }
                            $totalProposals = count($proposals);
                        @endphp
                        
                        @foreach(array_slice($opdStats, 0, 3) as $opdName => $count)
                            @php
                                $percentage = ($count / $totalProposals) * 100;
                                $colors = ['bg-primary', 'bg-success', 'bg-info'];
                                $colorIndex = $loop->index;
                            @endphp
                            <div class="progress-bar {{ $colors[$colorIndex] }}" 
                                 style="width: {{ $percentage }}%"
                                 title="{{ $opdName }}: {{ $count }} proposal ({{ round($percentage, 1) }}%)">
                            </div>
                        @endforeach
                    </div>
                    <small class="text-muted">{{ count($opdStats) }} OPD memiliki proposal</small>
                </div>
            </div>
        </div>
        
        <!-- Action Tips -->
        <div class="card-custom">
            <div class="card-header">
                <i class="fas fa-lightbulb me-2"></i> Panduan Persetujuan Massal
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Proses Persetujuan:</h6>
                    <ul class="mb-0 ps-3">
                        <li>Pilih proposal yang akan disetujui</li>
                        <li>Gunakan filter untuk menyaring proposal</li>
                        <li>Klik "Setujui" untuk proses massal</li>
                        <li>Proses berjalan di background</li>
                        @if($type == 'deletions')
                            <li>Pastikan proposal sudah diverifikasi</li>
                        @endif
                    </ul>
                </div>
                
                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Perhatian:</h6>
                    <ul class="mb-0 ps-3">
                        <li>Aksi tidak dapat dibatalkan</li>
                        <li>Proposal yang disetujui akan berubah status</li>
                        @if($type == 'mutations')
                            <li>Mutasi yang disetujui dapat dilanjutkan ke proses selesai</li>
                        @else
                            <li>Penghapusan yang disetujui dapat dilanjutkan ke proses selesai</li>
                        @endif
                        <li>Notifikasi akan dikirim ke pengusul</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let selectedProposals = new Set();
    let totalSelectedValue = 0;
    
    // Initialize checkboxes
    $(document).ready(function() {
        updateSelectedCount();
        
        // Select all checkboxes
        $('#selectAll, #selectAllCheckbox').change(function() {
            const isChecked = $(this).is(':checked');
            $('.proposal-checkbox').prop('checked', isChecked);
            
            if (isChecked) {
                $('.proposal-checkbox').each(function() {
                    const proposalId = $(this).val();
                    if (!selectedProposals.has(proposalId)) {
                        selectedProposals.add(proposalId);
                        // Update total value
                        const row = $(this).closest('tr');
                        const valueText = row.find('td:last').text().replace(/[^0-9]/g, '');
                        const value = parseInt(valueText) || 0;
                        totalSelectedValue += value;
                    }
                });
            } else {
                selectedProposals.clear();
                totalSelectedValue = 0;
            }
            
            updateSelectedCount();
        });
        
        // Individual checkbox change
        $('.proposal-checkbox').change(function() {
            const proposalId = $(this).val();
            const row = $(this).closest('tr');
            const valueText = row.find('td:last').text().replace(/[^0-9]/g, '');
            const value = parseInt(valueText) || 0;
            
            if ($(this).is(':checked')) {
                selectedProposals.add(proposalId);
                totalSelectedValue += value;
            } else {
                selectedProposals.delete(proposalId);
                totalSelectedValue -= value;
                $('#selectAll, #selectAllCheckbox').prop('checked', false);
            }
            
            updateSelectedCount();
        });
        
        // Form submission
        $('#bulkApprovalForm').submit(function(e) {
            if (selectedProposals.size === 0) {
                e.preventDefault();
                alert('Pilih minimal 1 proposal untuk disetujui');
                return false;
            }
            
            const proposalType = "{{ $type == 'mutations' ? 'mutasi' : 'penghapusan' }}";
            const totalValue = totalSelectedValue.toLocaleString('id-ID');
            
            return confirm(`Anda yakin ingin menyetujui ${selectedProposals.size} proposal ${proposalType} dengan total nilai Rp ${totalValue}?`);
        });
        
        // Filter functionality
        $('#opdFilter').change(function() {
            filterProposals();
        });
        
        $('#sortFilter').change(function() {
            sortProposals();
        });
    });
    
    function updateSelectedCount() {
        const count = $('.proposal-checkbox:checked').length;
        $('#selectedCount').text(count);
        $('#totalValue').text('Rp ' + totalSelectedValue.toLocaleString('id-ID'));
        
        if (count > 0) {
            $('#submitBtn').prop('disabled', false);
            $('#processCount').html(`(${count} proposal)`);
        } else {
            $('#submitBtn').prop('disabled', true);
            $('#processCount').html('');
        }
        
        // Update select all checkbox state
        const totalProposals = $('.proposal-checkbox').length;
        const allChecked = count === totalProposals && totalProposals > 0;
        $('#selectAll, #selectAllCheckbox').prop('checked', allChecked);
    }
    
    function clearSelection() {
        $('.proposal-checkbox').prop('checked', false);
        selectedProposals.clear();
        totalSelectedValue = 0;
        updateSelectedCount();
    }
    
    function filterProposals() {
        const selectedOpd = $('#opdFilter').val();
        
        $('.proposal-row').each(function() {
            const rowOpd = $(this).data('opd');
            
            if (!selectedOpd || rowOpd === selectedOpd) {
                $(this).show();
            } else {
                $(this).hide();
                // Uncheck hidden rows
                $(this).find('.proposal-checkbox').prop('checked', false);
            }
        });
        
        // Update selection after filtering
        selectedProposals.clear();
        totalSelectedValue = 0;
        $('.proposal-checkbox:checked:visible').each(function() {
            const proposalId = $(this).val();
            selectedProposals.add(proposalId);
            
            const row = $(this).closest('tr');
            const valueText = row.find('td:last').text().replace(/[^0-9]/g, '');
            const value = parseInt(valueText) || 0;
            totalSelectedValue += value;
        });
        
        updateSelectedCount();
    }
    
    function sortProposals() {
        const sortBy = $('#sortFilter').val();
        const tbody = $('#proposalsTable tbody');
        const rows = tbody.find('tr.proposal-row').toArray();
        
        rows.sort(function(a, b) {
            const aValue = $(a).data('value');
            const bValue = $(b).data('value');
            const aDate = $(a).data('date');
            const bDate = $(b).data('date');
            
            switch(sortBy) {
                case 'date_desc':
                    return bDate - aDate;
                case 'date_asc':
                    return aDate - bDate;
                case 'value_desc':
                    return bValue - aValue;
                case 'value_asc':
                    return aValue - bValue;
                default:
                    return 0;
            }
        });
        
        // Reappend rows in sorted order
        $.each(rows, function(index, row) {
            tbody.append(row);
        });
    }
</script>
@endpush
@endsection