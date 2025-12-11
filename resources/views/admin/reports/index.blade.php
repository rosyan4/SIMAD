@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 mb-0"><i class="fas fa-chart-bar me-2"></i>{{ $title }}</h2>
        </div>
    </div>
</div>

<div class="row">
    @foreach($reports as $report)
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card-custom h-100">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="{{ $report['icon'] }} fa-3x" style="color: var(--secondary-color);"></i>
                    </div>
                    <h5 class="card-title">{{ $report['name'] }}</h5>
                    <p class="card-text text-muted small">
                        @if($report['type'] == 'asset_summary')
                            Ringkasan data aset per periode
                        @elseif($report['type'] == 'mutation')
                            Laporan perpindahan aset antar OPD
                        @elseif($report['type'] == 'deletion')
                            Laporan penghapusan aset
                        @elseif($report['type'] == 'audit')
                            Laporan hasil audit aset
                        @endif
                    </p>
                    <a href="{{ route('admin.reports.generate', $report['type']) }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-cog me-1"></i> Generate
                    </a>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Recent Generated Reports -->
<div class="card-custom mt-4">
    <div class="card-header">
        <i class="fas fa-history me-2"></i> Laporan Terbaru
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Jenis Laporan</th>
                        <th>Periode</th>
                        <th>OPD</th>
                        <th>Format</th>
                        <th>Tanggal Generate</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Laporan Aset</td>
                        <td>2024</td>
                        <td>Semua OPD</td>
                        <td><span class="badge bg-success">PDF</span></td>
                        <td>15 Jan 2024 14:30</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download"></i>
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td>Laporan Mutasi</td>
                        <td>Des 2023</td>
                        <td>Dinas Pendidikan</td>
                        <td><span class="badge bg-info">Excel</span></td>
                        <td>10 Jan 2024 10:15</td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection