<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MED — Admin Panel & Avtorizatsiya Markazi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-body: #080c14;
            --bg-surface: #0f172a;
            --bg-card: #131d31;
            --bg-card-hover: #192640;
            --border-color: rgba(255, 255, 255, 0.09);
            --border-focus: #0ea5e9;
            
            --primary: #0284c7;
            --primary-light: #38bdf8;
            --primary-glow: rgba(56, 189, 248, 0.28);
            
            --medical-green: #10b981;
            --medical-green-glow: rgba(16, 185, 129, 0.25);
            --medical-red: #ef4444;
            --medical-red-glow: rgba(239, 68, 68, 0.25);
            --medical-amber: #f59e0b;
            
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --text-dim: #64748b;
            
            --glass-bg: rgba(19, 29, 49, 0.78);
            --glass-border: rgba(255, 255, 255, 0.12);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            background-image: 
                radial-gradient(circle at 15% 15%, rgba(2, 132, 199, 0.16) 0%, transparent 45%),
                radial-gradient(circle at 85% 85%, rgba(16, 185, 129, 0.14) 0%, transparent 45%),
                radial-gradient(circle at 50% 50%, rgba(15, 23, 42, 0.9) 0%, transparent 100%);
            background-attachment: fixed;
            position: relative;
            overflow-x: hidden;
        }

        /* Subtle cyber grid background */
        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 40px 40px;
            pointer-events: none;
            z-index: 0;
        }

        /* Top Header */
        .auth-header {
            position: relative;
            z-index: 10;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem 2.5rem;
            border-bottom: 1px solid var(--border-color);
            background: rgba(15, 23, 42, 0.65);
            backdrop-filter: blur(16px);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            text-decoration: none;
            color: var(--text-main);
        }

        .brand-logo {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px var(--primary-glow);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .brand-text h1 {
            font-size: 1.25rem;
            font-weight: 800;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .brand-badge {
            font-size: 0.68rem;
            padding: 0.2rem 0.6rem;
            background: rgba(14, 165, 233, 0.15);
            color: var(--primary-light);
            border: 1px solid rgba(14, 165, 233, 0.35);
            border-radius: 6px;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .brand-sub {
            font-size: 0.78rem;
            color: var(--text-muted);
            margin-top: 0.15rem;
        }

        .system-status {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.82rem;
            color: var(--medical-green);
            background: rgba(16, 185, 129, 0.1);
            padding: 0.4rem 0.9rem;
            border-radius: 20px;
            border: 1px solid rgba(16, 185, 129, 0.25);
            font-weight: 600;
        }

        .pulse-dot {
            width: 8px;
            height: 8px;
            background-color: var(--medical-green);
            border-radius: 50%;
            box-shadow: 0 0 10px var(--medical-green);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.95); opacity: 0.8; }
            50% { transform: scale(1.3); opacity: 1; }
            100% { transform: scale(0.95); opacity: 0.8; }
        }

        /* Main Container */
        .auth-container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 560px;
            margin: 3rem auto;
            padding: 0 1.5rem;
            display: flex;
            justify-content: center;
        }

        /* Login Form Card */
        .login-card {
            width: 100%;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2.5rem;
            backdrop-filter: blur(24px);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6), 0 0 35px rgba(2, 132, 199, 0.15);
            position: relative;
            overflow: hidden;
        }

        .login-card::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0284c7, #38bdf8, #10b981);
        }

        .card-header-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.8rem;
            background: rgba(14, 165, 233, 0.12);
            border: 1px solid rgba(14, 165, 233, 0.3);
            color: var(--primary-light);
            border-radius: 8px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        .card-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--text-main);
            letter-spacing: -0.02em;
            margin-bottom: 0.5rem;
        }

        .card-subtitle {
            color: var(--text-muted);
            font-size: 0.92rem;
            line-height: 1.5;
            margin-bottom: 1.75rem;
        }

        /* Alerts */
        .alert {
            padding: 0.85rem 1.15rem;
            border-radius: 10px;
            margin-bottom: 1.25rem;
            font-size: 0.88rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #fca5a5;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #6ee7b7;
        }

        /* Form Inputs */
        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 0.5rem;
        }

        .form-label span.req {
            color: var(--primary-light);
            font-size: 0.75rem;
            font-weight: 500;
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-icon {
            position: absolute;
            left: 1.1rem;
            color: var(--text-dim);
            pointer-events: none;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }

        .form-input {
            width: 100%;
            background: rgba(15, 23, 42, 0.75);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 0.88rem 1rem 0.88rem 3rem;
            color: var(--text-main);
            font-size: 0.95rem;
            outline: none;
            transition: all 0.25s ease;
        }

        .form-input:focus {
            border-color: var(--border-focus);
            background: rgba(15, 23, 42, 0.95);
            box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.2);
        }

        /* Prevent Chrome white autofill box */
        .form-input:-webkit-autofill,
        .form-input:-webkit-autofill:hover, 
        .form-input:-webkit-autofill:focus, 
        .form-input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px #0f172a inset !important;
            -webkit-text-fill-color: #ffffff !important;
            caret-color: #ffffff !important;
            transition: background-color 5000s ease-in-out 0s;
        }

        .form-input:focus + .input-icon,
        .input-wrapper:focus-within .input-icon {
            color: var(--primary-light);
        }

        .toggle-password {
            position: absolute;
            right: 1.1rem;
            background: transparent;
            border: none;
            color: var(--text-dim);
            cursor: pointer;
            padding: 0.25rem;
            display: flex;
            align-items: center;
            transition: color 0.2s;
        }

        /* Role Selector Pills (Admin, Doctor & Patient) */
        .role-selector {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
            margin-bottom: 1.25rem;
        }

        @media (max-width: 520px) {
            .role-selector {
                grid-template-columns: 1fr;
            }
        }

        .role-pill {
            position: relative;
        }

        .role-pill input[type="radio"] {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .role-label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            padding: 0.75rem 0.6rem;
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s ease;
            text-align: center;
        }

        .role-label svg {
            color: var(--text-muted);
            transition: transform 0.2s, color 0.2s;
            flex-shrink: 0;
        }

        .role-label span {
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-muted);
            transition: color 0.2s;
        }

        .role-label:hover {
            border-color: rgba(56, 189, 248, 0.4);
            background: rgba(14, 165, 233, 0.08);
        }

        .role-pill input[type="radio"]:checked + .role-label {
            background: rgba(14, 165, 233, 0.18);
            border-color: var(--primary-light);
            box-shadow: 0 0 18px rgba(14, 165, 233, 0.3);
        }

        .role-pill input[type="radio"]:checked + .role-label svg {
            color: var(--primary-light);
            transform: scale(1.1);
        }

        .role-pill input[type="radio"]:checked + .role-label span {
            color: #ffffff;
            font-weight: 700;
        }



        /* Extra Row */
        .form-extra {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 0.82rem;
        }

        .remember-box {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-muted);
            cursor: pointer;
        }

        .remember-box input {
            accent-color: var(--primary);
            width: 16px;
            height: 16px;
            border-radius: 4px;
            cursor: pointer;
        }

        .secure-badge {
            display: flex;
            align-items: center;
            gap: 0.35rem;
            color: var(--medical-green);
            font-weight: 500;
        }

        /* Submit Button */
        .btn-submit {
            width: 100%;
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #ffffff;
            font-size: 1rem;
            font-weight: 700;
            padding: 0.95rem 1.5rem;
            border-radius: 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            box-shadow: 0 8px 24px rgba(2, 132, 199, 0.4);
            transition: all 0.25s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(2, 132, 199, 0.55);
            background: linear-gradient(135deg, #0369a1 0%, #0ea5e9 100%);
        }

        .btn-submit:active {
            transform: translateY(0);
        }

        .btn-submit:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
        }

        .spinner {
            display: none;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #ffffff;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }



        /* Footer */
        .auth-footer {
            position: relative;
            z-index: 10;
            text-align: center;
            padding: 1.5rem;
            font-size: 0.8rem;
            color: var(--text-dim);
            border-top: 1px solid var(--border-color);
        }
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <header class="auth-header">
        <a href="{{ route('login') }}" class="brand">
            <div class="brand-logo">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2v20M2 12h20"/>
                </svg>
            </div>
            <div class="brand-text">
                <h1>
                    MED
                    <span class="brand-badge">Sog'liqni Saqlash E-Tizimi</span>
                </h1>
                <div class="brand-sub">Elektron Tibbiy Karta & Bemorlar Ma'lumotlar Bazasi</div>
            </div>
        </a>

        <div class="system-status">
            <span class="pulse-dot"></span>
            <span>Tizim Onlayn (Faol)</span>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="auth-container">

        <!-- Left: Login Card -->
        <div class="login-card">
            <div class="card-header-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
                Admin & Boshqaruv Markazi
            </div>
            <h2 class="card-title">Tizimga Kirish</h2>
            <p class="card-subtitle">
                Elektron tibbiy kartalar, laboratoriya tahlillari va reseptlar bazasini boshqarish uchun ma'lumotlaringizni kiriting.
            </p>

            @if(session('status'))
                <div class="alert alert-success">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                    </svg>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger" id="serverErrorAlert">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <div class="alert alert-danger" id="jsAlert" style="display: none;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
                <span id="jsAlertMessage"></span>
            </div>

            <form action="{{ route('login.post') }}" method="POST" id="loginForm" autocomplete="off">
                @csrf

                <!-- 1. Ism va Familiya -->
                <div class="form-group">
                    <label class="form-label" for="nameInput">
                        <span>Ism va Familiyangiz</span>
                    </label>
                    <div class="input-wrapper" id="nameInputWrapper">
                        <div class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            id="nameInput" 
                            name="name" 
                            class="form-input" 
                            value=""
                            placeholder="Ism va familiyangizni kiriting"
                            autocomplete="name"
                        >
                    </div>
                    <div class="field-error" id="nameError" style="display: none; color: #f87171; font-size: 0.78rem; margin-top: 6px; font-weight: 600;"></div>
                </div>

                <!-- 2. Telefon raqam yoki Gmail -->
                <div class="form-group">
                    <label class="form-label" for="loginInput">
                        <span>Telefon raqam yoki Gmail (Email)</span>
                    </label>
                    <div class="input-wrapper" id="loginInputWrapper">
                        <div class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            id="loginInput" 
                            name="login" 
                            class="form-input" 
                            value=""
                            placeholder="+998 90 123 45 67 yoki email@med.uz"
                            autocomplete="username"
                        >
                    </div>
                    <div class="field-error" id="loginError" style="display: none; color: #f87171; font-size: 0.78rem; margin-top: 6px; font-weight: 600;"></div>
                </div>

                <!-- 3. Maxfiy Parol -->
                <div class="form-group">
                    <label class="form-label" for="passwordInput">
                        <span>Maxfiy Parol</span>
                    </label>
                    <div class="input-wrapper" id="passwordInputWrapper">
                        <div class="input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                                <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                            </svg>
                        </div>
                        <input 
                            type="password" 
                            id="passwordInput" 
                            name="password" 
                            class="form-input" 
                            value=""
                            placeholder="Maxfiy parolingizni kiriting"
                            autocomplete="current-password"
                        >
                        <button type="button" class="toggle-password" id="togglePasswordBtn" title="Parolni ko'rsatish/yashirish">
                            <svg id="eyeIcon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                        </button>
                    </div>
                    <div class="field-error" id="passwordError" style="display: none; color: #f87171; font-size: 0.78rem; margin-top: 6px; font-weight: 600;"></div>
                </div>

                <!-- Kirish Huquqi: Admin, Shifokor va Bemor -->
                <div class="form-group" style="margin-top: 1.25rem;">
                    <label class="form-label">
                        <span>Kirish Huquqi</span>
                    </label>
                    <div class="role-selector" id="roleSelectorBox">
                        <div class="role-pill">
                            <input type="radio" id="roleAdmin" name="role" value="admin">
                            <label class="role-label" for="roleAdmin">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                                </svg>
                                <span>Admin</span>
                            </label>
                        </div>

                        <div class="role-pill">
                            <input type="radio" id="roleDoctor" name="role" value="doctor">
                            <label class="role-label" for="roleDoctor">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3"></path>
                                    <path d="M8 15v1a6 6 0 0 0 6 6v0a6 6 0 0 0 6-6v-4"></path>
                                    <circle cx="20" cy="10" r="2"></circle>
                                </svg>
                                <span>Shifokor</span>
                            </label>
                        </div>

                        <div class="role-pill">
                            <input type="radio" id="rolePatient" name="role" value="patient">
                            <label class="role-label" for="rolePatient">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="8" r="5"></circle>
                                    <path d="M20 21a8 8 0 1 0-16 0"></path>
                                </svg>
                                <span>Bemor</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Extra: Secure badge -->
                <div class="form-extra" style="justify-content: flex-end;">
                    <div class="secure-badge">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                        </svg>
                        <span>256-bit Shifrlangan Ulanish</span>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn-submit" id="submitBtn">
                    <span class="spinner" id="btnSpinner"></span>
                    <span id="btnText">Tizimga Kirish va Boshqaruv Panelini Ochish →</span>
                </button>
            </form>
        </div>



    </main>

    <!-- Footer -->
    <footer class="auth-footer">
        O'zbekiston Respublikasi Sog'liqni Saqlash Vazirligi • Milliy Yagona Elektron Tibbiy Tizim &copy; 2026
    </footer>

    <script>
        // Toggle password visibility
        const togglePasswordBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('passwordInput');
        const eyeIcon = document.getElementById('eyeIcon');
        const nameInput = document.getElementById('nameInput');
        const loginInput = document.getElementById('loginInput');

        if (togglePasswordBtn && passwordInput && eyeIcon) {
            togglePasswordBtn.addEventListener('click', function() {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                
                if (isPassword) {
                    eyeIcon.innerHTML = `
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path>
                        <line x1="1" y1="1" x2="23" y2="23"></line>
                    `;
                } else {
                    eyeIcon.innerHTML = `
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8z"></path>
                        <circle cx="12" cy="12" r="3"></circle>
                    `;
                }
            });
        }

        // Live input typing listeners to remove errors immediately
        const nameWrap = document.getElementById('nameInputWrapper');
        const loginWrap = document.getElementById('loginInputWrapper');
        const passWrap = document.getElementById('passwordInputWrapper');
        const nameErr = document.getElementById('nameError');
        const loginErr = document.getElementById('loginError');
        const passErr = document.getElementById('passwordError');

        nameInput?.addEventListener('input', function() {
            if (nameWrap) nameWrap.style.borderColor = '';
            if (nameErr) nameErr.style.display = 'none';
        });

        loginInput?.addEventListener('input', function() {
            if (loginWrap) loginWrap.style.borderColor = '';
            if (loginErr) loginErr.style.display = 'none';

            const val = (loginInput.value || '').replace(/\s+/g, '').toLowerCase();
            if (val.includes('910226667') || val.includes('admin') || val.includes('azizbek')) {
                const adminRadio = document.getElementById('roleAdmin');
                if (adminRadio) adminRadio.checked = true;
            }
        });

        passwordInput?.addEventListener('input', function() {
            if (passWrap) passWrap.style.borderColor = '';
            if (passErr) passErr.style.display = 'none';
        });

        document.querySelectorAll('input[name="role"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const roleBox = document.getElementById('roleSelectorBox');
                if (roleBox) {
                    roleBox.style.border = 'none';
                    roleBox.style.padding = '0';
                }
            });
        });

        // Form submission with explicit field checking and reliable redirect
        const loginForm = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');
        const btnSpinner = document.getElementById('btnSpinner');
        const jsAlert = document.getElementById('jsAlert');
        const jsAlertMessage = document.getElementById('jsAlertMessage');

        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();

            // Clear previous errors
            if (jsAlert) jsAlert.style.display = 'none';
            if (nameWrap) nameWrap.style.borderColor = '';
            if (loginWrap) loginWrap.style.borderColor = '';
            if (passWrap) passWrap.style.borderColor = '';
            if (nameErr) nameErr.style.display = 'none';
            if (loginErr) loginErr.style.display = 'none';
            if (passErr) passErr.style.display = 'none';

            const nameVal = (nameInput ? nameInput.value : '').trim();
            const loginVal = (loginInput ? loginInput.value : '').trim();
            const passVal = (passwordInput ? passwordInput.value : '');

            // 1. Ism kiritilmagan bo'lsa
            if (!nameVal) {
                if (nameWrap) nameWrap.style.borderColor = '#ef4444';
                if (nameErr) {
                    nameErr.innerText = "Iltimos, ism va familiyangizni kiriting!";
                    nameErr.style.display = 'block';
                }
                jsAlertMessage.innerText = "Iltimos, ism va familiyangizni kiriting!";
                jsAlert.style.display = 'flex';
                nameInput.focus();
                return;
            }

            // 2. Telefon yoki email kiritilmagan bo'lsa
            if (!loginVal) {
                if (loginWrap) loginWrap.style.borderColor = '#ef4444';
                if (loginErr) {
                    loginErr.innerText = "Iltimos, telefon raqamingiz yoki emailingizni kiriting!";
                    loginErr.style.display = 'block';
                }
                jsAlertMessage.innerText = "Iltimos, telefon raqamingiz yoki emailingizni kiriting!";
                jsAlert.style.display = 'flex';
                loginInput.focus();
                return;
            }

            // 3. Parol kiritilmagan bo'lsa
            if (!passVal) {
                if (passWrap) passWrap.style.borderColor = '#ef4444';
                if (passErr) {
                    passErr.innerText = "Iltimos, maxfiy parolingizni kiriting!";
                    passErr.style.display = 'block';
                }
                jsAlertMessage.innerText = "Iltimos, maxfiy parolingizni kiriting!";
                jsAlert.style.display = 'flex';
                passwordInput.focus();
                return;
            }

            if (passVal.length < 4) {
                if (passWrap) passWrap.style.borderColor = '#ef4444';
                if (passErr) {
                    passErr.innerText = "Parol kamida 4 ta belgidan iborat bo'lishi kerak!";
                    passErr.style.display = 'block';
                }
                jsAlertMessage.innerText = "Parol kamida 4 ta belgidan iborat bo'lishi kerak!";
                jsAlert.style.display = 'flex';
                passwordInput.focus();
                return;
            }

            // Agar rol tanlanmagan bo'lsa, avtomatik mosini belgilash
            let selectedRole = loginForm.querySelector('input[name="role"]:checked');
            const cleanLogin = loginVal.replace(/\s+/g, '').toLowerCase();
            if (!selectedRole) {
                if (cleanLogin.includes('910226667') || cleanLogin.includes('admin') || cleanLogin.includes('azizbek')) {
                    const adminRadio = document.getElementById('roleAdmin');
                    if (adminRadio) adminRadio.checked = true;
                } else {
                    const patientRadio = document.getElementById('rolePatient');
                    if (patientRadio) patientRadio.checked = true;
                }
            }

            // Loading state
            submitBtn.disabled = true;
            btnSpinner.style.display = 'inline-block';
            btnText.innerText = "Kirilmoqda...";

            const formData = new FormData(loginForm);

            try {
                const response = await fetch("{{ route('login.post') }}", {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    sessionStorage.setItem('med_auth_session', '1');
                    btnText.innerText = "Muvaffaqiyatli! Ochilmoqda...";
                    submitBtn.style.background = "linear-gradient(135deg, #059669 0%, #10b981 100%)";
                    
                    const redirectUrl = (data.redirect || '/med').replace(/^http:\/\//i, 'https://');
                    window.location.replace(redirectUrl);
                } else {
                    submitBtn.disabled = false;
                    btnSpinner.style.display = 'none';
                    btnText.innerText = "Tizimga Kirish va Boshqaruv Panelini Ochish →";

                    let errorMsg = data.message || "Kirishda xatolik yuz berdi. Ma'lumotlarni tekshiring.";
                    if (data.errors) {
                        const firstErrorKey = Object.keys(data.errors)[0];
                        if (firstErrorKey && data.errors[firstErrorKey][0]) {
                            errorMsg = data.errors[firstErrorKey][0];
                        }
                    }

                    jsAlertMessage.innerText = errorMsg;
                    jsAlert.style.display = 'flex';
                }
            } catch (err) {
                console.warn('AJAX fallback to regular submit:', err);
                loginForm.submit();
            }
        });
    </script>
</body>
</html>
