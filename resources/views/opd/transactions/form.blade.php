@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-3 mb-4 border-bottom">
        <h1 class="h3">
            @if($type == 'deletion')
                <i class="fas fa-trash-alt me-2"></i> Pengajuan Penghapusan Aset
            @elseif($type == 'mutation')
                <i class="fas fa-exchange-alt me-2"></i> Pengajuan Mutasi Aset
            @else
                <i class="fas fa-tools me-2"></i> Penjadwalan Pemeliharaan
            @endif
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('opd.transactions.index', ['tab' => $type]) }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <form id="transactionForm" method="POST" action="{{ $type == 'deletion' ? route('opd.transactions.deletions.store') : ($type == 'mutation' ? route('opd.transactions.storeMutation') : route('opd.transactions.storeMaintenance')) }}" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <!-- Asset Selection -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-box me-2"></i> Pilih Aset
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="asset_id" class="form-label">Aset *</label>
                            <select class="form-select select2" id="asset_id" name="asset_id" required 
                                    onchange="loadAssetDetails(this.value)">
                                <option value="">-- Pilih Aset --</option>
                                @foreach($data['assets'] as $asset)
                                    <option value="{{ $asset->asset_id }}" 
                                            {{ isset($data['selectedAsset']) && $data['selectedAsset']->asset_id == $asset->asset_id ? 'selected' : '' }}>
                                        {{ $asset->asset_code }} - {{ $asset->name }} 
                                        ({{ $asset->category->name ?? '-' }} | {{ $asset->formatted_value }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="invalid-feedback" id="assetError"></div>
                        </div>
                        
                        <!-- Asset Details -->
                        <div id="assetDetails" style="display: none;">
                            <hr>
                            <h6>Detail Aset</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Kode Aset:</td>
                                            <td id="detailAssetCode">-</td>
                                        </tr>
                                        <tr>
                                            <td>Nama:</td>
                                            <td id="detailAssetName">-</td>
                                        </tr>
                                        <tr>
                                            <td>Kategori:</td>
                                            <td id="detailCategory">-</td>
                                        </tr>
                                        <tr>
                                            <td>Lokasi:</td>
                                            <td id="detailLocation">-</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Nilai:</td>
                                            <td id="detailValue">-</td>
                                        </tr>
                                        <tr>
                                            <td>Status:</td>
                                            <td id="detailStatus">-</td>
                                        </tr>
                                        <tr>
                                            <td>Kondisi:</td>
                                            <td id="detailCondition">-</td>
                                        </tr>
                                        <tr>
                                            <td>Verifikasi:</td>
                                            <td id="detailVerification">-</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Transaction Specific Form -->
                @if($type == 'deletion')
                    <!-- Deletion Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-trash-alt me-2"></i> Alasan Penghapusan
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="deletion_reason" class="form-label">Alasan Penghapusan *</label>
                                    <select class="form-select" id="deletion_reason" name="deletion_reason" required>
                                        @foreach(\App\Models\AssetDeletion::DELETION_REASONS as $reason)
                                            @php
                                                $reasonNames = [
                                                    'rusak_berat' => 'Rusak Berat',
                                                    'hilang' => 'Hilang',
                                                    'jual' => 'Dijual',
                                                    'hibah' => 'Dihibahkan',
                                                    'musnah' => 'Musnah',
                                                    'lainnya' => 'Lainnya'
                                                ];
                                            @endphp
                                            <option value="{{ $reason }}">
                                                {{ $reasonNames[$reason] ?? $reason }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="deletion_method" class="form-label">Metode Penghapusan</label>
                                    <select class="form-select" id="deletion_method" name="deletion_method">
                                        <option value="">-- Pilih Metode --</option>
                                        @foreach(\App\Models\AssetDeletion::DELETION_METHODS as $method)
                                            @php
                                                $methodNames = [
                                                    'jual' => 'Dijual',
                                                    'hibah' => 'Dihibahkan',
                                                    'musnah' => 'Dimusnahkan',
                                                    'scrap' => 'Dibuang/Scrap'
                                                ];
                                            @endphp
                                            <option value="{{ $method }}">
                                                {{ $methodNames[$method] ?? $method }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="reason_details" class="form-label">Detail Alasan *</label>
                                <textarea class="form-control" id="reason_details" name="reason_details" 
                                          rows="4" placeholder="Jelaskan secara detail alasan penghapusan aset..." required></textarea>
                                <small class="text-muted">Minimal 20 karakter</small>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="sale_value" class="form-label">Nilai Penjualan (Jika Dijual)</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" id="sale_value" name="sale_value" min="0" step="0.01">
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="recipient" class="form-label">Penerima (Jika Dihibahkan/Dijual)</label>
                                    <input type="text" class="form-control" id="recipient" name="recipient" placeholder="Nama penerima...">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                @elseif($type == 'mutation')
                    <!-- Mutation Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-exchange-alt me-2"></i> Detail Mutasi
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">OPD Asal</label>
                                    <input type="text" class="form-control" value="{{ $currentOpdUnit->nama_opd }}" readonly>
                                    <input type="hidden" name="from_opd_unit_id" value="{{ $currentOpdUnit->opd_unit_id }}">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="to_opd_unit_id" class="form-label">OPD Tujuan *</label>
                                    <select class="form-select select2" id="to_opd_unit_id" name="to_opd_unit_id" required>
                                        <option value="">-- Pilih OPD Tujuan --</option>
                                        @foreach($data['opdUnits'] as $opdUnit)
                                            <option value="{{ $opdUnit->opd_unit_id }}">
                                                {{ $opdUnit->nama_opd }} ({{ $opdUnit->kode_opd }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Lokasi Asal</label>
                                    <input type="text" class="form-control" id="from_location" value="-" readonly>
                                    <input type="hidden" name="from_location_id" id="from_location_id">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="to_location_id" class="form-label">Lokasi Tujuan</label>
                                    <select class="form-select" id="to_location_id" name="to_location_id">
                                        <option value="">-- Pilih Lokasi Tujuan --</option>
                                        @foreach($data['locations'] as $location)
                                            <option value="{{ $location->location_id }}">
                                                {{ $location->name }} ({{ $location->type }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">Jika dikosongkan, OPD tujuan akan menentukan lokasi</small>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="mutation_date" class="form-label">Tanggal Mutasi *</label>
                                    <input type="date" class="form-control datepicker" id="mutation_date" name="mutation_date" 
                                           value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                @elseif($type == 'maintenance')
                    <!-- Maintenance Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-tools me-2"></i> Detail Pemeliharaan
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="maintenance_type" class="form-label">Jenis Pemeliharaan *</label>
                                    <select class="form-select" id="maintenance_type" name="maintenance_type" required>
                                        @foreach($data['maintenanceTypes'] as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="scheduled_date" class="form-label">Tanggal Terjadwal *</label>
                                    <input type="date" class="form-control datepicker" id="scheduled_date" name="scheduled_date" 
                                           value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="title" class="form-label">Judul Pemeliharaan *</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       placeholder="Contoh: Perbaikan AC, Kalibrasi Alat, dll..." required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description" 
                                          rows="3" placeholder="Deskripsi detail pekerjaan yang akan dilakukan..."></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="cost" class="form-label">Perkiraan Biaya</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Rp</span>
                                        <input type="number" class="form-control" id="cost" name="cost" min="0" step="0.01">
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="vendor" class="form-label">Vendor/Penyedia Jasa</label>
                                    <input type="text" class="form-control" id="vendor" name="vendor" 
                                           placeholder="Nama vendor/jasa...">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="vendor_contact" class="form-label">Kontak Vendor</label>
                                    <input type="text" class="form-control" id="vendor_contact" name="vendor_contact" 
                                           placeholder="Nomor telepon/email...">
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Supporting Documents -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-file-alt me-2"></i> Dokumen Pendukung
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Upload Dokumen</label>
                            <input type="file" class="form-control" id="supporting_documents" name="supporting_documents[]" 
                                   multiple accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Ukuran maksimal 5MB per file. Format: PDF, JPG, PNG, DOC
                            </small>
                        </div>
                        
                        <div id="documentPreview" class="mt-3"></div>
                    </div>
                </div>
                
                <!-- Additional Notes -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-sticky-note me-2"></i> Catatan Tambahan
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" id="notes" name="notes" 
                                      rows="3" placeholder="Catatan tambahan untuk proposal ini..."></textarea>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <!-- Summary Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-clipboard-check me-2"></i> Ringkasan
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h6>Jenis Transaksi:</h6>
                            <div class="alert alert-info">
                                @if($type == 'deletion')
                                    <i class="fas fa-trash-alt me-2"></i> Penghapusan Aset
                                @elseif($type == 'mutation')
                                    <i class="fas fa-exchange-alt me-2"></i> Mutasi Aset
                                @else
                                    <i class="fas fa-tools me-2"></i> Pemeliharaan Aset
                                @endif
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <h6>Status Awal:</h6>
                            <div class="alert alert-warning">
                                <i class="fas fa-clock me-2"></i> Menunggu Persetujuan
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <h6>Diajukan Oleh:</h6>
                            <p class="mb-1">{{ Auth::user()->name }}</p>
                            <small class="text-muted">{{ Auth::user()->opdUnit->nama_opd ?? '-' }}</small>
                        </div>
                        
                        <div class="mb-3">
                            <h6>Tanggal Pengajuan:</h6>
                            <p>{{ date('d/m/Y H:i') }}</p>
                        </div>
                        
                        @if($type == 'deletion')
                            <div class="mb-3">
                                <h6>Proses Penghapusan:</h6>
                                <ol class="small">
                                    <li>Pengajuan oleh Admin OPD</li>
                                    <li>Verifikasi oleh Admin OPD</li>
                                    <li>Persetujuan oleh Admin Utama</li>
                                    <li>Eksekusi Penghapusan</li>
                                </ol>
                            </div>
                        @elseif($type == 'mutation')
                            <div class="mb-3">
                                <h6>Proses Mutasi:</h6>
                                <ol class="small">
                                    <li>Pengajuan oleh OPD Asal</li>
                                    <li>Persetujuan oleh Admin Utama</li>
                                    <li>Konfirmasi oleh OPD Tujuan</li>
                                    <li>Transfer Kepemilikan</li>
                                </ol>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Requirements Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-clipboard-list me-2"></i> Persyaratan
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Aset dalam status aktif</span>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Dokumen aset sudah valid</span>
                            </li>
                            <li class="list-group-item d-flex align-items-center">
                                <i class="fas fa-check-circle text-success me-2"></i>
                                <span>Tidak ada transaksi tertunda</span>
                            </li>
                            @if($type == 'deletion')
                                <li class="list-group-item d-flex align-items-center">
                                    <i class="fas fa-info-circle text-info me-2"></i>
                                    <span>Lampirkan dokumen pendukung</span>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
                
                <!-- Action Card -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                @if($type == 'deletion')
                                    <i class="fas fa-paper-plane me-2"></i> Ajukan Penghapusan
                                @elseif($type == 'mutation')
                                    <i class="fas fa-paper-plane me-2"></i> Ajukan Mutasi
                                @else
                                    <i class="fas fa-calendar-check me-2"></i> Jadwalkan Pemeliharaan
                                @endif
                            </button>
                            
                            <button type="button" class="btn btn-outline-secondary" 
                                    onclick="window.location.href='{{ route('opd.transactions.index', ['tab' => $type]) }}'">
                                <i class="fas fa-times me-2"></i> Batal
                            </button>
                            
                            <hr>
                            
                            <div class="text-center">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Proposal akan diverifikasi oleh sistem sebelum diproses
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    @push('styles')
    <style>
        .document-preview {
            max-height: 300px;
            overflow-y: auto;
        }
        
        .document-item {
            padding: 8px;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            margin-bottom: 5px;
            background-color: #f8f9fa;
        }
        
        .requirement-list .list-group-item {
            border: none;
            padding: 8px 0;
        }
        
        .asset-details-table td {
            padding: 4px 8px;
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });
            
            // Initialize datepicker
            $('.datepicker').flatpickr({
                dateFormat: 'Y-m-d',
                locale: 'id',
                minDate: 'today'
            });
            
            // Load asset details if pre-selected
            @if(isset($data['selectedAsset']))
                loadAssetDetails({{ $data['selectedAsset']->asset_id }});
            @endif
            
            // Handle document preview
            $('#supporting_documents').on('change', function() {
                previewDocuments(this);
            });
            
            // Form submission
            $('#transactionForm').on('submit', function(e) {
                e.preventDefault();
                submitTransactionForm();
            });
        });
        
        // Load asset details
        function loadAssetDetails(assetId) {
            if (!assetId) {
                $('#assetDetails').hide();
                return;
            }
            
            $.ajax({
                url: '{{ url("opd/assets") }}/' + assetId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        const asset = response.asset;
                        
                        // Update details
                        $('#detailAssetCode').text(asset.asset_code);
                        $('#detailAssetName').text(asset.name);
                        $('#detailCategory').text(asset.category?.name + ' (' + asset.category?.kib_code + ')');
                        $('#detailLocation').text(asset.location?.name || 'Belum ditetapkan');
                        $('#detailValue').text(asset.formatted_value);
                        $('#detailStatus').html('<span class="badge status-badge badge-' + asset.status.replace('_', '-') + '">' + 
                            (asset.status === 'aktif' ? 'Aktif' : 
                             asset.status === 'dimutasi' ? 'Mutasi' : 
                             asset.status === 'dihapus' ? 'Terhapus' : 
                             asset.status === 'dalam_perbaikan' ? 'Perbaikan' : 
                             asset.status === 'nonaktif' ? 'Nonaktif' : asset.status) + '</span>');
                        $('#detailCondition').html('<span class="badge bg-' + 
                            (asset.condition === 'Baik' ? 'success' : 
                             asset.condition === 'Rusak Ringan' ? 'warning' : 'danger') + '">' + asset.condition + '</span>');
                        $('#detailVerification').html(
                            '<span class="badge badge-' + asset.document_verification_status.replace('_', '-') + '">' + 
                            (asset.document_verification_status === 'belum_diverifikasi' ? 'Belum' : 
                             asset.document_verification_status === 'valid' ? 'Valid' : 'Tidak Valid') + '</span> ' +
                            '<span class="badge badge-' + asset.validation_status.replace('_', '-') + '">' + 
                            (asset.validation_status === 'belum_divalidasi' ? 'Belum' : 
                             asset.validation_status === 'disetujui' ? 'Disetujui' : 
                             asset.validation_status === 'revisi' ? 'Revisi' : 'Ditolak') + '</span>'
                        );
                        
                        // Update mutation form fields
                        if (asset.location) {
                            $('#from_location').val(asset.location.name);
                            $('#from_location_id').val(asset.location.location_id);
                        }
                        
                        // Show details
                        $('#assetDetails').show();
                        
                        // Validate asset for transaction
                        validateAssetForTransaction(assetId);
                    }
                },
                error: handleAjaxError
            });
        }
        
        // Validate asset for transaction
        function validateAssetForTransaction(assetId) {
            const transactionType = '{{ $type }}';
            
            $.ajax({
                url: '{{ url("opd/transactions/validate-asset") }}/' + assetId,
                method: 'GET',
                data: { type: transactionType },
                success: function(response) {
                    if (!response.valid) {
                        showToast(response.message, 'warning');
                        
                        // Disable form if not valid
                        if (response.severity === 'error') {
                            $('#transactionForm button[type="submit"]').prop('disabled', true);
                        }
                    }
                }
            });
        }
        
        // Preview documents
        function previewDocuments(input) {
            const preview = $('#documentPreview');
            preview.empty();
            
            if (input.files.length === 0) {
                return;
            }
            
            const fileList = $('<div class="document-preview"></div>');
            
            for (let i = 0; i < input.files.length; i++) {
                const file = input.files[i];
                const fileItem = $(`
                    <div class="document-item d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-file-${file.type.includes('image') ? 'image' : 'pdf'} me-2"></i>
                            <span>${file.name}</span>
                            <small class="text-muted d-block">${formatFileSize(file.size)}</small>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeDocument(${i})">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `);
                fileList.append(fileItem);
            }
            
            preview.append(fileList);
        }
        
        // Remove document from preview
        function removeDocument(index) {
            const input = $('#supporting_documents')[0];
            const dt = new DataTransfer();
            
            for (let i = 0; i < input.files.length; i++) {
                if (i !== index) {
                    dt.items.add(input.files[i]);
                }
            }
            
            input.files = dt.files;
            previewDocuments(input);
        }
        
        // Format file size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        // Submit transaction form
        function submitTransactionForm() {
            const form = $('#transactionForm');
            const button = form.find('button[type="submit"]');
            const originalText = button.html();
            
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Mengirim...');
            
            // Create FormData for file uploads
            const formData = new FormData(form[0]);
            
            $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success || !response.errors) {
                        showToast(
                            '{{ $type == "deletion" ? "Proposal penghapusan berhasil diajukan" : ($type == "mutation" ? "Proposal mutasi berhasil diajukan" : "Pemeliharaan berhasil dijadwalkan") }}', 
                            'success'
                        );
                        
                        setTimeout(() => {
                            window.location.href = '{{ route("opd.transactions.index", ["tab" => $type]) }}';
                        }, 1500);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(field => {
                            const errorDiv = $('#' + field + 'Error');
                            const input = $('#' + field);
                            
                            input.addClass('is-invalid');
                            if (errorDiv.length) {
                                errorDiv.text(errors[field][0]);
                            }
                        });
                        showToast('Validasi gagal. Periksa kembali data yang dimasukkan.', 'danger');
                    } else {
                        handleAjaxError(xhr);
                    }
                },
                complete: function() {
                    button.prop('disabled', false).html(originalText);
                }
            });
        }
    </script>
    @endpush
@endsection