@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
        <div>
            <a href="{{ route('opd.assets.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        {{ isset($asset) ? 'Edit Data Aset' : 'Tambah Data Aset Baru' }}
                        @if(isset($asset) && ($asset->document_verification_status == 'valid' || $asset->validation_status == 'disetujui'))
                            <span class="badge bg-warning ms-2">Read Only (sudah diverifikasi/divalidasi)</span>
                        @endif
                    </h6>
                </div>
                <div class="card-body">
                    <form id="assetForm" method="POST" 
                          action="{{ isset($asset) ? route('opd.assets.update', $asset) : route('opd.assets.store') }}"
                          enctype="multipart/form-data">
                        @csrf
                        @if(isset($asset))
                            @method('PUT')
                        @endif
                        
                        <!-- Validation Errors -->
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        
                        <!-- Informasi Dasar -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3"><i class="fas fa-info-circle"></i> Informasi Dasar</h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label required">Nama Aset</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name', $asset->name ?? '') }}" required
                                       placeholder="Contoh: Mobil Dinas Toyota Avanza"
                                       {{ isset($asset) && ($asset->document_verification_status == 'valid' || $asset->validation_status == 'disetujui') ? 'readonly' : '' }}>
                                <div class="form-text">Nama lengkap aset sesuai dengan faktur/dokumen</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="category_id" class="form-label required">Kategori Aset</label>
                                <select class="form-control" id="category_id" name="category_id" required
                                        {{ isset($asset) && ($asset->document_verification_status == 'valid' || $asset->validation_status == 'disetujui') ? 'disabled' : '' }}>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}" 
                                                {{ old('category_id', $asset->category_id ?? '') == $category->category_id ? 'selected' : '' }}
                                                data-kib-code="{{ $category->kib_code }}"
                                                data-sub-categories="{{ json_encode($category->sub_categories ?? []) }}">
                                            {{ $category->kib_code }} - {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @if(isset($asset) && ($asset->document_verification_status == 'valid' || $asset->validation_status == 'disetujui'))
                                    <input type="hidden" name="category_id" value="{{ $asset->category_id }}">
                                @endif
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="sub_category_code" class="form-label required">Sub Kategori</label>
                                <select class="form-control" id="sub_category_code" name="sub_category_code" required
                                        {{ isset($asset) && ($asset->document_verification_status == 'valid' || $asset->validation_status == 'disetujui') ? 'disabled' : '' }}>
                                    <option value="">Pilih Sub Kategori</option>
                                    <!-- Options will be populated by JavaScript -->
                                    @if(isset($asset) && $asset->category)
                                        @php
                                            $subCategories = $asset->category->sub_categories ?? [];
                                        @endphp
                                        @if(!empty($subCategories))
                                            @foreach($subCategories as $code => $name)
                                                <option value="{{ $code }}" 
                                                    {{ old('sub_category_code', $asset->sub_category_code ?? '') == $code ? 'selected' : '' }}>
                                                    {{ $code }} - {{ $name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    @endif
                                </select>
                                @if(isset($asset) && ($asset->document_verification_status == 'valid' || $asset->validation_status == 'disetujui'))
                                    <input type="hidden" name="sub_category_code" value="{{ $asset->sub_category_code }}">
                                @endif
                                <div class="form-text">Pilih kategori terlebih dahulu</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="asset_code" class="form-label">Kode Aset</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="asset_code" name="asset_code" 
                                           value="{{ old('asset_code', $asset->asset_code ?? '') }}"
                                           placeholder="Akan digenerate otomatis jika kosong"
                                           {{ isset($asset) && ($asset->document_verification_status == 'valid' || $asset->validation_status == 'disetujui') ? 'readonly' : '' }}>
                                    @if(!isset($asset) || ($asset->document_verification_status != 'valid' && $asset->validation_status != 'disetujui'))
                                        <button type="button" class="btn btn-outline-secondary" id="previewCodeBtn">
                                            <i class="fas fa-eye"></i> Preview
                                        </button>
                                    @endif
                                </div>
                                <div class="form-text">
                                    Format: KIB-SUBKATEGORI-TAHUN-KODEDINAS-SEQ (Contoh: B-01-2025-05-001)
                                </div>
                                <div id="codePreview" class="mt-2"></div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="location_id" class="form-label">Lokasi</label>
                                <select class="form-control" id="location_id" name="location_id"
                                        {{ isset($asset) && ($asset->document_verification_status == 'valid' || $asset->validation_status == 'disetujui') ? 'disabled' : '' }}>
                                    <option value="">Pilih Lokasi</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->location_id }}"
                                                {{ old('location_id', $asset->location_id ?? '') == $location->location_id ? 'selected' : '' }}>
                                            {{ $location->name }} ({{ $location->type }})
                                        </option>
                                    @endforeach
                                </select>
                                @if(isset($asset) && ($asset->document_verification_status == 'valid' || $asset->validation_status == 'disetujui'))
                                    <input type="hidden" name="location_id" value="{{ $asset->location_id }}">
                                @endif
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="acquisition_year" class="form-label required">Tahun Perolehan</label>
                                <input type="number" class="form-control" id="acquisition_year" name="acquisition_year" 
                                       value="{{ old('acquisition_year', $asset->acquisition_year ?? date('Y')) }}" 
                                       min="1900" max="{{ date('Y') }}" required
                                       {{ isset($asset) && ($asset->document_verification_status == 'valid' || $asset->validation_status == 'disetujui') ? 'readonly' : '' }}>
                            </div>
                        </div>
                        
                        <!-- Nilai dan Kondisi -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3"><i class="fas fa-money-bill-wave"></i> Nilai dan Kondisi</h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="value" class="form-label required">Nilai Aset (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control" id="value" name="value" 
                                           value="{{ old('value', $asset->value ?? '') }}" 
                                           min="0" step="0.01" required
                                           placeholder="Contoh: 250000000"
                                           {{ isset($asset) && ($asset->document_verification_status == 'valid' || $asset->validation_status == 'disetujui') ? 'readonly' : '' }}>
                                </div>
                                <div class="form-text">Nilai perolehan aset sesuai dengan faktur pembelian</div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="condition" class="form-label required">Kondisi</label>
                                <select class="form-control" id="condition" name="condition" required
                                        {{ isset($asset) && ($asset->document_verification_status == 'valid' || $asset->validation_status == 'disetujui') ? 'disabled' : '' }}>
                                    <option value="Baik" {{ old('condition', $asset->condition ?? '') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="Rusak Ringan" {{ old('condition', $asset->condition ?? '') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                    <option value="Rusak Berat" {{ old('condition', $asset->condition ?? '') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                                </select>
                                @if(isset($asset) && ($asset->document_verification_status == 'valid' || $asset->validation_status == 'disetujui'))
                                    <input type="hidden" name="condition" value="{{ $asset->condition }}">
                                @endif
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label required">Status Aset</label>
                                <select class="form-control" id="status" name="status" required
                                        {{ isset($asset) && ($asset->document_verification_status == 'valid' || $asset->validation_status == 'disetujui') ? 'disabled' : '' }}>
                                    <option value="aktif" {{ old('status', $asset->status ?? '') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="nonaktif" {{ old('status', $asset->status ?? '') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                    <option value="dalam_perbaikan" {{ old('status', $asset->status ?? '') == 'dalam_perbaikan' ? 'selected' : '' }}>Dalam Perbaikan</option>
                                </select>
                                @if(isset($asset) && ($asset->document_verification_status == 'valid' || $asset->validation_status == 'disetujui'))
                                    <input type="hidden" name="status" value="{{ $asset->status }}">
                                @endif
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Administratif</label>
                                <div class="form-control-plaintext">
                                    <span class="badge bg-{{ isset($asset) && $asset->document_verification_status == 'valid' ? 'success' : 'secondary' }}">
                                        {{ isset($asset) ? $asset->document_verification_status : 'Belum Diverifikasi' }}
                                    </span>
                                    <span class="badge bg-{{ isset($asset) && $asset->validation_status == 'disetujui' ? 'success' : 'secondary' }} ms-2">
                                        {{ isset($asset) ? $asset->validation_status : 'Belum Divalidasi' }}
                                    </span>
                                </div>
                                <div class="form-text">Status akan diperbarui setelah proses verifikasi oleh admin</div>
                            </div>
                        </div>
                        
                        <!-- Data KIB (conditional based on category) -->
                        <div class="row mb-4" id="kibDataSection" style="display: none;">
                            <div class="col-12">
                                <h5 class="text-primary mb-3"><i class="fas fa-file-alt"></i> Data KIB Spesifik</h5>
                                <div id="kibFormFields">
                                    <!-- Form fields will be dynamically generated based on KIB code -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- Existing KIB Data (for edit mode) -->
                        @if(isset($asset) && $asset->kib_data && count($asset->kib_data) > 0)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h5 class="text-primary mb-3"><i class="fas fa-file-alt"></i> Data KIB {{ $asset->category->kib_code }} (Existing)</h5>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <tbody>
                                                        @foreach($asset->kib_data as $key => $value)
                                                            <tr>
                                                                <th width="30%">{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                                                                <td>{{ $value }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="alert alert-info mt-3">
                                                <i class="fas fa-info-circle"></i> Data KIB sudah tersimpan. Untuk mengubah, hubungi admin utama.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Dokumen Pendukung -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3"><i class="fas fa-file-upload"></i> Dokumen Pendukung</h5>
                            </div>
                            
                            @if(!isset($asset) || ($asset->document_verification_status != 'valid' && $asset->validation_status != 'disetujui'))
                                <div class="col-md-6 mb-3">
                                    <label for="document_type" class="form-label">Jenis Dokumen</label>
                                    <select class="form-control" id="document_type" name="document_type">
                                        <option value="pengadaan">Dokumen Pengadaan</option>
                                        <option value="mutasi">Dokumen Mutasi</option>
                                        <option value="penghapusan">Dokumen Penghapusan</option>
                                        <option value="pemeliharaan">Dokumen Pemeliharaan</option>
                                        <option value="lainnya">Dokumen Lainnya</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="documents" class="form-label">Upload Dokumen</label>
                                    <input type="file" class="form-control" id="documents" name="documents[]" multiple 
                                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx">
                                    <div class="form-text">
                                        Maksimal 5MB per file. Format: PDF, JPG, PNG, DOC, XLS
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Preview uploaded documents for edit -->
                            @if(isset($asset) && $asset->documents->count() > 0)
                                <div class="col-12 mb-3">
                                    <label class="form-label">Dokumen Terupload</label>
                                    <div class="row">
                                        @foreach($asset->documents as $document)
                                            <div class="col-md-3 mb-2">
                                                <div class="card border">
                                                    <div class="card-body p-2">
                                                        <div class="d-flex align-items-center">
                                                            <i class="fas fa-file-{{ in_array($document->file_type, ['pdf']) ? 'pdf text-danger' : (in_array($document->file_type, ['jpg','jpeg','png']) ? 'image text-success' : 'word text-primary') }} me-2"></i>
                                                            <small class="text-truncate">{{ basename($document->file_path) }}</small>
                                                        </div>
                                                        <div class="mt-2">
                                                            <a href="{{ Storage::url($document->file_path) }}" target="_blank" class="btn btn-sm btn-info">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ Storage::url($document->file_path) }}" download class="btn btn-sm btn-success">
                                                                <i class="fas fa-download"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Hidden fields -->
                        <input type="hidden" id="opd_unit_id" name="opd_unit_id" value="{{ $opdUnit->opd_unit_id ?? auth()->user()->opd_unit_id }}">
                        <input type="hidden" id="created_by" name="created_by" value="{{ auth()->id() }}">
                        
                        @if(!isset($asset))
                            <input type="hidden" id="document_verification_status" name="document_verification_status" value="belum_diverifikasi">
                            <input type="hidden" id="validation_status" name="validation_status" value="belum_divalidasi">
                        @endif
                        
                        @if(isset($asset) && $asset->kib_data)
                            @foreach($asset->kib_data as $key => $value)
                                <input type="hidden" name="kib_data[{{ $key }}]" value="{{ $value }}">
                            @endforeach
                        @endif
                        
                        <!-- Submit Buttons -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('opd.assets.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Batal
                                    </a>
                                    <div>
                                        @if(!isset($asset) || ($asset->document_verification_status != 'valid' && $asset->validation_status != 'disetujui'))
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-save"></i> {{ isset($asset) ? 'Perbarui Data' : 'Simpan Aset' }}
                                            </button>
                                        @endif
                                        
                                        @if(isset($asset))
                                            <a href="{{ route('opd.assets.show', $asset) }}" class="btn btn-info ms-2">
                                                <i class="fas fa-eye"></i> Lihat Detail
                                            </a>
                                        @endif
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
    .required:after {
        content: " *";
        color: red;
    }
    
    .form-text {
        font-size: 0.85rem;
        color: #6c757d;
    }
    
    #codePreview {
        padding: 10px;
        background-color: #f8f9fa;
        border-radius: 4px;
        border-left: 4px solid #007bff;
    }
    
    .kib-field {
        margin-bottom: 15px;
    }
    
    .readonly-field {
        background-color: #e9ecef;
        cursor: not-allowed;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialize variables
        let currentKibCode = '';
        
        // Load sub categories for initial category if editing
        @if(isset($asset) && $asset->category)
            currentKibCode = '{{ $asset->category->kib_code }}';
            loadSubCategories({{ $asset->category_id }}, '{{ $asset->sub_category_code }}');
            loadKIBForm('{{ $asset->category->kib_code }}');
        @endif
        
        // Category change event
        $('#category_id').change(function() {
            const selectedOption = $(this).find('option:selected');
            const categoryId = $(this).val();
            const kibCode = selectedOption.data('kib-code');
            const subCategories = selectedOption.data('sub-categories') || {};
            
            if (categoryId) {
                loadSubCategoriesFromData(subCategories);
                loadKIBForm(kibCode);
            } else {
                $('#sub_category_code').html('<option value="">Pilih Sub Kategori</option>');
                $('#kibDataSection').hide();
            }
        });
        
        // Preview asset code
        $('#previewCodeBtn').click(function() {
            previewAssetCode();
        });
        
        // Auto-preview when relevant fields change
        $('#category_id, #sub_category_code, #acquisition_year').change(function() {
            previewAssetCode();
        });
        
        // Form submission validation
        $('#assetForm').submit(function(e) {
            // Additional validation can be added here
            if (!validateForm()) {
                e.preventDefault();
                return false;
            }
            
            // Show loading
            Swal.fire({
                title: 'Menyimpan data...',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
        });
        
        // Function to load sub categories from data attribute
        function loadSubCategoriesFromData(subCategories, selectedValue = '') {
            const $select = $('#sub_category_code');
            $select.html('<option value="">Pilih Sub Kategori</option>');
            
            if (subCategories && Object.keys(subCategories).length > 0) {
                $.each(subCategories, function(code, name) {
                    $select.append(`<option value="${code}" ${code == selectedValue ? 'selected' : ''}>${code} - ${name}</option>`);
                });
            } else {
                $select.append('<option value="01">01 - Default</option>');
            }
        }
        
        // Function to load sub categories for edit mode
        function loadSubCategories(categoryId, selectedValue = '') {
            // Get the selected option
            const selectedOption = $('#category_id option[value="' + categoryId + '"]');
            const subCategories = selectedOption.data('sub-categories') || {};
            
            loadSubCategoriesFromData(subCategories, selectedValue);
        }
        
        // Function to load KIB form fields
        function loadKIBForm(kibCode) {
            if (!kibCode) {
                $('#kibDataSection').hide();
                return;
            }
            
            // Only show KIB data section for NEW assets (not editing existing ones)
            @if(!isset($asset))
                // Show KIB data section for new assets
                $('#kibDataSection').show();
                
                // Define KIB form templates
                const templates = {
                    'A': `
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Luas Tanah (m²)</label>
                                <input type="number" class="form-control kib-field" name="kib_data[luas]" 
                                       value="{{ old('kib_data.luas') }}"
                                       min="0" step="0.01">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Lokasi Tanah</label>
                                <input type="text" class="form-control kib-field" name="kib_data[lokasi_tanah]"
                                       value="{{ old('kib_data.lokasi_tanah') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status Hak</label>
                                <input type="text" class="form-control kib-field" name="kib_data[status_hak]"
                                       value="{{ old('kib_data.status_hak') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nomor Sertifikat</label>
                                <input type="text" class="form-control kib-field" name="kib_data[sertifikat_nomor]"
                                       value="{{ old('kib_data.sertifikat_nomor') }}">
                            </div>
                        </div>
                    `,
                    'B': `
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Merk</label>
                                <input type="text" class="form-control kib-field" name="kib_data[merk]"
                                       value="{{ old('kib_data.merk') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tipe</label>
                                <input type="text" class="form-control kib-field" name="kib_data[tipe]"
                                       value="{{ old('kib_data.tipe') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Spesifikasi</label>
                                <textarea class="form-control kib-field" name="kib_data[spesifikasi]" rows="3">{{ old('kib_data.spesifikasi') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Bahan</label>
                                <input type="text" class="form-control kib-field" name="kib_data[bahan]"
                                       value="{{ old('kib_data.bahan') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tahun Pembuatan</label>
                                <input type="number" class="form-control kib-field" name="kib_data[tahun_pembuatan]"
                                       value="{{ old('kib_data.tahun_pembuatan') }}"
                                       min="1900" max="{{ date('Y') }}">
                            </div>
                        </div>
                    `,
                    'C': `
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Luas Bangunan (m²)</label>
                                <input type="number" class="form-control kib-field" name="kib_data[luas_bangunan]"
                                       value="{{ old('kib_data.luas_bangunan') }}"
                                       min="0" step="0.01">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jumlah Lantai</label>
                                <input type="number" class="form-control kib-field" name="kib_data[jumlah_lantai]"
                                       value="{{ old('kib_data.jumlah_lantai') }}"
                                       min="1">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Konstruksi</label>
                                <input type="text" class="form-control kib-field" name="kib_data[konstruksi]"
                                       value="{{ old('kib_data.konstruksi') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea class="form-control kib-field" name="kib_data[alamat_lengkap]" rows="3">{{ old('kib_data.alamat_lengkap') }}</textarea>
                            </div>
                        </div>
                    `,
                    'D': `
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Panjang (m)</label>
                                <input type="number" class="form-control kib-field" name="kib_data[panjang]"
                                       value="{{ old('kib_data.panjang') }}"
                                       min="0" step="0.01">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Lebar (m)</label>
                                <input type="number" class="form-control kib-field" name="kib_data[lebar]"
                                       value="{{ old('kib_data.lebar') }}"
                                       min="0" step="0.01">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Konstruksi</label>
                                <input type="text" class="form-control kib-field" name="kib_data[konstruksi]"
                                       value="{{ old('kib_data.konstruksi') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kondisi</label>
                                <select class="form-control kib-field" name="kib_data[kondisi]">
                                    <option value="Baik" {{ old('kib_data.kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="Rusak Ringan" {{ old('kib_data.kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                    <option value="Rusak Berat" {{ old('kib_data.kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                                </select>
                            </div>
                        </div>
                    `,
                    'E': `
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jenis</label>
                                <input type="text" class="form-control kib-field" name="kib_data[jenis]"
                                       value="{{ old('kib_data.jenis') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Spesifikasi</label>
                                <textarea class="form-control kib-field" name="kib_data[spesifikasi]" rows="3">{{ old('kib_data.spesifikasi') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kondisi</label>
                                <select class="form-control kib-field" name="kib_data[kondisi]">
                                    <option value="Baik" {{ old('kib_data.kondisi') == 'Baik' ? 'selected' : '' }}>Baik</option>
                                    <option value="Rusak Ringan" {{ old('kib_data.kondisi') == 'Rusak Ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                                    <option value="Rusak Berat" {{ old('kib_data.kondisi') == 'Rusak Berat' ? 'selected' : '' }}>Rusak Berat</option>
                                </select>
                            </div>
                        </div>
                    `,
                    'F': `
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Kontraktor</label>
                                <input type="text" class="form-control kib-field" name="kib_data[nama_kontraktor]"
                                       value="{{ old('kib_data.nama_kontraktor') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nilai Kontrak (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" class="form-control kib-field" name="kib_data[nilai_kontrak]"
                                           value="{{ old('kib_data.nilai_kontrak') }}"
                                           min="0" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control kib-field" name="kib_data[tanggal_mulai]"
                                       value="{{ old('kib_data.tanggal_mulai') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal Selesai</label>
                                <input type="date" class="form-control kib-field" name="kib_data[tanggal_selesai]"
                                       value="{{ old('kib_data.tanggal_selesai') }}">
                            </div>
                        </div>
                    `
                };
                
                // Set the form template
                $('#kibFormFields').html(templates[kibCode] || '<div class="alert alert-info">Tidak ada data spesifik untuk KIB ini</div>');
            @endif
        }
        
        // Function to preview asset code
        function previewAssetCode() {
            const categoryId = $('#category_id').val();
            const subCategory = $('#sub_category_code').val();
            const year = $('#acquisition_year').val();
            
            if (!categoryId || !subCategory || !year) {
                $('#codePreview').html('<small class="text-muted">Lengkapi kategori, sub kategori, dan tahun untuk preview kode</small>');
                return;
            }
            
            // Get KIB code from selected category
            const kibCode = $('#category_id option:selected').data('kib-code');
            
            $.ajax({
                url: '{{ route("opd.assets.previewCode") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    kib_code: kibCode,
                    sub_category_code: subCategory,
                    acquisition_year: year
                },
                beforeSend: function() {
                    $('#codePreview').html('<div class="spinner-border spinner-border-sm text-primary"></div> Generating...');
                },
                success: function(response) {
                    if (response.success) {
                        $('#codePreview').html(`
                            <strong>Kode Aset yang akan digenerate:</strong><br>
                            <code class="text-primary fs-5">${response.asset_code}</code><br>
                            <small class="text-muted">Klik tombol "Preview" untuk memperbarui</small>
                        `);
                        
                        // Auto-fill the asset code field if empty
                        if (!$('#asset_code').val()) {
                            $('#asset_code').val(response.asset_code);
                        }
                    } else {
                        $('#codePreview').html(`<small class="text-danger">${response.message}</small>`);
                    }
                },
                error: function(xhr) {
                    const error = xhr.responseJSON?.message || 'Terjadi kesalahan';
                    $('#codePreview').html(`<small class="text-danger">${error}</small>`);
                }
            });
        }
        
        // Function to validate form
        function validateForm() {
            let isValid = true;
            let errors = [];
            
            // Basic validation
            if (!$('#name').val()) {
                errors.push('Nama aset harus diisi');
                isValid = false;
            }
            
            if (!$('#category_id').val()) {
                errors.push('Kategori harus dipilih');
                isValid = false;
            }
            
            if (!$('#sub_category_code').val()) {
                errors.push('Sub kategori harus dipilih');
                isValid = false;
            }
            
            if (!$('#value').val() || $('#value').val() < 0) {
                errors.push('Nilai aset harus diisi dengan nilai positif');
                isValid = false;
            }
            
            if (!$('#acquisition_year').val() || $('#acquisition_year').val() < 1900 || $('#acquisition_year').val() > {{ date('Y') }}) {
                errors.push('Tahun perolehan harus antara 1900 dan {{ date('Y') }}');
                isValid = false;
            }
            
            // Show errors if any
            if (errors.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Validasi Gagal',
                    html: '<ul class="text-start"><li>' + errors.join('</li><li>') + '</li></ul>',
                    confirmButtonText: 'OK'
                });
            }
            
            return isValid;
        }
        
        // Enable form if it's disabled but not verified
        @if(isset($asset) && $asset->document_verification_status != 'valid' && $asset->validation_status != 'disetujui')
            // Re-enable disabled select fields and remove hidden inputs
            $('select[disabled]').each(function() {
                $(this).prop('disabled', false);
                $(this).next('input[type="hidden"]').remove();
            });
        @endif
    });
</script>
@endpush