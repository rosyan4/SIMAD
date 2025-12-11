<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Sistem Manajemen Aset Daerah') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Custom Styles -->
        <style>
            .guest-layout {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            
            .guest-card {
                background: white;
                border-radius: 12px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                width: 100%;
                max-width: 500px;
                padding: 40px;
            }
            
            .guest-header {
                text-align: center;
                margin-bottom: 30px;
            }
            
            .guest-logo {
                font-size: 32px;
                font-weight: bold;
                color: #1e3a8a;
                margin-bottom: 10px;
            }
            
            .guest-title {
                font-size: 20px;
                color: #4a5568;
                margin-bottom: 5px;
            }
            
            .guest-subtitle {
                font-size: 14px;
                color: #718096;
            }
        </style>
    </head>
    <body>
        <div class="guest-layout">
            <div class="guest-card">
                <div class="guest-header">
                    <div class="guest-logo">SIMAD</div>
                    <h1 class="guest-title">Sistem Manajemen Aset Daerah</h1>
                    <p class="guest-subtitle">{{ config('app.name', 'Laravel') }}</p>
                </div>
                
                {{ $slot }}
            </div>
        </div>
    </body>
</html>