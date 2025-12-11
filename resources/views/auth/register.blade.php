<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftarkan User Baru - SIMAD</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    
    <!-- Styles -->
    <style>
        .register-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .register-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 500px;
            overflow: hidden;
        }
        
        .register-header {
            background: #1e3a8a;
            color: white;
            padding: 25px;
            text-align: center;
        }
        
        .register-title {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .register-subtitle {
            font-size: 14px;
            opacity: 0.8;
        }
        
        .register-body {
            padding: 25px;
        }
        
        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #4a5568;
            text-decoration: none;
            margin-bottom: 20px;
            font-weight: 500;
        }
        
        .back-button:hover {
            color: #2d3748;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #4a5568;
        }
        
        .form-input,
        .form-select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .role-selection {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .role-option {
            flex: 1;
        }
        
        .role-card {
            padding: 15px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .role-card:hover {
            border-color: #cbd5e0;
        }
        
        .role-card.selected {
            border-color: #667eea;
            background: #ebf4ff;
        }
        
        .role-title {
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .role-description {
            font-size: 14px;
            color: #718096;
        }
        
        .opd-section {
            margin-top: 20px;
            padding: 15px;
            background: #f7fafc;
            border-radius: 8px;
            display: none;
        }
        
        .opd-section.show {
            display: block;
        }
        
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: #1e3a8a;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-submit:hover {
            background: #2d4a9c;
        }
        
        .error-message {
            color: #e53e3e;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
    
    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleRadios = document.querySelectorAll('input[name="role"]');
            const opdSection = document.getElementById('opdSection');
            const opdSelect = document.getElementById('opd_unit_id');
            
            roleRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    // Update UI for selected role
                    document.querySelectorAll('.role-card').forEach(card => {
                        card.classList.remove('selected');
                    });
                    this.closest('.role-card').classList.add('selected');
                    
                    // Show/hide OPD section
                    if (this.value === 'admin_opd') {
                        opdSection.classList.add('show');
                        opdSelect.required = true;
                    } else {
                        opdSection.classList.remove('show');
                        opdSelect.required = false;
                    }
                });
            });
            
            // Set initial state
            const selectedRole = document.querySelector('input[name="role"]:checked');
            if (selectedRole && selectedRole.value === 'admin_opd') {
                opdSection.classList.add('show');
                opdSelect.required = true;
            }
        });
    </script>
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <h1 class="register-title">Daftarkan User Baru</h1>
                <p class="register-subtitle">Sistem Manajemen Aset Daerah</p>
            </div>
            
            <div class="register-body">
                <a href="{{ route('admin.users.index') }}" class="back-button">
                    ← Kembali ke Daftar User
                </a>

                <!-- Validation Errors -->
                @if ($errors->any())
                    <div style="background: #fed7d7; color: #742a2a; padding: 12px; border-radius: 8px; margin-bottom: 20px;">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Name -->
                    <div class="form-group">
                        <label class="form-label" for="name">Nama Lengkap</label>
                        <input id="name" class="form-input" type="text" name="name" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Email Address -->
                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" required>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input id="password" class="form-input" type="password" name="password" required>
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Konfirmasi Password</label>
                        <input id="password_confirmation" class="form-input" type="password" name="password_confirmation" required>
                    </div>

                    <!-- Role Selection -->
                    <div class="form-group">
                        <label class="form-label">Pilih Role</label>
                        <div class="role-selection">
                            <div class="role-option">
                                <label class="role-card {{ old('role') === 'admin_utama' ? 'selected' : '' }}">
                                    <input type="radio" name="role" value="admin_utama" 
                                           {{ old('role') === 'admin_utama' ? 'checked' : '' }} 
                                           style="display: none;">
                                    <div class="role-title">Admin Utama</div>
                                    <div class="role-description">Akses ke semua data dan pengaturan sistem</div>
                                </label>
                            </div>
                            
                            <div class="role-option">
                                <label class="role-card {{ old('role') === 'admin_opd' ? 'selected' : '' }}">
                                    <input type="radio" name="role" value="admin_opd" 
                                           {{ old('role') === 'admin_opd' ? 'checked' : '' }} 
                                           style="display: none;">
                                    <div class="role-title">Admin OPD</div>
                                    <div class="role-description">Hanya akses data OPD tertentu</div>
                                </label>
                            </div>
                        </div>
                        @error('role')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- OPD Selection (only for admin_opd) -->
                    <div id="opdSection" class="opd-section {{ old('role') === 'admin_opd' ? 'show' : '' }}">
                        <label class="form-label" for="opd_unit_id">Pilih OPD</label>
                        <select id="opd_unit_id" name="opd_unit_id" class="form-select">
                            <option value="">-- Pilih OPD --</option>
                            @foreach($opdUnits as $opdUnit)
                                <option value="{{ $opdUnit->opd_unit_id }}" 
                                        {{ old('opd_unit_id') == $opdUnit->opd_unit_id ? 'selected' : '' }}>
                                    {{ $opdUnit->kode_opd }} - {{ $opdUnit->nama_opd }}
                                </option>
                            @endforeach
                        </select>
                        @error('opd_unit_id')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="btn-submit">
                        Daftarkan User
                    </button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>