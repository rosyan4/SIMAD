@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body text-center">
                <div class="mb-3">
                    <div class="avatar bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" 
                         style="width: 100px; height: 100px;">
                        <i class="fas fa-user fa-3x text-white"></i>
                    </div>
                </div>
                <h5>{{ $user->name }}</h5>
                <p class="text-muted">{{ $user->email }}</p>
                <small class="text-muted">Admin OPD</small>
                
                <hr>
                
                <div class="list-group">
                    <a href="{{ route('opd.profile.index', ['tab' => 'profile']) }}" 
                       class="list-group-item list-group-item-action {{ $tab == 'profile' ? 'active' : '' }}">
                        <i class="fas fa-user me-2"></i> Profil
                    </a>
                    <a href="{{ route('opd.profile.index', ['tab' => 'activities']) }}" 
                       class="list-group-item list-group-item-action {{ $tab == 'activities' ? 'active' : '' }}">
                        <i class="fas fa-history me-2"></i> Aktivitas
                    </a>
                    <a href="{{ route('opd.profile.index', ['tab' => 'statistics']) }}" 
                       class="list-group-item list-group-item-action {{ $tab == 'statistics' ? 'active' : '' }}">
                        <i class="fas fa-chart-bar me-2"></i> Statistik
                    </a>
                    <a href="{{ route('opd.profile.index', ['tab' => 'opd']) }}" 
                       class="list-group-item list-group-item-action {{ $tab == 'opd' ? 'active' : '' }}">
                        <i class="fas fa-landmark me-2"></i> Profil OPD
                    </a>
                    <a href="{{ route('opd.profile.index', ['tab' => 'notifications']) }}" 
                       class="list-group-item list-group-item-action {{ $tab == 'notifications' ? 'active' : '' }}">
                        <i class="fas fa-bell me-2"></i> Notifikasi
                    </a>
                    <a href="{{ route('opd.profile.index', ['tab' => 'security']) }}" 
                       class="list-group-item list-group-item-action {{ $tab == 'security' ? 'active' : '' }}">
                        <i class="fas fa-lock me-2"></i> Keamanan
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                @if($tab == 'profile')
                    <!-- Profile Tab -->
                    <h5 class="card-title">Profil Pengguna</h5>
                    <form id="profileForm" method="POST" action="{{ route('opd.profile.update') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama Lengkap *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ old('email', $user->email) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">OPD Unit</label>
                                <input type="text" class="form-control" value="{{ $user->opdUnit->nama_opd ?? '-' }}" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Role</label>
                                <input type="text" class="form-control" value="Admin OPD" readonly>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                    
                @elseif($tab == 'activities')
                    <!-- Activities Tab -->
                    <h5 class="card-title">Riwayat Aktivitas</h5>
                    <div class="table-responsive">
                        <table class="table data-table">
                            <thead>
                                <tr>
                                    <th>Waktu</th>
                                    <th>Aksi</th>
                                    <th>Deskripsi</th>
                                    <th>Aset</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['activities'] as $activity)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($activity->change_date)->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @php
                                            $actionColors = [
                                                'create' => 'success',
                                                'update' => 'warning',
                                                'delete' => 'danger',
                                                'verifikasi' => 'info'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $actionColors[$activity->action] ?? 'secondary' }}">
                                            {{ ucfirst($activity->action) }}
                                        </span>
                                    </td>
                                    <td>{{ $activity->description }}</td>
                                    <td>
                                        {{ $activity->asset_name }}
                                        <br><small class="text-muted">{{ $activity->asset_code }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-center">
                        {{ $data['activities']->links() }}
                    </div>
                    
                @elseif($tab == 'statistics')
                    <!-- Statistics Tab -->
                    <h5 class="card-title mb-4">Statistik Pengguna</h5>
                    
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Statistik Pengguna</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Aset Ditambahkan</td>
                                            <td class="text-end">{{ number_format($data['userStats']['total_assets_created']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Pemeliharaan Dicatat</td>
                                            <td class="text-end">{{ number_format($data['userStats']['total_maintenances_recorded']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Mutasi Diusulkan</td>
                                            <td class="text-end">{{ number_format($data['userStats']['total_mutations_proposed']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Penghapusan Diusulkan</td>
                                            <td class="text-end">{{ number_format($data['userStats']['total_deletions_proposed']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Dokumen Diupload</td>
                                            <td class="text-end">{{ number_format($data['userStats']['total_documents_uploaded']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Login Terakhir</td>
                                            <td class="text-end">{{ $data['userStats']['last_login'] }}</td>
                                        </tr>
                                        <tr>
                                            <td>Akun Dibuat</td>
                                            <td class="text-end">{{ $data['userStats']['account_created'] }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="card-title">Statistik OPD</h6>
                                    <table class="table table-sm">
                                        <tr>
                                            <td>Total Aset</td>
                                            <td class="text-end">{{ number_format($data['opdStats']['total_assets']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Total Nilai</td>
                                            <td class="text-end">Rp {{ number_format($data['opdStats']['total_value'], 0, ',', '.') }}</td>
                                        </tr>
                                        <tr>
                                            <td>Aset Tervalidasi</td>
                                            <td class="text-end">{{ number_format($data['opdStats']['verified_assets']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Aset Aktif</td>
                                            <td class="text-end">{{ number_format($data['opdStats']['active_assets']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Jumlah Lokasi</td>
                                            <td class="text-end">{{ number_format($data['opdStats']['locations_count']) }}</td>
                                        </tr>
                                        <tr>
                                            <td>Aksi Tertunda</td>
                                            <td class="text-end">{{ number_format($data['opdStats']['pending_actions']) }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                @elseif($tab == 'opd')
                    <!-- OPD Profile Tab -->
                    <h5 class="card-title">Profil OPD</h5>
                    <form method="POST" action="{{ route('opd.profile.updateOpdProfile') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="nama_opd" class="form-label">Nama OPD *</label>
                                <input type="text" class="form-control" id="nama_opd" name="nama_opd" 
                                       value="{{ old('nama_opd', $data['opdUnit']->nama_opd ?? '') }}" required>
                            </div>
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
                            <div class="col-md-12">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3">{{ old('alamat', $data['opdUnit']->alamat ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                    
                @elseif($tab == 'notifications')
                    <!-- Notifications Tab -->
                    <h5 class="card-title">Pengaturan Notifikasi</h5>
                    <form method="POST" action="{{ route('opd.profile.updateNotifications') }}">
                        @csrf
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="email_notifications" 
                                       name="email_notifications" value="1" 
                                       {{ $data['notificationPreferences']['email'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_notifications">
                                    Notifikasi Email
                                </label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="push_notifications" 
                                       name="push_notifications" value="1"
                                       {{ $data['notificationPreferences']['push'] ? 'checked' : '' }}>
                                <label class="form-check-label" for="push_notifications">
                                    Notifikasi dalam Aplikasi
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Jenis Notifikasi</label>
                            <div class="row">
                                @foreach(['important' => 'Penting', 'maintenance' => 'Pemeliharaan', 'deletion' => 'Penghapusan', 'mutation' => 'Mutasi', 'verification' => 'Verifikasi', 'audit' => 'Audit'] as $key => $label)
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="notification_types[]" value="{{ $key }}"
                                               id="notif_{{ $key }}"
                                               {{ in_array($key, $data['notificationPreferences']['types']) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="notif_{{ $key }}">
                                            {{ $label }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                        </div>
                    </form>
                    
                @elseif($tab == 'security')
                    <!-- Security Tab -->
                    <h5 class="card-title">Ubah Password</h5>
                    <form method="POST" action="{{ route('opd.profile.changePassword') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="current_password" class="form-label">Password Saat Ini *</label>
                                <input type="password" class="form-control" id="current_password" 
                                       name="current_password" required>
                            </div>
                            <div class="col-md-6">
                                <label for="new_password" class="form-label">Password Baru *</label>
                                <input type="password" class="form-control" id="new_password" 
                                       name="new_password" required minlength="8">
                            </div>
                            <div class="col-md-6">
                                <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru *</label>
                                <input type="password" class="form-control" id="new_password_confirmation" 
                                       name="new_password_confirmation" required minlength="8">
                            </div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">Ubah Password</button>
                        </div>
                    </form>
                    
                    <div class="mt-5">
                        <h6 class="text-muted">Sesi Aktif</h6>
                        <div class="card">
                            <div class="card-body">
                                <p class="mb-1"><strong>Browser:</strong> {{ request()->header('User-Agent') }}</p>
                                <p class="mb-1"><strong>IP Address:</strong> {{ request()->ip() }}</p>
                                <p class="mb-0"><strong>Waktu Login:</strong> {{ now()->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection