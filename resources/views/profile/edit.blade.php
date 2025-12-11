@extends('layouts.app')

@section('title', 'Edit Profil - SIMAK BMN')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Profil</h1>
        <a href="{{ route('profile.show') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Profil</h6>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Avatar Upload -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label class="form-label">Foto Profil</label>
                                <div class="d-flex align-items-center">
                                    <div class="mr-4">
                                        @if($user->avatar)
                                            <img id="avatar-preview" src="{{ asset('storage/' . $user->avatar) }}" 
                                                 class="img-profile rounded-circle border"
                                                 style="width: 100px; height: 100px; object-fit: cover;">
                                        @else
                                            <div id="avatar-preview" class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center" 
                                                 style="width: 100px; height: 100px;">
                                                <span class="text-white h4">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="custom-file">
                                            <input type="file" name="avatar" class="custom-file-input" id="avatar" accept="image/*">
                                            <label class="custom-file-label" for="avatar">Pilih file gambar</label>
                                        </div>
                                        <small class="form-text text-muted">
                                            Format: JPG, PNG, GIF. Maksimal 2MB.
                                        </small>
                                        @if($user->avatar)
                                            <div class="mt-2">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="remove_avatar" id="remove_avatar" value="1">
                                                    <label class="form-check-label text-danger" for="remove_avatar">
                                                        Hapus foto profil
                                                    </label>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Basic Information -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                       value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- OPD Info (Readonly for non-admin) -->
                        @if($user->opdUnit)
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">OPD Unit</label>
                                <input type="text" class="form-control" value="{{ $user->opdUnit->nama_opd }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Role</label>
                                <input type="text" class="form-control" 
                                       value="{{ $user->role == 'admin_utama' ? 'Admin Utama' : 'Admin OPD' }}" readonly>
                            </div>
                        </div>
                        @endif

                        <hr class="my-4">

                        <!-- Password Change -->
                        <h6 class="font-weight-bold text-gray-800 mb-3">Ubah Password</h6>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle mr-2"></i>
                            Kosongkan field password jika tidak ingin mengubah password.
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Password Saat Ini</label>
                                <input type="password" name="current_password" class="form-control @error('current_password') is-invalid @enderror">
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="new_password" class="form-control @error('new_password') is-invalid @enderror">
                                <small class="form-text text-muted">Minimal 8 karakter</small>
                                @error('new_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Konfirmasi Password Baru</label>
                                <input type="password" name="new_password_confirmation" class="form-control">
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Simpan Perubahan
                                    </button>
                                    <a href="{{ route('profile.show') }}" class="btn btn-secondary">
                                        Batal
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Password Requirements -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Persyaratan Password</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check text-success mr-2"></i>
                            Minimal 8 karakter
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success mr-2"></i>
                            Mengandung huruf dan angka
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success mr-2"></i>
                            Tidak menggunakan informasi pribadi
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check text-success mr-2"></i>
                            Tidak sama dengan password lama
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Account Security Tips -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tips Keamanan</h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-shield-alt mr-2"></i>
                        <strong>Jaga kerahasiaan akun Anda</strong>
                    </div>
                    
                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <i class="fas fa-lock text-gray-600 mr-2"></i>
                            Jangan bagikan password ke siapapun
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-sign-out-alt text-gray-600 mr-2"></i>
                            Selalu logout setelah menggunakan komputer bersama
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-sync-alt text-gray-600 mr-2"></i>
                            Ganti password secara berkala
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-envelope text-gray-600 mr-2"></i>
                            Gunakan email aktif untuk verifikasi
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Avatar preview
        $('#avatar').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    if ($('#avatar-preview').hasClass('rounded-circle')) {
                        // It's an image
                        $('#avatar-preview').attr('src', e.target.result);
                    } else {
                        // It's a div, replace with image
                        $('#avatar-preview').replaceWith(`
                            <img id="avatar-preview" src="${e.target.result}" 
                                 class="img-profile rounded-circle border"
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        `);
                    }
                }
                
                reader.readAsDataURL(file);
            }
        });

        // Remove avatar checkbox
        $('#remove_avatar').on('change', function() {
            if ($(this).is(':checked')) {
                // Reset preview to default
                const initial = '{{ strtoupper(substr($user->name, 0, 1)) }}';
                $('#avatar-preview').replaceWith(`
                    <div id="avatar-preview" class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center" 
                         style="width: 100px; height: 100px;">
                        <span class="text-white h4">${initial}</span>
                    </div>
                `);
                $('#avatar').val('');
                $('.custom-file-label').text('Pilih file gambar');
            }
        });

        // Custom file input label
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });

        // Form validation
        $('form').on('submit', function(e) {
            const newPassword = $('input[name="new_password"]').val();
            const confirmPassword = $('input[name="new_password_confirmation"]').val();
            const currentPassword = $('input[name="current_password"]').val();
            
            // If new password is filled
            if (newPassword) {
                // Check if current password is provided
                if (!currentPassword) {
                    e.preventDefault();
                    alert('Harap masukkan password saat ini untuk mengubah password.');
                    $('input[name="current_password"]').focus();
                    return false;
                }
                
                // Check if passwords match
                if (newPassword !== confirmPassword) {
                    e.preventDefault();
                    alert('Password baru dan konfirmasi password tidak cocok.');
                    return false;
                }
                
                // Check password strength
                if (newPassword.length < 8) {
                    e.preventDefault();
                    alert('Password baru harus minimal 8 karakter.');
                    return false;
                }
            }
        });
    });
</script>
@endpush

@push('styles')
<style>
    .img-profile {
        border: 4px solid #f8f9fc;
        box-shadow: 0 0 15px rgba(0,0,0,.1);
        transition: all 0.3s ease;
    }
    
    .img-profile:hover {
        transform: scale(1.05);
        box-shadow: 0 0 20px rgba(0,0,0,.15);
    }
    
    .custom-file-label::after {
        content: "Browse";
    }
</style>
@endpush