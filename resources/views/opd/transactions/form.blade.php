@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ $title }}</h5>
            </div>
            <div class="card-body">
                @if($type == 'deletion')
                    <!-- Deletion Form -->
                    <form method="POST" action="{{ route('opd.transactions.store-deletion') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2">Pilih Aset</h6>
                            <div class="mb-3">
                                <label for="asset_id" class="form-label">Aset yang akan Dihapus *</label>
                                <select class="form-select select2" id="asset_id" name="asset_id" required>
                                    <option value="">Pilih Aset</option>
                                    @foreach($data['assets'] as $asset)
                                    <option value="{{ $asset->asset_id }}" 
                                            {{ (request('asset_id') == $asset->asset_id) ? 'selected' : '' }}>
                                        {{ $asset->asset_code }} - {{ $asset->name }}
                                        ({{ $asset->location->name ?? 'Tidak ada lokasi' }}, Rp {{ number_format($asset->value, 0, ',', '.') }})
                                    </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Hanya aset dengan status aktif, terverifikasi, dan disetujui yang dapat diajukan penghapusan</small>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2">Alasan Penghapusan</h6>
                            <div class="mb-3">
                                <label for="deletion_reason" class="form-label">Alasan Penghapusan *</label>
                                <select class="form-select" id="deletion_reason" name="deletion_reason" required>
                                    <option value="">Pilih Alasan</option>
                                    <option value="rusak_berat">Rusak Berat</option>
                                    <option value="hilang">Hilang</option>
                                    <option value="jual">Dijual</option>
                                    <option value="hibah">Dihibahkan</option>
                                    <option value="musnah">Musnah (Kebakaran/Bencana)</option>
                                    <option value="lainnya">Lainnya</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="reason_details" class="form-label">Detail Alasan *</label>
                                <textarea class="form-control" id="reason_details" name="reason_details" 
                                          rows="4" required minlength="20" maxlength="1000"
                                          placeholder="Jelaskan secara detail alasan penghapusan aset..."></textarea>
                                <small class="text-muted">Minimal 20 karakter, maksimal 1000 karakter</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="notes" class="form-label">Catatan Tambahan</label>
                                <textarea class="form-control" id="notes" name="notes" 
                                          rows="2" maxlength="500"
                                          placeholder="Catatan tambahan (opsional)..."></textarea>
                            </div>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Perhatian:</strong> Pengajuan penghapusan aset akan melalui proses verifikasi dan persetujuan. Aset tidak dapat dihapus secara langsung.
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('opd.transactions.index', ['tab' => 'deletions']) }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-paper-plane me-1"></i> Ajukan Penghapusan
                            </button>
                        </div>
                    </form>
                    
                @elseif($type == 'mutation')
                    <!-- Mutation Form -->
                    <form method="POST" action="{{ route('opd.transactions.store-mutation') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2">Aset yang akan Dimutasi</h6>
                            <div class="mb-3">
                                <label for="asset_id" class="form-label">Pilih Aset *</label>
                                <select class="form-select select2" id="asset_id" name="asset_id" required>
                                    <option value="">Pilih Aset</option>
                                    @foreach($data['assets'] as $asset)
                                    <option value="{{ $asset->asset_id }}" 
                                            {{ (request('asset_id') == $asset->asset_id) ? 'selected' : '' }}>
                                        {{ $asset->asset_code }} - {{ $asset->name }}
                                        (Lokasi: {{ $asset->location->name ?? 'Tidak ada' }}, Nilai: Rp {{ number_format($asset->value, 0, ',', '.') }})
                                    </option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Hanya aset dengan status aktif, terverifikasi, dan disetujui yang dapat dimutasi</small>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2">Tujuan Mutasi</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="to_opd_unit_id" class="form-label">OPD Tujuan *</label>
                                    <select class="form-select select2" id="to_opd_unit_id" name="to_opd_unit_id" required>
                                        <option value="">Pilih OPD Tujuan</option>
                                        @foreach($data['opdUnits'] as $opdUnit)
                                        <option value="{{ $opdUnit->opd_unit_id }}">
                                            {{ $opdUnit->kode_opd }} - {{ $opdUnit->nama_opd }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="to_location_id" class="form-label">Lokasi Tujuan (Opsional)</label>
                                    <select class="form-select select2" id="to_location_id" name="to_location_id">
                                        <option value="">Pilih Lokasi</option>
                                        <option value="">Biarkan OPD tujuan menentukan</option>
                                    </select>
                                    <small class="text-muted">Jika tidak dipilih, OPD tujuan akan menentukan lokasi</small>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="mutation_date" class="form-label">Tanggal Mutasi *</label>
                                    <input type="date" class="form-control" id="mutation_date" name="mutation_date" 
                                           value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                                </div>
                                
                                <div class="col-md-12">
                                    <label for="notes" class="form-label">Catatan Mutasi</label>
                                    <textarea class="form-control" id="notes" name="notes" 
                                              rows="3" maxlength="500"
                                              placeholder="Berikan catatan atau informasi tambahan mengenai mutasi ini..."></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Informasi:</strong> 
                            <ul class="mb-0 mt-2">
                                <li>Mutasi harus disetujui oleh OPD tujuan</li>
                                <li>Aset akan berstatus "dimutasi" selama proses</li>
                                <li>Status akan berubah menjadi "aktif" setelah diterima oleh OPD tujuan</li>
                            </ul>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('opd.transactions.index', ['tab' => 'mutations']) }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-1"></i> Ajukan Mutasi
                            </button>
                        </div>
                    </form>
                    
                @elseif($type == 'maintenance')
                    <!-- Maintenance Form -->
                    <form method="POST" action="{{ route('opd.transactions.store-maintenance') }}">
                        @csrf
                        
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2">Aset yang akan Dipelihara</h6>
                            <div class="mb-3">
                                <label for="asset_id" class="form-label">Pilih Aset *</label>
                                <select class="form-select select2" id="asset_id" name="asset_id" required>
                                    <option value="">Pilih Aset</option>
                                    @foreach($data['assets'] as $asset)
                                    <option value="{{ $asset->asset_id }}" 
                                            {{ (isset($data['selectedAsset']) && $data['selectedAsset']->asset_id == $asset->asset_id) ? 'selected' : '' }}>
                                        {{ $asset->asset_code }} - {{ $asset->name }}
                                        (Kondisi: {{ $asset->condition }}, Lokasi: {{ $asset->location->name ?? 'Tidak ada' }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2">Detail Pemeliharaan</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="maintenance_type" class="form-label">Jenis Pemeliharaan *</label>
                                    <select class="form-select" id="maintenance_type" name="maintenance_type" required>
                                        <option value="">Pilih Jenis</option>
                                        @foreach($data['maintenanceTypes'] as $key => $label)
                                        <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="scheduled_date" class="form-label">Tanggal Dijadwalkan *</label>
                                    <input type="date" class="form-control" id="scheduled_date" name="scheduled_date" 
                                           value="{{ date('Y-m-d') }}" min="{{ date('Y-m-d') }}" required>
                                </div>
                                
                                <div class="col-md-12">
                                    <label for="title" class="form-label">Judul Pemeliharaan *</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           placeholder="Contoh: Perbaikan AC, Kalibrasi Timbangan, dll" required>
                                </div>
                                
                                <div class="col-md-12">
                                    <label for="description" class="form-label">Deskripsi/Keterangan</label>
                                    <textarea class="form-control" id="description" name="description" 
                                              rows="3" placeholder="Jelaskan detail pekerjaan yang akan dilakukan..."></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h6 class="border-bottom pb-2">Informasi Vendor & Biaya</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="vendor" class="form-label">Nama Vendor/Petugas</label>
                                    <input type="text" class="form-control" id="vendor" name="vendor" 
                                           placeholder="Nama vendor atau petugas pemeliharaan">
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="vendor_contact" class="form-label">Kontak Vendor</label>
                                    <input type="text" class="form-control" id="vendor_contact" name="vendor_contact" 
                                           placeholder="Nomor telepon atau email">
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="cost" class="form-label">Perkiraan Biaya (Rp)</label>
                                    <input type="number" class="form-control" id="cost" name="cost" 
                                           min="0" step="1000" value="0">
                                </div>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Catatan:</strong> 
                            <ul class="mb-0 mt-2">
                                <li>Status aset akan berubah menjadi "dalam_perbaikan" jika jenis pemeliharaan adalah "perbaikan"</li>
                                <li>Biaya dapat diupdate setelah pemeliharaan selesai</li>
                                <li>Anda dapat mengubah status pemeliharaan nanti melalui halaman detail</li>
                            </ul>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('opd.transactions.index', ['tab' => 'maintenances']) }}" class="btn btn-secondary">Kembali</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-calendar-plus me-1"></i> Jadwalkan Pemeliharaan
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    @if($type == 'mutation' && isset($data['selectedAsset']))
        // Auto-select the asset if coming from asset detail page
        $('#asset_id').val({{ $data['selectedAsset']->asset_id }}).trigger('change');
    @endif
    
    // Populate location dropdown for mutation
    $('#to_opd_unit_id').on('change', function() {
        const opdUnitId = $(this).val();
        if (opdUnitId) {
            // In a real implementation, you would fetch locations for the selected OPD
            // This is a placeholder
            $('#to_location_id').html(`
                <option value="">Pilih Lokasi</option>
                <option value="">Biarkan OPD tujuan menentukan</option>
                <option value="1">Gedung A</option>
                <option value="2">Gedung B</option>
            `);
        }
    });
});
</script>
@endpush
@endsection