<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SIMAD - Login</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-dark: #1e40af;
            --primary-light: #3b82f6;
            --secondary-color: #0ea5e9;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #06b6d4;
            --gray-900: #0f172a;
            --gray-800: #1e293b;
            --gray-700: #334155;
            --gray-600: #475569;
            --gray-500: #64748b;
            --gray-400: #94a3b8;
            --gray-300: #cbd5e1;
            --gray-200: #e2e8f0;
            --gray-100: #f1f5f9;
            --gray-50: #f8fafc;
            --white: #ffffff;
            --border-radius: 12px;
            --border-radius-sm: 8px;
            --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 6px 16px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 24px rgba(0, 0, 0, 0.12);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--gray-50);
            color: var(--gray-900);
            line-height: 1.6;
            font-size: 15px;
            min-height: 100vh;
            overflow-y: auto;
        }
        
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            background-color: var(--gray-50);
        }
        
        .login-container {
            width: 100%;
            max-width: 480px;
            animation: slideUp 0.4s ease-out;
        }
        
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-card {
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            overflow: hidden;
            border: 2px solid var(--gray-200);
        }
        
        .login-header {
            background-color: var(--primary-color);
            color: var(--white);
            padding: 2.5rem 2rem;
            text-align: center;
        }
        
        .login-logo {
            width: 80px;
            height: 80px;
            background: var(--white);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
            font-size: 2rem;
            color: var(--primary-color);
            font-weight: 800;
            font-family: 'Outfit', sans-serif;
            box-shadow: var(--shadow-sm);
            border: 3px solid rgba(255,255,255,0.3);
        }
        
        .login-logo i {
            font-size: 2.25rem;
        }
        
        .login-title {
            font-size: 1.875rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            font-family: 'Outfit', sans-serif;
            letter-spacing: -0.5px;
        }
        
        .login-subtitle {
            font-size: 0.95rem;
            opacity: 0.95;
            font-weight: 500;
        }
        
        .login-body {
            padding: 2rem;
            background-color: var(--white);
        }
        
        .alert {
            border-radius: var(--border-radius-sm);
            border: 2px solid;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: flex-start;
            gap: 0.75rem;
        }
        
        .alert i {
            font-size: 1.2rem;
            padding: 0.5rem;
            border-radius: 8px;
            min-width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border-color: var(--success-color);
        }
        
        .alert-success i {
            background-color: var(--success-color);
            color: var(--white);
        }
        
        .alert-danger {
            background-color: #fee2e2;
            color: #991b1b;
            border-color: var(--danger-color);
        }
        
        .alert-danger i {
            background-color: var(--danger-color);
            color: var(--white);
        }
        
        .alert ul {
            margin: 0;
            padding-left: 1.25rem;
            list-style-type: disc;
        }
        
        .alert ul li {
            margin-bottom: 0.25rem;
        }
        
        .alert ul li:last-child {
            margin-bottom: 0;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--gray-700);
            font-size: 0.9rem;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid var(--gray-300);
            border-radius: var(--border-radius-sm);
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.2s ease;
            background-color: var(--white);
            color: var(--gray-900);
            font-family: 'Inter', sans-serif;
            min-height: 44px;
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            background-color: var(--white);
        }
        
        .form-input::placeholder {
            color: var(--gray-400);
        }
        
        .error-message {
            color: var(--danger-color);
            font-size: 0.85rem;
            margin-top: 0.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }
        
        .error-message i {
            font-size: 0.9rem;
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            user-select: none;
        }
        
        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary-color);
        }
        
        .remember-me span {
            color: var(--gray-700);
            font-weight: 600;
        }
        
        .forgot-password {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s ease;
        }
        
        .forgot-password:hover {
            color: var(--primary-dark);
            text-decoration: underline;
        }
        
        .btn-login {
            width: 100%;
            padding: 0.65rem 1.25rem;
            background-color: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius-sm);
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            font-family: 'Inter', sans-serif;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            min-height: 42px;
        }
        
        .btn-login:hover {
            background-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: var(--shadow-sm);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .btn-login:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .btn-login i {
            font-size: 1rem;
        }
        
        .login-footer {
            text-align: center;
            padding: 1.5rem 2rem;
            border-top: 2px solid var(--gray-200);
            background-color: var(--gray-50);
        }
        
        .login-footer p {
            margin-bottom: 0.5rem;
            color: var(--gray-600);
            font-size: 0.85rem;
            font-weight: 500;
        }
        
        .login-footer p:last-child {
            margin-bottom: 0;
        }
        
        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }
        
        .login-footer a:hover {
            text-decoration: underline;
        }
        
        .login-footer .copyright {
            color: var(--gray-500);
            font-size: 0.8rem;
        }
        
        /* Responsive */
        @media (max-width: 576px) {
            .login-wrapper {
                padding: 1.5rem 0.75rem;
            }
            
            .login-header {
                padding: 2rem 1.5rem;
            }
            
            .login-logo {
                width: 70px;
                height: 70px;
            }
            
            .login-logo i {
                font-size: 2rem;
            }
            
            .login-title {
                font-size: 1.5rem;
            }
            
            .login-subtitle {
                font-size: 0.85rem;
            }
            
            .login-body {
                padding: 1.5rem;
            }
            
            .form-input {
                padding: 0.7rem 0.875rem;
                font-size: 0.9rem;
            }
            
            .btn-login {
                padding: 0.6rem 1rem;
                font-size: 0.85rem;
            }
            
            .remember-forgot {
                flex-direction: column;
                gap: 0.875rem;
                align-items: flex-start;
            }
            
            .login-footer {
                padding: 1.25rem 1.5rem;
            }
        }
        
        /* Selection */
        ::selection {
            background: rgba(37, 99, 235, 0.2);
            color: var(--gray-900);
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-card">
                <div class="login-header">
                    <div class="login-logo">
                        <i class="fas fa-landmark"></i>
                    </div>
                    <h1 class="login-title">SIMAD</h1>
                    <p class="login-subtitle">Sistem Manajemen Aset Digital</p>
                </div>
                
                <div class="login-body">
                    <!-- Session Status -->
                    @if (session('status'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ session('status') }}</span>
                        </div>
                    @endif

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-circle"></i>
                            <div>
                                <strong>Terjadi kesalahan:</strong>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" id="loginForm">
                        @csrf

                        <!-- Email Address -->
                        <div class="form-group">
                            <label class="form-label" for="email">
                                Email
                            </label>
                            <input 
                                id="email" 
                                class="form-input" 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                autofocus 
                                autocomplete="email"
                                placeholder="nama@example.com">
                            @error('email')
                                <span class="error-message">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="form-group">
                            <label class="form-label" for="password">
                                Password
                            </label>
                            <input 
                                id="password" 
                                class="form-input" 
                                type="password" 
                                name="password" 
                                required 
                                autocomplete="current-password"
                                placeholder="Masukkan password Anda">
                            @error('password')
                                <span class="error-message">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Remember Me & Forgot Password -->
                        <div class="remember-forgot">
                            <label class="remember-me">
                                <input type="checkbox" name="remember" id="remember">
                                <span>Ingat saya</span>
                            </label>
                            
                            @if (Route::has('password.request'))
                                <a class="forgot-password" href="{{ route('password.request') }}">
                                    Lupa password?
                                </a>
                            @endif
                        </div>

                        <button type="submit" class="btn-login" id="loginBtn">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>Masuk</span>
                        </button>
                    </form>
                </div>
                
                <div class="login-footer">
                    <p><strong>&copy; {{ date('Y') }} SIMAD</strong> - Sistem Manajemen Aset Digital</p>
                    <p class="copyright">Hak cipta dilindungi undang-undang</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Form submission loading state
        document.getElementById('loginForm').addEventListener('submit', function() {
            const btn = document.getElementById('loginBtn');
            btn.disabled = true;
            btn.querySelector('span').textContent = 'Memproses...';
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 5000);
    </script>
</body>
</html>