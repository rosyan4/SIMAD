@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-3 mb-4 border-bottom">
        <h1 class="h3">
            <i class="fas fa-user-circle me-2"></i> Profil & Pengaturan
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print"></i> Cetak
                </button>
            </div>
        </div>
    </div>

    <!-- Profile Tabs -->
    <ul class="nav nav-tabs" id="profileTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'profile' ? 'active' : '' }}" 
                    id="profile-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#profile"
                    type="button"
                    onclick="window.location.href='{{ route('opd.profile.index') }}?tab=profile'">
                <i class="fas fa-user me-1"></i> Profil User
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'activities' ? 'active' : '' }}" 
                    id="activities-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#activities"
                    type="button"
                    onclick="window.location.href='{{ route('opd.profile.index') }}?tab=activities'">
                <i class="fas fa-history me-1"></i> Aktivitas
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'statistics' ? 'active' : '' }}" 
                    id="statistics-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#statistics"
                    type="button"
                    onclick="window.location.href='{{ route('opd.profile.index') }}?tab=statistics'">
                <i class="fas fa-chart-bar me-1"></i> Statistik
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'opd' ? 'active' : '' }}" 
                    id="opd-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#opd"
                    type="button"
                    onclick="window.location.href='{{ route('opd.profile.index') }}?tab=opd'">
                <i class="fas fa-building me-1"></i> Profil OPD
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'notifications' ? 'active' : '' }}" 
                    id="notifications-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#notifications"
                    type="button"
                    onclick="window.location.href='{{ route('opd.profile.index') }}?tab=notifications'">
                <i class="fas fa-bell me-1"></i> Notifikasi
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'security' ? 'active' : '' }}" 
                    id="security-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#security"
                    type="button"
                    onclick="window.location.href='{{ route('opd.profile.index') }}?tab=security'">
                <i class="fas fa-lock me-1"></i> Keamanan
            </button>
        </li>
    </ul>

    <div class="tab-content" id="profileTabsContent">
        <!-- Profile Tab -->
        <div class="tab-pane fade {{ $tab == 'profile' ? 'show active' : '' }}" id="profile">
            <div class="row">
                <div class="col-lg-4">
                    <!-- User Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-id-card me-2"></i> Informasi User
                        </div>
                        <div class="card-body text-center">
                            <div class="mb-3">
                                <div class="avatar-circle mb-3" style="width: 120px; height: 120px; background-color: #3498db; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                    <span style="font-size: 48px; color: white;">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <h4>{{ $user->name }}</h4>
                                <p class="text-muted mb-1">{{ $user->email }}</p>
                                <span class="badge bg-primary">{{ $user->display_role }}</span>
                            </div>
                            
                            <hr>
                            
                            <div class="text-start">
                                <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i> Detail Akun</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td>OPD</td>
                                        <td class="text-end">{{ $user->opdUnit->nama_opd ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Login Terakhir</td>
                                        <td class="text-end">
                                            @if($user->last_login)
                                                {{ $user->last_login->format('d/m/Y H:i') }}
                                            @else
                                                Belum pernah
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Akun Dibuat</td>
                                        <td class="text-end">{{ $user->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Umur Akun</td>
                                        <td class="text-end">{{ now()->diffInDays($user->created_at) }} hari</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Stats -->
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-line me-2"></i> Ringkasan Aktivitas
                        </div>
                        <div class="card-body">
                            <div id="userStatsChart" style="height: 200px;"></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-8">
                    <!-- Edit Profile Form -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-edit me-2"></i> Edit Profil
                        </div>
                        <div class="card-body">
                            <form id="profileForm" method="POST" action="{{ route('opd.profile.update') }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Nama Lengkap *</label>
                                        <input type="text" class="form-control" id="name" name="name" 
                                               value="{{ old('name', $user->name) }}" required>
                                        <div class="invalid-feedback" id="nameError"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email *</label>
                                        <input type="email" class="form-control" id="email" name="email" 
                                               value="{{ old('email', $user->email) }}" required>
                                        <div class="invalid-feedback" id="emailError"></div>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Role</label>
                                        <input type="text" class="form-control" value="{{ $user->display_role }}" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">OPD Unit</label>
                                        <input type="text" class="form-control" value="{{ $user->opdUnit->nama_opd ?? '-' }}" readonly>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                        <i class="fas fa-undo me-1"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="updateProfileBtn">
                                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Activity Summary -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fas fa-boxes me-2"></i> Aset
                                </div>
                                <div class="card-body">
                                    <h1 class="display-4 text-center text-primary" id="userAssetCount">0</h1>
                                    <p class="text-center mb-0">Total Aset Yang Dibuat</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <i class="fas fa-tools me-2"></i> Pemeliharaan
                                </div>
                                <div class="card-body">
                                    <h1 class="display-4 text-center text-warning" id="userMaintenanceCount">0</h1>
                                    <p class="text-center mb-0">Pemeliharaan Direkam</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Activities Tab -->
        <div class="tab-pane fade {{ $tab == 'activities' ? 'show active' : '' }}" id="activities">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-history me-2"></i> Riwayat Aktivitas
                    </div>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportActivities('excel')">
                            <i class="fas fa-file-excel"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportActivities('pdf')">
                            <i class="fas fa-file-pdf"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filter -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <label for="filterAction" class="form-label">Aksi</label>
                            <select class="form-select form-select-sm" id="filterAction">
                                <option value="">Semua</option>
                                <option value="create">Create</option>
                                <option value="update">Update</option>
                                <option value="delete">Delete</option>
                                <option value="restore">Restore</option>
                                <option value="verifikasi">Verifikasi</option>
                                <option value="validasi">Validasi</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filterDateFrom" class="form-label">Dari Tanggal</label>
                            <input type="date" class="form-control form-control-sm" id="filterDateFrom">
                        </div>
                        <div class="col-md-3">
                            <label for="filterDateTo" class="form-label">Sampai Tanggal</label>
                            <input type="date" class="form-control form-control-sm" id="filterDateTo">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">&nbsp;</label>
                            <div>
                                <button class="btn btn-sm btn-primary w-100" onclick="filterActivities()">
                                    <i class="fas fa-filter me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Activities Table -->
                    <div class="table-responsive">
                        <table class="table table-hover" id="activitiesTable">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                    <th>Aset</th>
                                    <th>Deskripsi</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($data['activities'] ?? [] as $activity)
                                    <tr>
                                        <td>
                                            {{ \Carbon\Carbon::parse($activity->change_date)->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            @php
                                                $actionColors = [
                                                    'create' => 'success',
                                                    'update' => 'primary',
                                                    'delete' => 'danger',
                                                    'restore' => 'info',
                                                    'verifikasi' => 'warning',
                                                    'validasi' => 'dark'
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $actionColors[$activity->action] ?? 'secondary' }}">
                                                {{ ucfirst($activity->action) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($activity->asset_id)
                                                <a href="{{ route('opd.assets.show', $activity->asset_id) }}" class="text-decoration-none">
                                                    {{ $activity->asset_name }}
                                                </a>
                                                <br>
                                                <small class="text-muted">{{ $activity->asset_code }}</small>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $activity->description }}</td>
                                        <td><small class="text-muted">{{ $activity->ip_address }}</small></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">Tidak ada aktivitas</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if(isset($data['activities']) && $data['activities']->hasPages())
                        <div class="d-flex justify-content-center">
                            {{ $data['activities']->appends(['tab' => 'activities'])->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics Tab -->
        <div class="tab-pane fade {{ $tab == 'statistics' ? 'show active' : '' }}" id="statistics">
            <div class="row">
                <div class="col-lg-6">
                    <!-- User Statistics -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-user-chart me-2"></i> Statistik User
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <tbody>
                                    @if(isset($data['userStats']))
                                        <tr>
                                            <td>Total Aset Dibuat</td>
                                            <td class="text-end fw-bold">{{ number_format($data['userStats']['total_assets_created']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Pemeliharaan Direkam</td>
                                            <td class="text-end fw-bold">{{ number_format($data['userStats']['total_maintenances_recorded']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Mutasi Diajukan</td>
                                            <td class="text-end fw-bold">{{ number_format($data['userStats']['total_mutations_proposed']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Penghapusan Diajukan</td>
                                            <td class="text-end fw-bold">{{ number_format($data['userStats']['total_deletions_proposed']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Dokumen Diupload</td>
                                            <td class="text-end fw-bold">{{ number_format($data['userStats']['total_documents_uploaded']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Login Terakhir</td>
                                            <td class="text-end">{{ $data['userStats']['last_login'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>Akun Dibuat</td>
                                            <td class="text-end">{{ $data['userStats']['account_created'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>Umur Akun</td>
                                            <td class="text-end">{{ $data['userStats']['account_age'] }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <!-- OPD Statistics -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-building-chart me-2"></i> Statistik OPD
                        </div>
                        <div class="card-body">
                            <table class="table table-striped">
                                <tbody>
                                    @if(isset($data['opdStats']))
                                        <tr>
                                            <td>Total Aset</td>
                                            <td class="text-end fw-bold">{{ number_format($data['opdStats']['total_assets']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total Nilai</td>
                                            <td class="text-end fw-bold">Rp {{ number_format($data['opdStats']['total_value'], 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Aset Terverifikasi</td>
                                            <td class="text-end fw-bold">{{ number_format($data['opdStats']['verified_assets']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Aset Aktif</td>
                                            <td class="text-end fw-bold">{{ number_format($data['opdStats']['active_assets']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Lokasi Terdaftar</td>
                                            <td class="text-end fw-bold">{{ number_format($data['opdStats']['locations_count']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Aksi Tertunda</td>
                                            <td class="text-end fw-bold">{{ number_format($data['opdStats']['pending_actions']) }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Monthly Activity Chart -->
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-line me-2"></i> Aktivitas Bulanan
                        </div>
                        <div class="card-body">
                            <canvas id="monthlyActivityChart" height="200"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- OPD Profile Tab -->
        <div class="tab-pane fade {{ $tab == 'opd' ? 'show active' : '' }}" id="opd">
            <div class="row">
                <div class="col-lg-4">
                    <!-- OPD Info Card -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-building me-2"></i> Informasi OPD
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <div class="avatar-circle mb-3" style="width: 100px; height: 100px; background-color: #2c3e50; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-landmark" style="font-size: 48px; color: white;"></i>
                                </div>
                                <h4>{{ $data['opdUnit']->nama_opd ?? 'N/A' }}</h4>
                                <p class="text-muted mb-1">Kode: {{ $data['opdUnit']->kode_opd ?? 'N/A' }}</p>
                            </div>
                            
                            <hr>
                            
                            <div class="text-start">
                                <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i> Detail OPD</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td>Kode Numerik</td>
                                        <td class="text-end">{{ $data['opdUnit']->kode_opd_numeric ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Alamat</td>
                                        <td class="text-end">{{ $data['opdUnit']->alamat ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>Kepala OPD</td>
                                        <td class="text-end">{{ $data['opdUnit']->kepala_opd ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td>NIP Kepala</td>
                                        <td class="text-end">{{ $data['opdUnit']->nip_kepala_opd ?? '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- OPD Quick Stats -->
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-pie me-2"></i> Statistik OPD
                        </div>
                        <div class="card-body">
                            <div id="opdStatsChart" style="height: 200px;"></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-8">
                    <!-- Edit OPD Profile Form -->
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-edit me-2"></i> Edit Profil OPD
                        </div>
                        <div class="card-body">
                            <form id="opdProfileForm" method="POST" action="{{ route('opd.profile.updateOpd') }}">
                                @csrf
                                @method('PUT')
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="nama_opd" class="form-label">Nama OPD *</label>
                                        <input type="text" class="form-control" id="nama_opd" name="nama_opd" 
                                               value="{{ old('nama_opd', $data['opdUnit']->nama_opd ?? '') }}" required>
                                        <div class="invalid-feedback" id="namaOpdError"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Kode OPD</label>
                                        <input type="text" class="form-control" value="{{ $data['opdUnit']->kode_opd ?? '' }}" readonly>
                                        <small class="text-muted">Kode OPD tidak dapat diubah</small>
                                    </div>
                                </div>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="kepala_opd" class="form-label">Kepala OPD</label>
                                        <input type="text" class="form-control" id="kepala_opd" name="kepala_opd" 
                                               value="{{ old('kepala_opd', $data['opdUnit']->kepala_opd ?? '') }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nip_kepala_opd" class="form-label">NIP Kepala OPD</label>
                                        <input type="text" class="form-control" id="nip_kepala_opd" name="nip_kepala_opd" 
                                               value="{{ old('nip_kepala_opd', $data['opdUnit']->nip_kepala_opd ?? '') }}">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat</label>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="3">{{ old('alamat', $data['opdUnit']->alamat ?? '') }}</textarea>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetOpdForm()">
                                        <i class="fas fa-undo me-1"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="updateOpdProfileBtn">
                                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notifications Tab -->
        <div class="tab-pane fade {{ $tab == 'notifications' ? 'show active' : '' }}" id="notifications">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-bell me-2"></i> Pengaturan Notifikasi
                        </div>
                        <div class="card-body">
                            <form id="notificationsForm" method="POST" action="{{ route('opd.profile.updateNotifications') }}">
                                @csrf
                                @method('PUT')
                                
                                <!-- Notification Channels -->
                                <div class="mb-4">
                                    <h5 class="mb-3">Saluran Notifikasi</h5>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications"
                                               {{ old('email_notifications', $data['notificationPreferences']['email'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="email_notifications">
                                            <i class="fas fa-envelope me-2"></i> Notifikasi Email
                                        </label>
                                        <small class="text-muted d-block">Terima notifikasi melalui email</small>
                                    </div>
                                    
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="push_notifications" name="push_notifications"
                                               {{ old('push_notifications', $data['notificationPreferences']['push'] ?? true) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="push_notifications">
                                            <i class="fas fa-mobile-alt me-2"></i> Notifikasi Push
                                        </label>
                                        <small class="text-muted d-block">Terima notifikasi di browser</small>
                                    </div>
                                </div>
                                
                                <!-- Notification Types -->
                                <div class="mb-4">
                                    <h5 class="mb-3">Jenis Notifikasi</h5>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="notif_important" name="notification_types[]" value="important"
                                                       {{ in_array('important', old('notification_types', $data['notificationPreferences']['types'] ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="notif_important">
                                                    <i class="fas fa-exclamation-circle text-danger me-2"></i> Penting
                                                </label>
                                                <small class="text-muted d-block">Notifikasi penting dan darurat</small>
                                            </div>
                                            
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="notif_maintenance" name="notification_types[]" value="maintenance"
                                                       {{ in_array('maintenance', old('notification_types', $data['notificationPreferences']['types'] ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="notif_maintenance">
                                                    <i class="fas fa-tools text-warning me-2"></i> Pemeliharaan
                                                </label>
                                                <small class="text-muted d-block">Jadwal dan status pemeliharaan</small>
                                            </div>
                                            
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="notif_deletion" name="notification_types[]" value="deletion"
                                                       {{ in_array('deletion', old('notification_types', $data['notificationPreferences']['types'] ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="notif_deletion">
                                                    <i class="fas fa-trash-alt text-danger me-2"></i> Penghapusan
                                                </label>
                                                <small class="text-muted d-block">Status proposal penghapusan</small>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="notif_mutation" name="notification_types[]" value="mutation"
                                                       {{ in_array('mutation', old('notification_types', $data['notificationPreferences']['types'] ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="notif_mutation">
                                                    <i class="fas fa-exchange-alt text-primary me-2"></i> Mutasi
                                                </label>
                                                <small class="text-muted d-block">Status mutasi aset</small>
                                            </div>
                                            
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="notif_verification" name="notification_types[]" value="verification"
                                                       {{ in_array('verification', old('notification_types', $data['notificationPreferences']['types'] ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="notif_verification">
                                                    <i class="fas fa-check-circle text-success me-2"></i> Verifikasi
                                                </label>
                                                <small class="text-muted d-block">Status verifikasi dokumen</small>
                                            </div>
                                            
                                            <div class="form-check mb-3">
                                                <input class="form-check-input" type="checkbox" id="notif_audit" name="notification_types[]" value="audit"
                                                       {{ in_array('audit', old('notification_types', $data['notificationPreferences']['types'] ?? [])) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="notif_audit">
                                                    <i class="fas fa-clipboard-check text-info me-2"></i> Audit
                                                </label>
                                                <small class="text-muted d-block">Hasil dan jadwal audit</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Notification Frequency -->
                                <div class="mb-4">
                                    <h5 class="mb-3">Frekuensi Notifikasi</h5>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="frequency" id="frequency_realtime" value="realtime" checked>
                                        <label class="form-check-label" for="frequency_realtime">
                                            Real-time
                                        </label>
                                        <small class="text-muted d-block">Terima notifikasi segera setelah terjadi</small>
                                    </div>
                                    
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="radio" name="frequency" id="frequency_daily" value="daily">
                                        <label class="form-check-label" for="frequency_daily">
                                            Ringkasan Harian
                                        </label>
                                        <small class="text-muted d-block">Terima notifikasi sekali sehari (pukul 17:00)</small>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary" onclick="testNotification()">
                                        <i class="fas fa-bell me-1"></i> Test Notifikasi
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="updateNotificationsBtn">
                                        <i class="fas fa-save me-1"></i> Simpan Pengaturan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <!-- Recent Notifications -->
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-history me-2"></i> Notifikasi Terbaru
                        </div>
                        <div class="card-body">
                            <div class="list-group list-group-flush" id="recentNotifications">
                                <!-- Notifications will be loaded here -->
                                <div class="text-center py-4 text-muted">
                                    <i class="fas fa-bell-slash fa-2x mb-2"></i>
                                    <p>Tidak ada notifikasi</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security Tab -->
        <div class="tab-pane fade {{ $tab == 'security' ? 'show active' : '' }}" id="security">
            <div class="row">
                <div class="col-lg-6">
                    <!-- Change Password Form -->
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-key me-2"></i> Ubah Password
                        </div>
                        <div class="card-body">
                            <form id="changePasswordForm" method="POST" action="{{ route('opd.profile.changePassword') }}">
                                @csrf
                                
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Password Saat Ini *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="currentPasswordError"></div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">Password Baru *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="newPasswordError"></div>
                                    <small class="text-muted">Minimal 8 karakter</small>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru *</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                                        <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('new_password_confirmation')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                    <div class="invalid-feedback" id="confirmPasswordError"></div>
                                </div>
                                
                                <!-- Password Strength Meter -->
                                <div class="mb-4">
                                    <label class="form-label">Kekuatan Password</label>
                                    <div class="progress mb-1" style="height: 5px;">
                                        <div class="progress-bar" id="passwordStrengthBar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small id="passwordStrengthText" class="text-muted"></small>
                                </div>
                                
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-outline-secondary" onclick="resetPasswordForm()">
                                        <i class="fas fa-undo me-1"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="changePasswordBtn">
                                        <i class="fas fa-save me-1"></i> Ubah Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-6">
                    <!-- Security Information -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-shield-alt me-2"></i> Informasi Keamanan
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6><i class="fas fa-lightbulb me-2"></i> Tips Keamanan</h6>
                                <ul class="mb-0">
                                    <li>Gunakan password yang kuat dan unik</li>
                                    <li>Jangan gunakan password yang sama untuk akun lain</li>
                                    <li>Selalu logout setelah selesai menggunakan aplikasi</li>
                                    <li>Jangan bagikan password kepada siapapun</li>
                                    <li>Perbarui password secara berkala</li>
                                </ul>
                            </div>
                            
                            <h6 class="mt-4">Sesi Aktif</h6>
                            <table class="table table-sm">
                                <tbody>
                                    <tr>
                                        <td>IP Address Saat Ini</td>
                                        <td class="text-end">{{ request()->ip() }}</td>
                                    </tr>
                                    <tr>
                                        <td>Browser</td>
                                        <td class="text-end">{{ request()->header('User-Agent') }}</td>
                                    </tr>
                                    <tr>
                                        <td>Login Terakhir</td>
                                        <td class="text-end">
                                            @if($user->last_login)
                                                {{ $user->last_login->format('d/m/Y H:i:s') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>Sesi Dimulai</td>
                                        <td class="text-end">{{ now()->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Two-Factor Authentication -->
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-mobile-alt me-2"></i> Two-Factor Authentication
                        </div>
                        <div class="card-body">
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="twoFactorAuth" disabled>
                                <label class="form-check-label" for="twoFactorAuth">
                                    Two-Factor Authentication
                                </label>
                                <small class="text-muted d-block">Fitur ini akan segera tersedia</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        .avatar-circle {
            transition: transform 0.3s;
        }
        
        .avatar-circle:hover {
            transform: scale(1.05);
        }
        
        .progress-bar {
            transition: width 0.5s;
        }
        
        .list-group-item {
            border-left: none;
            border-right: none;
            padding: 12px 0;
        }
        
        .list-group-item:first-child {
            border-top: none;
        }
        
        .list-group-item:last-child {
            border-bottom: none;
        }
        
        .notification-item {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .notification-item:last-child {
            border-bottom: none;
        }
        
        .notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
        }
        
        .notification-important { background-color: #ffeaea; color: #dc3545; }
        .notification-maintenance { background-color: #fff3cd; color: #ffc107; }
        .notification-deletion { background-color: #f8d7da; color: #dc3545; }
        .notification-mutation { background-color: #d1ecf1; color: #0dcaf0; }
        .notification-verification { background-color: #d4edda; color: #198754; }
        .notification-audit { background-color: #e2e3e5; color: #6c757d; }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Initialize data when page loads
        $(document).ready(function() {
            loadUserStats();
            loadRecentNotifications();
            
            // Initialize password strength checker
            $('#new_password').on('keyup', checkPasswordStrength);
            
            // Load charts for statistics tab
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
                if (e.target.id === 'statistics-tab') {
                    loadStatisticsCharts();
                }
            });
        });
        
        // Load user statistics
        function loadUserStats() {
            $.ajax({
                url: "{{ route('opd.profile.userStats') }}",
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        $('#userAssetCount').text(response.stats.total_assets_created.toLocaleString());
                        $('#userMaintenanceCount').text(response.stats.total_maintenances_recorded.toLocaleString());
                        
                        // Update user stats chart
                        renderUserStatsChart(response.stats);
                    }
                }
            });
        }
        
        // Render user statistics chart
        function renderUserStatsChart(stats) {
            const ctx = document.getElementById('userStatsChart');
            if (!ctx) return;
            
            const chartData = {
                labels: ['Aset', 'Pemeliharaan', 'Mutasi', 'Penghapusan', 'Dokumen'],
                datasets: [{
                    data: [
                        stats.total_assets_created,
                        stats.total_maintenances_recorded,
                        stats.total_mutations_proposed,
                        stats.total_deletions_proposed,
                        stats.total_documents_uploaded
                    ],
                    backgroundColor: [
                        '#3498db',
                        '#f39c12',
                        '#9b59b6',
                        '#e74c3c',
                        '#2ecc71'
                    ]
                }]
            };
            
            new Chart(ctx, {
                type: 'polarArea',
                data: chartData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
        
        // Load statistics charts
        function loadStatisticsCharts() {
            // Load monthly activity chart
            $.ajax({
                url: "{{ route('opd.profile.userStats') }}",
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        renderMonthlyActivityChart(response.stats);
                    }
                }
            });
            
            // Load OPD stats chart
            $.ajax({
                url: "{{ route('opd.profile.opdStats') }}",
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        renderOpdStatsChart(response.stats);
                    }
                }
            });
        }
        
        // Render monthly activity chart
        function renderMonthlyActivityChart(stats) {
            const ctx = document.getElementById('monthlyActivityChart');
            if (!ctx) return;
            
            // This is sample data - in real app, you would get monthly data from API
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const activities = [12, 19, 8, 15, 22, 18, 25, 12, 19, 15, 22, 18];
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Aktivitas',
                        data: activities,
                        borderColor: '#3498db',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        borderWidth: 2,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        
        // Render OPD stats chart
        function renderOpdStatsChart(stats) {
            const ctx = document.getElementById('opdStatsChart');
            if (!ctx) return;
            
            const chartData = {
                labels: ['Total Aset', 'Aset Aktif', 'Terverifikasi', 'Lokasi', 'Tertunda'],
                datasets: [{
                    data: [
                        stats.total_assets,
                        stats.active_assets,
                        stats.verified_assets,
                        stats.locations_count,
                        stats.pending_actions
                    ],
                    backgroundColor: [
                        '#3498db',
                        '#2ecc71',
                        '#27ae60',
                        '#9b59b6',
                        '#f39c12'
                    ]
                }]
            };
            
            new Chart(ctx, {
                type: 'doughnut',
                data: chartData,
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
        
        // Load recent notifications
        function loadRecentNotifications() {
            // Simulate loading notifications
            setTimeout(() => {
                const notifications = [
                    {
                        id: 1,
                        type: 'maintenance',
                        title: 'Pemeliharaan Terjadwal',
                        message: 'Aset "Komputer Server" perlu pemeliharaan rutin',
                        time: '10 menit yang lalu',
                        read: false
                    },
                    {
                        id: 2,
                        type: 'verification',
                        title: 'Dokumen Diverifikasi',
                        message: 'Dokumen pengadaan aset telah diverifikasi',
                        time: '1 jam yang lalu',
                        read: true
                    },
                    {
                        id: 3,
                        type: 'mutation',
                        title: 'Mutasi Disetujui',
                        message: 'Mutasi aset ke OPD Pendidikan telah disetujui',
                        time: '2 jam yang lalu',
                        read: true
                    }
                ];
                
                const container = $('#recentNotifications');
                container.empty();
                
                if (notifications.length === 0) {
                    container.html(`
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-bell-slash fa-2x mb-2"></i>
                            <p>Tidak ada notifikasi</p>
                        </div>
                    `);
                    return;
                }
                
                notifications.forEach(notif => {
                    const typeClasses = {
                        'important': 'notification-important',
                        'maintenance': 'notification-maintenance',
                        'deletion': 'notification-deletion',
                        'mutation': 'notification-mutation',
                        'verification': 'notification-verification',
                        'audit': 'notification-audit'
                    };
                    
                    const typeIcons = {
                        'important': 'fa-exclamation-circle',
                        'maintenance': 'fa-tools',
                        'deletion': 'fa-trash-alt',
                        'mutation': 'fa-exchange-alt',
                        'verification': 'fa-check-circle',
                        'audit': 'fa-clipboard-check'
                    };
                    
                    container.append(`
                        <div class="notification-item d-flex align-items-start ${notif.read ? '' : 'fw-bold'}">
                            <div class="notification-icon ${typeClasses[notif.type] || 'notification-audit'}">
                                <i class="fas ${typeIcons[notif.type] || 'fa-bell'}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <h6 class="mb-1">${notif.title}</h6>
                                    <small class="text-muted">${notif.time}</small>
                                </div>
                                <p class="mb-0">${notif.message}</p>
                            </div>
                        </div>
                    `);
                });
            }, 1000);
        }
        
        // Filter activities
        function filterActivities() {
            const action = $('#filterAction').val();
            const dateFrom = $('#filterDateFrom').val();
            const dateTo = $('#filterDateTo').val();
            
            // This would normally be an AJAX request
            // For now, just show a message
            showToast('Filter diterapkan', 'info');
        }
        
        // Export activities
        function exportActivities(format) {
            showToast(`Mengekspor data aktivitas ke ${format.toUpperCase()}...`, 'info');
            // In real implementation, trigger file download
        }
        
        // Test notification
        function testNotification() {
            showToast('Test notifikasi berhasil dikirim!', 'success');
        }
        
        // Check password strength
        function checkPasswordStrength() {
            const password = $('#new_password').val();
            let strength = 0;
            let text = '';
            let color = '';
            
            if (password.length >= 8) strength++;
            if (/[a-z]/.test(password)) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            
            switch(strength) {
                case 0:
                case 1:
                    text = 'Sangat Lemah';
                    color = '#dc3545';
                    break;
                case 2:
                    text = 'Lemah';
                    color = '#ffc107';
                    break;
                case 3:
                    text = 'Cukup';
                    color = '#17a2b8';
                    break;
                case 4:
                    text = 'Kuat';
                    color = '#28a745';
                    break;
                case 5:
                    text = 'Sangat Kuat';
                    color = '#20c997';
                    break;
            }
            
            const width = (strength / 5) * 100;
            $('#passwordStrengthBar')
                .css('width', width + '%')
                .css('background-color', color);
            $('#passwordStrengthText')
                .text(text)
                .css('color', color);
        }
        
        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = $('#' + fieldId);
            const type = field.attr('type') === 'password' ? 'text' : 'password';
            field.attr('type', type);
        }
        
        // Reset forms
        function resetForm() {
            $('#profileForm')[0].reset();
            showToast('Form berhasil direset', 'info');
        }
        
        function resetOpdForm() {
            $('#opdProfileForm')[0].reset();
            showToast('Form berhasil direset', 'info');
        }
        
        function resetPasswordForm() {
            $('#changePasswordForm')[0].reset();
            $('#passwordStrengthBar').css('width', '0%');
            $('#passwordStrengthText').text('');
            showToast('Form berhasil direset', 'info');
        }
        
        // AJAX form submissions
        $('#profileForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const button = $('#updateProfileBtn');
            const originalText = button.html();
            
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');
            
            $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        
                        // Update user info on page
                        if (response.user) {
                            $('.avatar-circle span').text(response.user.name.charAt(0).toUpperCase());
                            $('h4:contains("' + $user->name + '")').text(response.user.name);
                            $('.text-muted:contains("' + $user->email + '")').text(response.user.email);
                        }
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(field => {
                            const errorDiv = $('#' + field + 'Error');
                            const input = $('#' + field);
                            
                            input.addClass('is-invalid');
                            errorDiv.text(errors[field][0]);
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
        });
        
        $('#opdProfileForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const button = $('#updateOpdProfileBtn');
            const originalText = button.html();
            
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');
            
            $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        
                        // Update OPD info on page
                        if (response.opdUnit) {
                            $('h4:contains("' + ($data['opdUnit']->nama_opd ?? '') + '")').text(response.opdUnit.nama_opd);
                        }
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(field => {
                            const errorDiv = $('#' + field + 'Error');
                            const input = $('#' + field);
                            
                            input.addClass('is-invalid');
                            errorDiv.text(errors[field][0]);
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
        });
        
        $('#notificationsForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const button = $('#updateNotificationsBtn');
            const originalText = button.html();
            
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Menyimpan...');
            
            $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                    }
                },
                error: function(xhr) {
                    handleAjaxError(xhr);
                },
                complete: function() {
                    button.prop('disabled', false).html(originalText);
                }
            });
        });
        
        $('#changePasswordForm').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const button = $('#changePasswordBtn');
            const originalText = button.html();
            
            // Validate password match
            const newPassword = $('#new_password').val();
            const confirmPassword = $('#new_password_confirmation').val();
            
            if (newPassword !== confirmPassword) {
                $('#new_password_confirmation').addClass('is-invalid');
                $('#confirmPasswordError').text('Password tidak cocok');
                showToast('Password tidak cocok', 'danger');
                return;
            }
            
            button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Mengubah...');
            
            $.ajax({
                url: form.attr('action'),
                method: form.attr('method'),
                data: form.serialize(),
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        form[0].reset();
                        $('#passwordStrengthBar').css('width', '0%');
                        $('#passwordStrengthText').text('');
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        const errors = xhr.responseJSON.errors;
                        Object.keys(errors).forEach(field => {
                            const errorDiv = $('#' + field + 'Error');
                            const input = $('#' + field);
                            
                            input.addClass('is-invalid');
                            errorDiv.text(errors[field][0]);
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
        });
    </script>
    @endpush
@endsection