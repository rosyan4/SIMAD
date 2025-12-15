@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">{{ $title }}</h4>
                </div>
                <div class="card-body">
                    <form id="assetForm" action="{{ $asset ? route('opd.assets.update', $asset) : route('opd.assets.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @if($asset)
                            @method('PUT')
                        @endif
                        
                        <!-- Hidden fields for auto-set values -->
                        <input type="hidden" name="status" value="aktif">
                        <input type="hidden" name="document_verification_status" value="belum_diverifikasi">
                        <input type="hidden" name="validation_status" value="belum_divalidasi">

                        <div class="row">
                            <!-- Kolom Kiri -->
                            <div class="col-md-6">
                                <!-- Informasi Dasar Aset -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Informasi Dasar Aset</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="name" class="form-label required">Nama Aset</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" 
                                                   value="{{ old('name', $asset->name ?? '') }}" 
                                                   required maxlength="255">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="category_id" class="form-label required">Kategori</label>
                                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                                        id="category_id" name="category_id" required>
                                                    <option value="">Pilih Kategori</option>
                                                    @foreach($categories as $category)
                                                        <option value="{{ $category->category_id }}" 
                                                                {{ old('category_id', $asset->category_id ?? '') == $category->category_id ? 'selected' : '' }}
                                                                data-kib-code="{{ $category->kib_code }}">
                                                            {{ $category->kib_code }} - {{ $category->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('category_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="sub_category_code" class="form-label required">Sub Kategori</label>
                                                <select class="form-select @error('sub_category_code') is-invalid @enderror" 
                                                        id="sub_category_code" name="sub_category_code" required>
                                                    <option value="">Pilih Sub Kategori</option>
                                                    <!-- Options akan diisi via JavaScript -->
                                                </select>
                                                @error('sub_category_code')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="location_id" class="form-label">Lokasi</label>
                                                <select class="form-select @error('location_id') is-invalid @enderror" 
                                                        id="location_id" name="location_id">
                                                    <option value="">Pilih Lokasi</option>
                                                    @foreach($locations as $location)
                                                        <option value="{{ $location->location_id }}" 
                                                                {{ old('location_id', $asset->location_id ?? '') == $location->location_id ? 'selected' : '' }}>
                                                            {{ $location->name }} ({{ $location->type }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('location_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="condition" class="form-label required">Kondisi</label>
                                                <select class="form-select @error('condition') is-invalid @enderror" 
                                                        id="condition" name="condition" required>
                                                    <option value="">Pilih Kondisi</option>
                                                    @foreach(['Baik', 'Rusak Ringan', 'Rusak Berat'] as $condition)
                                                        <option value="{{ $condition }}" 
                                                                {{ old('condition', $asset->condition ?? '') == $condition ? 'selected' : '' }}>
                                                            {{ $condition }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('condition')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informasi Nilai dan Tahun -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Nilai dan Tahun</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label for="value" class="form-label required">Nilai Aset (Rp)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">Rp</span>
                                                    <input type="number" class="form-control @error('value') is-invalid @enderror" 
                                                           id="value" name="value" 
                                                           value="{{ old('value', $asset->value ?? '') }}" 
                                                           min="0" step="0.01" required>
                                                </div>
                                                @error('value')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="acquisition_year" class="form-label required">Tahun Perolehan</label>
                                                <input type="number" class="form-control @error('acquisition_year') is-invalid @enderror" 
                                                       id="acquisition_year" name="acquisition_year" 
                                                       value="{{ old('acquisition_year', $asset->acquisition_year ?? date('Y')) }}" 
                                                       min="1900" max="{{ date('Y') }}" required>
                                                @error('acquisition_year')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom Kanan -->
                            <div class="col-md-6">
                                <!-- Kode Aset -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Kode Aset</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="asset_code" class="form-label">Kode Aset</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control @error('asset_code') is-invalid @enderror" 
                                                       id="asset_code" name="asset_code" 
                                                       value="{{ old('asset_code', $asset->asset_code ?? '') }}" 
                                                       placeholder="Kosongkan untuk generate otomatis">
                                                <button type="button" class="btn btn-outline-secondary" id="previewCodeBtn">
                                                    <i class="fas fa-sync-alt"></i> Preview
                                                </button>
                                            </div>
                                            <small class="text-muted">Kosongkan untuk generate otomatis</small>
                                            @error('asset_code')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="alert alert-info" id="codePreview">
                                            <small class="d-block mb-1"><strong>Format Kode:</strong> KIB-SUBKATEGORI-TAHUN-KODEDINAS-SEQ</small>
                                            <small class="d-block"><strong>Contoh:</strong> B-01-2025-05-001</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Data KIB -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Data KIB <span id="kibCodeDisplay"></span></h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="kibFields">
                                            <!-- Fields akan diisi berdasarkan KIB code via JavaScript -->
                                            <p class="text-muted">Pilih kategori untuk menampilkan data KIB</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Dokumen -->
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Dokumen Pendukung</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label for="documents" class="form-label">Unggah Dokumen</label>
                                            <input type="file" class="form-control @error('documents') is-invalid @enderror" 
                                                   id="documents" name="documents[]" multiple>
                                            <small class="text-muted">Format: PDF, JPG, PNG, DOC, XLS (Max: 5MB per file)</small>
                                            @error('documents')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="document_type" class="form-label">Jenis Dokumen</label>
                                            <select class="form-select @error('document_type') is-invalid @enderror" 
                                                    id="document_type" name="document_type">
                                                <option value="pengadaan" {{ old('document_type', 'pengadaan') == 'pengadaan' ? 'selected' : '' }}>Pengadaan</option>
                                                <option value="lainnya" {{ old('document_type') == 'lainnya' ? 'selected' : '' }}>Lainnya</option>
                                            </select>
                                            @error('document_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('opd.assets.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Kembali
                                    </a>
                                    <div>
                                        <button type="reset" class="btn btn-warning me-2">
                                            <i class="fas fa-redo"></i> Reset
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save"></i> {{ $asset ? 'Update' : 'Simpan' }} Aset
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .required::after {
        content: " *";
        color: #dc3545;
    }
    .card-header.bg-light {
        background-color: #f8f9fa !important;
        border-bottom: 1px solid #dee2e6;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    // Data sub kategori statis
    const subCategories = {
        'A': { '01': 'Tanah Perkantoran', '02': 'Tanah Fasilitas Umum', '03': 'Tanah Lainnya' },
        'B': { '01': 'Alat Berat', '02': 'Alat Elektronik', '03': 'Kendaraan Dinas', '04': 'Peralatan Medis', 
                '05': 'Peralatan Olahraga', '06': 'Furniture', '07': 'Alat Laboratorium' },
        'C': { '01': 'Gedung Kantor', '02': 'Gedung Sekolah', '03': 'Rumah Sakit', '04': 'Gedung Olahraga (GOR)', '05': 'Stadion' },
        'D': { '01': 'Jalan Kota', '02': 'Jembatan', '03': 'Jaringan Irigasi', '04': 'Jaringan Internet' },
        'E': { '01': 'Koleksi Perpustakaan', '02': 'Aset Lainnya' },
        'F': { '01': 'Konstruksi Gedung', '02': 'Konstruksi Jalan', '03': 'Konstruksi Lainnya' }
    };

    // Data KIB fields
    const kibFields = {
        'A': [
            { name: 'luas', label: 'Luas (m²)', type: 'number', required: true },
            { name: 'lokasi_tanah', label: 'Lokasi Tanah', type: 'text', required: true },
            { name: 'status_hak', label: 'Status Hak', type: 'text', required: true },
            { name: 'sertifikat_tanggal', label: 'Tanggal Sertifikat', type: 'date', required: true },
            { name: 'sertifikat_nomor', label: 'Nomor Sertifikat', type: 'text', required: true }
        ],
        'B': [
            { name: 'merk', label: 'Merk', type: 'text', required: true },
            { name: 'tipe', label: 'Tipe', type: 'text', required: true },
            { name: 'spesifikasi', label: 'Spesifikasi', type: 'text', required: true },
            { name: 'bahan', label: 'Bahan', type: 'text', required: true },
            { name: 'tahun_pembuatan', label: 'Tahun Pembuatan', type: 'number', required: true }
        ],
        'C': [
            { name: 'luas_bangunan', label: 'Luas Bangunan (m²)', type: 'number', required: true },
            { name: 'jumlah_lantai', label: 'Jumlah Lantai', type: 'number', required: true },
            { name: 'konstruksi', label: 'Konstruksi', type: 'text', required: true },
            { name: 'alamat_lengkap', label: 'Alamat Lengkap', type: 'text', required: true }
        ],
        'D': [
            { name: 'panjang', label: 'Panjang (m)', type: 'number', required: true },
            { name: 'lebar', label: 'Lebar (m)', type: 'number', required: true },
            { name: 'konstruksi', label: 'Konstruksi', type: 'text', required: true },
            { name: 'kondisi', label: 'Kondisi', type: 'text', required: true }
        ],
        'E': [
            { name: 'jenis', label: 'Jenis', type: 'text', required: true },
            { name: 'spesifikasi', label: 'Spesifikasi', type: 'text', required: true },
            { name: 'kondisi', label: 'Kondisi', type: 'text', required: true }
        ],
        'F': [
            { name: 'nama_kontraktor', label: 'Nama Kontraktor', type: 'text', required: true },
            { name: 'nilai_kontrak', label: 'Nilai Kontrak', type: 'number', required: true },
            { name: 'tanggal_mulai', label: 'Tanggal Mulai', type: 'date', required: true },
            { name: 'tanggal_selesai', label: 'Tanggal Selesai', type: 'date', required: true }
        ]
    };

    const kibDisplayNames = {
        'A': 'KIB A - Tanah', 'B': 'KIB B - Peralatan dan Mesin', 'C': 'KIB C - Gedung dan Bangunan',
        'D': 'KIB D - Jalan, Irigasi, dan Jaringan', 'E': 'KIB E - Aset Tetap Lainnya', 'F': 'KIB F - Konstruksi Dalam Pengerjaan'
    };

    // Fungsi untuk update sub kategori
    function updateSubCategories() {
        const categorySelect = $('#category_id');
        const subCategorySelect = $('#sub_category_code');
        const selectedOption = categorySelect.find('option:selected');
        const kibCode = selectedOption.data('kib-code');
        
        subCategorySelect.empty().append('<option value="">Pilih Sub Kategori</option>');
        
        if (kibCode && subCategories[kibCode]) {
            $.each(subCategories[kibCode], function(code, name) {
                subCategorySelect.append(new Option(name, code));
            });
            
            // Set nilai lama jika ada
            const oldValue = "{{ old('sub_category_code', $asset->sub_category_code ?? '') }}";
            if (oldValue) {
                subCategorySelect.val(oldValue);
            }
        }
        
        updateKIBFields(kibCode);
        updateCodePreview();
    }

    // Fungsi untuk update KIB fields
    function updateKIBFields(kibCode) {
        const kibFieldsContainer = $('#kibFields');
        const kibCodeDisplay = $('#kibCodeDisplay');
        
        if (kibCode && kibDisplayNames[kibCode]) {
            kibCodeDisplay.text(`- ${kibDisplayNames[kibCode]}`);
            
            if (kibFields[kibCode]) {
                let html = '<div class="row">';
                kibFields[kibCode].forEach(field => {
                    const fieldId = `kib_${field.name}`;
                    let oldValue = '';
                    
                    if (oldKibData && oldKibData[field.name] !== undefined) {
                        oldValue = oldKibData[field.name];
                    } else if (kibData && kibData[field.name] !== undefined) {
                        oldValue = kibData[field.name];
                    }
                    
                    html += `
                        <div class="col-md-6 mb-3">
                            <label for="${fieldId}" class="form-label ${field.required ? 'required' : ''}">${field.label}</label>
                            <input type="${field.type}" 
                                   class="form-control" 
                                   id="${fieldId}" 
                                   name="kib_data[${field.name}]" 
                                   value="${oldValue}"
                                   ${field.required ? 'required' : ''}
                                   ${field.type === 'number' ? 'min="0"' : ''}>
                        </div>
                    `;
                });
                html += '</div>';
                kibFieldsContainer.html(html);
            } else {
                kibFieldsContainer.html('<p class="text-muted">Tidak ada data KIB khusus untuk kategori ini</p>');
            }
        } else {
            kibCodeDisplay.text('');
            kibFieldsContainer.html('<p class="text-muted">Pilih kategori untuk menampilkan data KIB</p>');
        }
    }

    // Fungsi untuk update preview kode
    function updateCodePreview() {
        const categorySelect = $('#category_id');
        const subCategorySelect = $('#sub_category_code');
        const acquisitionYear = $('#acquisition_year').val();
        const assetCodeField = $('#asset_code');
        
        const selectedCategory = categorySelect.find('option:selected');
        const kibCode = selectedCategory.data('kib-code');
        const subCategoryCode = subCategorySelect.val();
        
        if (kibCode && subCategoryCode && acquisitionYear) {
            // AJAX request untuk preview kode
            $.ajax({
                url: "{{ route('opd.assets.previewCode') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    kib_code: kibCode,
                    sub_category_code: subCategoryCode,
                    acquisition_year: acquisitionYear
                },
                success: function(response) {
                    if (response.success && response.asset_code) {
                        if (!assetCodeField.val()) {
                            assetCodeField.val(response.asset_code);
                        }
                        $('#codePreview').removeClass('alert-info alert-danger')
                            .addClass('alert-success')
                            .html(`<strong>Kode Preview:</strong> ${response.asset_code}`);
                    }
                },
                error: function(xhr) {
                    console.error('Error previewing code:', xhr.responseJSON);
                    $('#codePreview').removeClass('alert-info alert-success')
                        .addClass('alert-danger')
                        .html(`<strong>Error:</strong> ${xhr.responseJSON?.message || 'Gagal generate kode'}`);
                }
            });
        }
    }

    // Event Listeners
    $('#category_id').change(function() {
        updateSubCategories();
    });

    $('#sub_category_code, #acquisition_year').change(function() {
        updateCodePreview();
    });

    $('#previewCodeBtn').click(function() {
        updateCodePreview();
    });

    // Format mata uang input
    $('#value').on('blur', function() {
        const value = parseFloat($(this).val());
        if (!isNaN(value)) {
            $(this).val(value.toFixed(2));
        }
    });

    // Test save button
    $('#testSaveBtn').click(function() {
        const formData = new FormData($('#assetForm')[0]);
        
        // Remove files for test
        formData.delete('documents[]');
        
        // Add test flag
        formData.append('test_mode', 'true');
        
        $.ajax({
            url: "{{ route('opd.assets.store') }}",
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                alert('Test Success! Check console for details.');
                console.log('Test Response:', response);
            },
            error: function(xhr) {
                alert('Test Failed! Check console for error.');
                console.error('Test Error:', xhr.responseJSON || xhr.responseText);
            }
        });
    });

    // Inisialisasi saat halaman dimuat
    @if(isset($asset) && $asset->category_id)
        setTimeout(() => {
            $('#category_id').trigger('change');
        }, 100);
    @elseif(old('category_id'))
        setTimeout(() => {
            $('#category_id').trigger('change');
        }, 100);
    @endif

    // Form validation
    $('#assetForm').submit(function(e) {
        // Clear previous error states
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        
        let isValid = true;
        
        // Validasi required fields
        $('[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                $(this).after(`<div class="invalid-feedback">Field ini wajib diisi</div>`);
                isValid = false;
                
                if (isValid === false) {
                    $(this).focus();
                }
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Harap lengkapi semua field yang wajib diisi!');
        } else {
            // Show loading
            $(this).find('button[type="submit"]').html('<i class="fas fa-spinner fa-spin"></i> Menyimpan...').prop('disabled', true);
        }
    });
});
</script>
@endpush