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
                        <li class="breadcrumb-item"><a href="{{ route('admin.assets.index') }}">Aset</a></li>
                        <li class="breadcrumb-item active">Aksi Massal</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('admin.assets.index', ['type' => 'pending-' . $action]) }}" 
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
                <i class="fas fa-list-check me-2"></i> Pilih Aset untuk {{ $action == 'verification' ? 'Verifikasi' : 'Validasi' }} Massal
            </div>
            <div class="card-body">
                <form id="bulkActionForm" action="{{ route('admin.assets.process-bulk-actions') }}" method="POST">
                    @csrf
                    <input type="hidden" name="action" value="{{ $action }}">
                    
                    <!-- Action Settings -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Status</label>
                            @if($action == 'verification')
                                <select name="status" class="form-select" required>
                                    <option value="valid">Valid</option>
                                    <option value="tidak_valid">Tidak Valid</option>
                                </select>
                                <small class="text-muted">Status verifikasi dokumen</small>
                            @else
                                <select name="status" class="form-select" required>
                                    <option value="disetujui">Disetujui</option>
                                    <option value="revisi">Perlu Revisi</option>
                                    <option value="ditolak">Ditolak</option>
                                </select>
                                <small class="text-muted">Status validasi aset</small>
                            @endif
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Catatan (Opsional)</label>
                            <textarea name="notes" class="form-control" rows="1" 
                                      placeholder="Catatan untuk semua aset yang dipilih..."></textarea>
                        </div>
                    </div>
                    
                    <!-- Assets List -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="badge bg-primary">{{ count($assets) }} aset tersedia</span>
                                <small class="text-muted ms-2">Pilih aset untuk diproses</small>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label" for="selectAll">
                                    Pilih Semua
                                </label>
                            </div>
                        </div>
                        
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table table-sm table-hover">
                                <thead class="table-light sticky-top">
                                    <tr>
                                        <th width="30">
                                            <input type="checkbox" id="selectAllCheckbox">
                                        </th>
                                        <th>Kode Aset</th>
                                        <th>Nama Aset</th>
                                        <th>OPD</th>
                                        <th>Kategori</th>
                                        <th>Nilai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($assets as $asset)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="asset_ids[]" 
                                                       value="{{ $asset->asset_id }}" 
                                                       class="asset-checkbox">
                                            </td>
                                            <td>
                                                <strong>{{ $asset->asset_code }}</strong>
                                            </td>
                                            <td>{{ $asset->name }}</td>
                                            <td>{{ $asset->opdUnit->kode_opd ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-info">{{ $asset->category->kib_code ?? '-' }}</span>
                                            </td>
                                            <td>{{ $asset->formatted_value }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                <i class="fas fa-inbox fa-2x mb-3"></i>
                                                <p>Tidak ada aset yang perlu {{ $action == 'verification' ? 'verifikasi' : 'validasi' }}</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        @if(count($assets) > 0)
                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <span id="selectedCount" class="badge bg-success">0</span> aset terpilih
                                </div>
                                <div>
                                    <button type="button" class="btn btn-secondary btn-sm" onclick="clearSelection()">
                                        <i class="fas fa-times me-1"></i> Bersihkan Pilihan
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    @if(count($assets) > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Perhatian:</strong> Aksi ini akan {{ $action == 'verification' ? 'memverifikasi' : 'memvalidasi' }} 
                            semua aset yang dipilih dengan status dan catatan yang sama.
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.assets.index', ['type' => 'pending-' . $action]) }}" 
                               class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                <i class="fas fa-play me-1"></i> Proses {{ count($assets) > 0 ? '' : '' }}
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
                    <label class="form-label text-muted">Total Aset Tersedia</label>
                    <h3 class="mb-0">{{ count($assets) }}</h3>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted">Per OPD</label>
                    <div class="list-group list-group-flush">
                        @php
                            $opdCounts = [];
                            foreach($assets as $asset) {
                                $opdName = $asset->opdUnit->nama_opd ?? 'Unknown';
                                $opdCounts[$opdName] = ($opdCounts[$opdName] ?? 0) + 1;
                            }
                            arsort($opdCounts);
                        @endphp
                        
                        @foreach(array_slice($opdCounts, 0, 5) as $opdName => $count)
                            <div class="list-group-item d-flex justify-content-between px-0 py-2">
                                <span class="text-truncate" style="max-width: 70%;">{{ $opdName }}</span>
                                <span class="badge bg-primary">{{ $count }}</span>
                            </div>
                        @endforeach
                        
                        @if(count($opdCounts) > 5)
                            <div class="list-group-item px-0 py-2 text-center">
                                <small class="text-muted">dan {{ count($opdCounts) - 5 }} OPD lainnya...</small>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label text-muted">Per Kategori</label>
                    <div class="list-group list-group-flush">
                        @php
                            $categoryCounts = [];
                            foreach($assets as $asset) {
                                $categoryName = $asset->category->name ?? 'Unknown';
                                $categoryCounts[$categoryName] = ($categoryCounts[$categoryName] ?? 0) + 1;
                            }
                            arsort($categoryCounts);
                        @endphp
                        
                        @foreach(array_slice($categoryCounts, 0, 5) as $categoryName => $count)
                            <div class="list-group-item d-flex justify-content-between px-0 py-2">
                                <span class="text-truncate" style="max-width: 70%;">{{ $categoryName }}</span>
                                <span class="badge bg-info">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Tips -->
        <div class="card-custom">
            <div class="card-header">
                <i class="fas fa-lightbulb me-2"></i> Tips Aksi Massal
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fas fa-info-circle me-2"></i>Panduan Singkat:</h6>
                    <ul class="mb-0 ps-3">
                        <li>Pilih aset yang akan diproses</li>
                        <li>Pilih status {{ $action == 'verification' ? 'verifikasi' : 'validasi' }}</li>
                        <li>Tambahkan catatan jika diperlukan</li>
                        <li>Klik "Proses" untuk menjalankan aksi</li>
                        <li>Proses mungkin memerlukan waktu beberapa saat</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning">
                    <h6><i class="fas fa-exclamation-triangle me-2"></i>Perhatian:</h6>
                    <ul class="mb-0 ps-3">
                        <li>Aksi tidak dapat dibatalkan</li>
                        <li>Pastikan semua pilihan sudah benar</li>
                        <li>Status dan catatan akan sama untuk semua aset</li>
                        <li>Proses akan berjalan di background</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let selectedAssets = new Set();
    
    // Initialize checkboxes
    $(document).ready(function() {
        updateSelectedCount();
        
        // Select all checkboxes
        $('#selectAll, #selectAllCheckbox').change(function() {
            const isChecked = $(this).is(':checked');
            $('.asset-checkbox').prop('checked', isChecked);
            
            if (isChecked) {
                $('.asset-checkbox').each(function() {
                    selectedAssets.add($(this).val());
                });
            } else {
                selectedAssets.clear();
            }
            
            updateSelectedCount();
        });
        
        // Individual checkbox change
        $('.asset-checkbox').change(function() {
            const assetId = $(this).val();
            if ($(this).is(':checked')) {
                selectedAssets.add(assetId);
            } else {
                selectedAssets.delete(assetId);
                $('#selectAll, #selectAllCheckbox').prop('checked', false);
            }
            
            updateSelectedCount();
        });
        
        // Form submission
        $('#bulkActionForm').submit(function(e) {
            if (selectedAssets.size === 0) {
                e.preventDefault();
                alert('Pilih minimal 1 aset untuk diproses');
                return false;
            }
            
            const actionType = $('select[name="status"]').val();
            const actionName = "{{ $action == 'verification' ? 'verifikasi' : 'validasi' }}";
            const statusText = $('select[name="status"] option:selected').text();
            
            return confirm(`Anda yakin ingin ${actionName} ${selectedAssets.size} aset dengan status "${statusText}"?`);
        });
    });
    
    function updateSelectedCount() {
        const count = $('.asset-checkbox:checked').length;
        $('#selectedCount').text(count);
        
        if (count > 0) {
            $('#submitBtn').prop('disabled', false);
            $('#processCount').html(`(${count} aset)`);
        } else {
            $('#submitBtn').prop('disabled', true);
            $('#processCount').html('');
        }
        
        // Update select all checkbox state
        const totalAssets = $('.asset-checkbox').length;
        const allChecked = count === totalAssets && totalAssets > 0;
        $('#selectAll, #selectAllCheckbox').prop('checked', allChecked);
    }
    
    function clearSelection() {
        $('.asset-checkbox').prop('checked', false);
        selectedAssets.clear();
        updateSelectedCount();
    }
</script>
@endpush
@endsection