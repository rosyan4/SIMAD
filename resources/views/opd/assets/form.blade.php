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
                <form method="POST" action="{{ $asset ? route('opd.assets.update', $asset) : route('opd.assets.store') }}" enctype="multipart/form-data">
                    @csrf
                    @if($asset)
                        @method('PUT')
                    @endif
                    
                    <!-- Basic Information -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Informasi Dasar</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama Aset *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name', $asset->name ?? '') }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">Kategori *</label>
                                <select class="form-select select2" id="category_id" name="category_id" required onchange="updateSubCategories()">
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}" 
                                                data-kib-code="{{ $category->kib_code }}"
                                                {{ (old('category_id', $asset->category_id ?? '') == $category->category_id) ? 'selected' : '' }}>
                                            {{ $category->kib_code }} - {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="sub_category_code" class="form-label">Sub Kategori *</label>
                                <select class="form-select select2" id="sub_category_code" name="sub_category_code" required onchange="previewAssetCode()">
                                    <option value="">Pilih Sub Kategori</option>
                                    <!-- Options will be populated by JavaScript -->
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="acquisition_year" class="form-label">Tahun Perolehan *</label>
                                <input type="number" class="form-control" id="acquisition_year" name="acquisition_year" 
                                       min="1900" max="{{ date('Y') }}" 
                                       value="{{ old('acquisition_year', $asset->acquisition_year ?? date('Y')) }}" 
                                       required onchange="previewAssetCode()">
                            </div>
                            
                            <div class="col-md-12">
                                <label for="asset_code" class="form-label">Kode Aset</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="asset_code" name="asset_code" 
                                           value="{{ old('asset_code', $asset->asset_code ?? '') }}" readonly>
                                    <button type="button" class="btn btn-outline-secondary" onclick="previewAssetCode()">
                                        <i class="fas fa-sync-alt"></i> Generate
                                    </button>
                                </div>
                                <small class="text-muted" id="asset_code_preview"></small>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="location_id" class="form-label">Lokasi</label>
                                <select class="form-select select2" id="location_id" name="location_id">
                                    <option value="">Pilih Lokasi</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->location_id }}"
                                                {{ (old('location_id', $asset->location_id ?? '') == $location->location_id) ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="condition" class="form-label">Kondisi *</label>
                                <select class="form-select" id="condition" name="condition" required>
                                    <option value="Baik" {{ (old('condition', $asset->condition ?? '') == 'Baik') ? 'selected' : '' }}>Baik</option>
                                    <option value="Rusak Ringan" {{ (old('condition', $asset->condition ?? '') == 'Rusak Ringan') ? 'selected' : '' }}>Rusak Ringan</option>
                                    <option value="Rusak Berat" {{ (old('condition', $asset->condition ?? '') == 'Rusak Berat') ? 'selected' : '' }}>Rusak Berat</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Asset Details -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Detail Aset</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="brand" class="form-label">Merk</label>
                                <input type="text" class="form-control" id="brand" name="brand" 
                                       value="{{ old('brand', $asset->brand ?? '') }}">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="model" class="form-label">Model/Tipe</label>
                                <input type="text" class="form-control" id="model" name="model" 
                                       value="{{ old('model', $asset->model ?? '') }}">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="serial_number" class="form-label">Nomor Seri</label>
                                <input type="text" class="form-control" id="serial_number" name="serial_number" 
                                       value="{{ old('serial_number', $asset->serial_number ?? '') }}">
                            </div>
                            
                            <div class="col-md-6">
                                <label for="value" class="form-label">Nilai (Rp) *</label>
                                <input type="number" class="form-control" id="value" name="value" 
                                       min="0" step="1000" 
                                       value="{{ old('value', $asset->value ?? '0') }}" required>
                            </div>
                            
                            <div class="col-md-12">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $asset->description ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Document Upload -->
                    <div class="mb-4">
                        <h6 class="border-bottom pb-2">Dokumen Pendukung</h6>
                        @if($asset)
                            <div class="mb-3">
                                <small class="text-muted">Unggah dokumen tambahan</small>
                                <input type="file" class="form-control" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">File maksimal 5MB, format: PDF, JPG, PNG</small>
                            </div>
                        @else
                            <div class="mb-3">
                                <label class="form-label">Dokumen Perolehan</label>
                                <input type="file" class="form-control" name="documents[]" multiple required>
                                <small class="text-muted">Upload dokumen perolehan aset (minimal 1 file)</small>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('opd.assets.index') }}" class="btn btn-secondary">Kembali</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Static sub categories data
    const subCategories = {
        'A': {
            '01': 'Tanah Perkantoran',
            '02': 'Tanah Fasilitas Umum', 
            '03': 'Tanah Lainnya'
        },
        'B': {
            '01': 'Alat Berat',
            '02': 'Alat Elektronik',
            '03': 'Kendaraan Dinas',
            '04': 'Peralatan Medis',
            '05': 'Peralatan Olahraga',
            '06': 'Furniture',
            '07': 'Alat Laboratorium'
        },
        'C': {
            '01': 'Gedung Kantor',
            '02': 'Gedung Sekolah',
            '03': 'Rumah Sakit',
            '04': 'Gedung Olahraga (GOR)',
            '05': 'Stadion'
        },
        'D': {
            '01': 'Jalan Kota',
            '02': 'Jembatan',
            '03': 'Jaringan Irigasi',
            '04': 'Jaringan Internet'
        },
        'E': {
            '01': 'Koleksi Perpustakaan',
            '02': 'Aset Lainnya'
        },
        'F': {
            '01': 'Konstruksi Gedung',
            '02': 'Konstruksi Jalan',
            '03': 'Konstruksi Lainnya'
        }
    };
    
    function updateSubCategories() {
        const categorySelect = document.getElementById('category_id');
        const selectedOption = categorySelect.options[categorySelect.selectedIndex];
        const kibCode = selectedOption.getAttribute('data-kib-code');
        const subCategorySelect = document.getElementById('sub_category_code');
        
        // Clear existing options
        subCategorySelect.innerHTML = '<option value="">Pilih Sub Kategori</option>';
        
        if (kibCode && subCategories[kibCode]) {
            // Add options for the selected KIB code
            Object.entries(subCategories[kibCode]).forEach(([code, name]) => {
                const option = document.createElement('option');
                option.value = code;
                option.textContent = `${code} - ${name}`;
                subCategorySelect.appendChild(option);
            });
        }
        
        // Trigger preview
        previewAssetCode();
    }
    
    // Initialize on page load
    $(document).ready(function() {
        // If editing, populate sub categories based on selected category
        @if($asset && $asset->category)
            setTimeout(() => {
                $('#category_id').trigger('change');
                $('#sub_category_code').val('{{ $asset->sub_category_code }}').trigger('change');
            }, 500);
        @endif
    });
</script>
@endpush
@endsection