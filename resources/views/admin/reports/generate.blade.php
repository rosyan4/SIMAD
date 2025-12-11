@extends('layouts.admin')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file-alt mr-2"></i>
                        {{ $title }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left mr-1"></i> Kembali
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <form id="reportForm" action="{{ route('admin.reports.process') }}" method="POST">
                        @csrf
                        
                        <input type="hidden" name="report_type" value="{{ $type }}">
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="format">Format Laporan *</label>
                                    <select name="format" id="format" class="form-control" required>
                                        <option value="view">Tampilan Web</option>
                                        <option value="pdf">PDF</option>
                                        <option value="excel">Excel</option>
                                    </select>
                                    <small class="form-text text-muted">Pilih format output laporan</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="opd_unit_id">Filter OPD (Opsional)</label>
                                    <select name="opd_unit_id" id="opd_unit_id" class="form-control select2">
                                        <option value="">Semua OPD</option>
                                        @foreach($opdUnits as $opd)
                                        <option value="{{ $opd->opd_unit_id }}">
                                            {{ $opd->kode_opd }} - {{ $opd->nama_opd }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        @if($type === 'asset_summary' || $type === 'audit')
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="year">Tahun *</label>
                                    <select name="year" id="year" class="form-control" required>
                                        @for($i = date('Y'); $i >= 2020; $i--)
                                        <option value="{{ $i }}" {{ $i == date('Y') ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        @if($type === 'mutation' || $type === 'deletion')
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Tanggal Mulai *</label>
                                    <input type="date" 
                                           name="start_date" 
                                           id="start_date" 
                                           class="form-control" 
                                           value="{{ date('Y-m-d', strtotime('-1 month')) }}"
                                           required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">Tanggal Akhir *</label>
                                    <input type="date" 
                                           name="end_date" 
                                           id="end_date" 
                                           class="form-control" 
                                           value="{{ date('Y-m-d') }}"
                                           required>
                                </div>
                            </div>
                        </div>
                        @endif
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h5><i class="fas fa-info-circle mr-2"></i>Informasi Laporan</h5>
                                    @if($type === 'asset_summary')
                                    <p class="mb-0">Laporan ini akan menampilkan ringkasan aset berdasarkan tahun, termasuk distribusi per kategori, kondisi, dan statistik bulanan.</p>
                                    @elseif($type === 'mutation')
                                    <p class="mb-0">Laporan ini akan menampilkan data mutasi aset dalam periode tertentu, termasuk aset yang dipindahkan antar OPD.</p>
                                    @elseif($type === 'deletion')
                                    <p class="mb-0">Laporan ini akan menampilkan data penghapusan aset dalam periode tertentu, termasuk alasan penghapusan dan nilai penjualan.</p>
                                    @elseif($type === 'audit')
                                    <p class="mb-0">Laporan ini akan menampilkan hasil audit aset dalam tahun tertentu, termasuk temuan dan status tindak lanjut.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <button type="button" class="btn btn-secondary mr-2" onclick="previewReport()">
                                    <i class="fas fa-eye mr-1"></i> Preview
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-file-download mr-1"></i> Generate Laporan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Inisialisasi select2
    $(document).ready(function() {
        $('#opd_unit_id').select2({
            placeholder: 'Pilih OPD...',
            allowClear: true
        });
        
        // Validasi tanggal
        $('#end_date').on('change', function() {
            var startDate = new Date($('#start_date').val());
            var endDate = new Date($(this).val());
            
            if (endDate < startDate) {
                alert('Tanggal akhir tidak boleh kurang dari tanggal mulai');
                $(this).val('');
            }
        });
    });
    
    function previewReport() {
        // Set format ke view untuk preview
        $('#format').val('view');
        $('#reportForm').submit();
    }
</script>
@endpush

@push('styles')
<style>
    .select2-container--default .select2-selection--single {
        height: calc(2.25rem + 2px);
        padding: .375rem .75rem;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
    }
    
    .card {
        box-shadow: 0 0 1px rgba(0,0,0,.125), 0 1px 3px rgba(0,0,0,.2);
        margin-bottom: 1rem;
    }
    
    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
</style>
@endpush