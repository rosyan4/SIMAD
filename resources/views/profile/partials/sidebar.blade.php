<div class="card mb-3">
    <div class="card-body text-center">
        @if($user->avatar)
            <img src="{{ Storage::url($user->avatar) }}" 
                 alt="{{ $user->name }}" 
                 class="img-thumbnail rounded-circle mb-3" 
                 style="width: 120px; height: 120px; object-fit: cover;">
        @else
            <div class="avatar-placeholder rounded-circle d-flex align-items-center justify-content-center bg-primary text-white mb-3 mx-auto"
                 style="width: 120px; height: 120px; font-size: 36px;">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
        @endif
        
        <h5 class="card-title">{{ $user->name }}</h5>
        <p class="text-muted mb-1">{{ $user->email }}</p>
        
        <div class="mb-2">
            @if($user->role === 'admin_utama')
                <span class="badge bg-primary">
                    <i class="fas fa-crown me-1"></i>Admin Utama
                </span>
            @else
                <span class="badge bg-success">
                    <i class="fas fa-building me-1"></i>Admin OPD
                </span>
            @endif
        </div>
        
        @if($user->opdUnit)
            <p class="text-muted mb-0">
                <i class="fas fa-building me-1"></i>{{ $user->opdUnit->nama_opd }}
            </p>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h6 class="mb-0"><i class="fas fa-user-circle me-2"></i>Menu Profil</h6>
    </div>
    <div class="list-group list-group-flush">
        <a href="{{ route('profile.show') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('profile.show') ? 'active' : '' }}">
            <i class="fas fa-user me-2"></i>Profil Saya
        </a>
        <a href="{{ route('profile.edit') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
            <i class="fas fa-edit me-2"></i>Edit Profil
        </a>
        <a href="{{ route('profile.activity') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('profile.activity') ? 'active' : '' }}">
            <i class="fas fa-history me-2"></i>Aktivitas
        </a>
    </div>
</div>