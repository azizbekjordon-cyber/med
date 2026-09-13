<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>MED Pass — {{ $med->user?->name ?? 'Bemor' }} | Kasalxona & Dorixona Portali</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-body: #0a0e17;
            --bg-surface: #111827;
            --bg-card: #162032;
            --bg-card-hover: #1c2a42;
            --border-color: rgba(255, 255, 255, 0.08);
            --border-focus: #0ea5e9;
            
            --primary: #0284c7;
            --primary-light: #38bdf8;
            --primary-glow: rgba(56, 189, 248, 0.25);
            
            --medical-green: #10b981;
            --medical-green-glow: rgba(16, 185, 129, 0.25);
            --medical-red: #ef4444;
            --medical-red-glow: rgba(239, 68, 68, 0.25);
            --medical-amber: #f59e0b;
            
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --text-dim: #64748b;
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
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(2, 132, 199, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(16, 185, 129, 0.1) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(15, 23, 42, 0.8) 0%, transparent 100%);
            background-attachment: fixed;
            padding-bottom: 4rem;
        }

        /* Top Header */
        .scan-header {
            background: rgba(17, 24, 39, 0.9);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-color);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: #fff;
        }

        .brand-badge {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.1rem;
            box-shadow: 0 0 15px var(--primary-glow);
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.1rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), #0369a1);
            color: #fff;
            box-shadow: 0 4px 15px var(--primary-glow);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px var(--primary-glow);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid var(--border-color);
            color: var(--text-main);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.12);
        }

        .btn-green {
            background: linear-gradient(135deg, var(--medical-green), #047857);
            color: #fff;
            box-shadow: 0 4px 15px var(--medical-green-glow);
        }

        .btn-green:hover {
            transform: translateY(-1px);
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        /* Alert notifications */
        .alert-banner {
            background: rgba(16, 185, 129, 0.12);
            border: 1px solid rgba(16, 185, 129, 0.3);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #6ee7b7;
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.12);
            border-color: rgba(245, 158, 11, 0.3);
            color: #fcd34d;
        }

        /* Patient Hero Profile Card */
        .patient-hero {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 1.75rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
        }

        .hero-glow {
            position: absolute;
            top: -50%;
            right: -20%;
            width: 350px;
            height: 350px;
            background: radial-gradient(circle, rgba(2, 132, 199, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .patient-main-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .patient-identity {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .avatar-box {
            width: 76px;
            height: 76px;
            border-radius: 18px;
            background: linear-gradient(135deg, #1e293b, #0f172a);
            border: 2px solid rgba(56, 189, 248, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            box-shadow: 0 8px 20px rgba(0,0,0,0.3);
        }

        .patient-names h1 {
            font-size: 1.75rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.5px;
        }

        .patient-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.4rem;
        }

        .badge {
            padding: 3px 9px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
        }

        .badge-blue { background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.25); }
        .badge-green { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.25); }
        .badge-red { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.25); }
        .badge-amber { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.25); }

        .blood-card {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 14px;
            padding: 0.75rem 1.25rem;
            text-align: center;
        }

        .blood-card .val {
            font-size: 1.5rem;
            font-weight: 800;
            color: #f87171;
        }

        .blood-card .lbl {
            font-size: 0.7rem;
            color: #fca5a5;
            text-transform: uppercase;
            font-weight: 600;
        }

        /* Critical Warning Strip */
        .critical-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--border-color);
        }

        .critical-box {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            padding: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .critical-box-title {
            font-size: 0.78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .pill-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
        }

        .pill {
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .pill-allergy { background: rgba(239, 68, 68, 0.2); color: #fca5a5; }
        .pill-chronic { background: rgba(245, 158, 11, 0.2); color: #fde68a; }

        /* Mode Tabs Switcher */
        .mode-nav {
            display: flex;
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 0.4rem;
            margin-bottom: 1.75rem;
            gap: 0.4rem;
            overflow-x: auto;
        }

        .mode-btn {
            flex: 1;
            min-width: 170px;
            padding: 0.85rem 1rem;
            border-radius: 12px;
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .mode-btn:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.04);
        }

        .mode-btn.active {
            background: linear-gradient(135deg, rgba(2, 132, 199, 0.2), rgba(14, 165, 233, 0.25));
            border: 1px solid rgba(56, 189, 248, 0.4);
            color: #fff;
            box-shadow: 0 4px 15px rgba(2, 132, 199, 0.15);
        }

        .mode-btn .count {
            background: rgba(255, 255, 255, 0.1);
            padding: 2px 7px;
            border-radius: 10px;
            font-size: 0.72rem;
        }

        /* Tab Content Panes */
        .tab-pane {
            display: none;
        }

        .tab-pane.active {
            display: block;
            animation: fadeIn 0.25s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Content Layout */
        .grid-layout {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 1.75rem;
        }

        @media (max-width: 960px) {
            .grid-layout {
                grid-template-columns: 1fr;
            }
        }

        /* Card Section */
        .content-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 18px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.25rem;
            padding-bottom: 0.85rem;
            border-bottom: 1px solid var(--border-color);
        }

        .card-header h2 {
            font-size: 1.15rem;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Active Medications List */
        .med-item-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            transition: border-color 0.2s;
            position: relative;
        }

        .med-item-card:hover {
            border-color: rgba(56, 189, 248, 0.3);
        }

        .med-item-card.dispensed {
            opacity: 0.75;
            background: rgba(17, 24, 39, 0.6);
            border-left: 4px solid var(--medical-green);
        }

        .med-item-card.active-rx {
            border-left: 4px solid var(--primary-light);
        }

        .med-header-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 0.6rem;
        }

        .med-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #fff;
        }

        .med-dosage-tag {
            background: rgba(56, 189, 248, 0.15);
            color: var(--primary-light);
            padding: 2px 8px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-family: 'JetBrains Mono', monospace;
            font-weight: 600;
            margin-left: 0.5rem;
        }

        .rx-number {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.75rem;
            color: var(--text-dim);
        }

        .schedule-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(245, 158, 11, 0.12);
            color: #fbbf24;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.82rem;
            font-weight: 600;
            margin: 0.4rem 0;
        }

        .med-meta-text {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.5rem;
            line-height: 1.5;
        }

        /* Forms */
        .form-group {
            margin-bottom: 1.1rem;
        }

        .form-group label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.35rem;
        }

        .form-control {
            width: 100%;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.7rem 0.9rem;
            color: #fff;
            font-size: 0.85rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: var(--primary-light);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        /* Timeline for records */
        .record-card {
            border-left: 3px solid var(--primary);
            padding-left: 1.25rem;
            position: relative;
            margin-bottom: 1.5rem;
        }

        .record-card::before {
            content: '';
            position: absolute;
            left: -7px;
            top: 4px;
            width: 11px;
            height: 11px;
            border-radius: 50%;
            background: var(--primary-light);
            box-shadow: 0 0 10px var(--primary-light);
        }

        .record-diagnosis {
            font-size: 1.05rem;
            font-weight: 700;
            color: #fff;
        }

        .record-meta {
            font-size: 0.78rem;
            color: var(--text-dim);
            margin: 0.25rem 0 0.6rem 0;
            display: flex;
            flex-wrap: wrap;
            gap: 0.8rem;
        }

        .record-box {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 0.9rem;
            font-size: 0.83rem;
            line-height: 1.6;
            color: #cbd5e1;
        }

        /* Schedule Timeline Box */
        .daily-routine-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1rem;
        }

        .routine-slot {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 1.25rem;
            position: relative;
            overflow: hidden;
        }

        .routine-slot-header {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        /* Print media query */
        @media print {
            body {
                background: #fff !important;
                color: #000 !important;
            }
            .scan-header, .mode-nav, .no-print {
                display: none !important;
            }
            .content-card, .patient-hero {
                border: 1px solid #ccc !important;
                background: #fff !important;
                color: #000 !important;
                box-shadow: none !important;
            }
            .med-item-card, .record-box {
                background: #f8fafc !important;
                border: 1px solid #ddd !important;
                color: #000 !important;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="scan-header">
        <a href="{{ route('med.portal') }}" class="brand-link">
            <div class="brand-badge">MED</div>
            <div>
                <div style="font-weight: 800; font-size: 1.05rem; letter-spacing: 0.5px;">MED-PASS VERIFIED</div>
                <div style="font-size: 0.7rem; color: var(--medical-green); display: flex; align-items: center; gap: 4px;">
                    <span style="width: 6px; height: 6px; background: var(--medical-green); border-radius: 50%;"></span>
                    QR-Kod Skaneri & Rasmiy Tibbiy Tizim
                </div>
            </div>
        </a>

        <div class="header-actions">
            <button type="button" class="btn btn-secondary no-print" onclick="window.print()">
                <span>🖨️</span> Chop Etish
            </button>
            <a href="{{ route('med.portal', ['med' => $med->med_number]) }}" class="btn btn-primary no-print">
                <span>💻</span> Asosiy Portal
            </a>
        </div>
    </header>

    <div class="container">

        <!-- Flash messages -->
        @if(session('success'))
            <div class="alert-banner">
                <span style="font-size: 1.25rem;">✅</span>
                <div>
                    <strong>Muvaffaqiyatli:</strong> {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert-banner alert-warning">
                <span style="font-size: 1.25rem;">⚠️</span>
                <div>{{ session('warning') }}</div>
            </div>
        @endif

        <!-- Patient Summary Card -->
        <div class="patient-hero">
            <div class="hero-glow"></div>
            <div class="patient-main-row">
                <div class="patient-identity">
                    <div class="avatar-box">
                        {{ ($med->user?->gender ?? 'male') === 'female' ? '👩' : '👨' }}
                    </div>
                    <div class="patient-names">
                        <h1>{{ $med->user?->name ?? 'Bemor' }}</h1>
                        <div class="patient-tags">
                            <span class="badge badge-blue">Karta: {{ $med->med_number }}</span>
                            <span class="badge badge-green">JSHSHIR: {{ $med->user?->pinfl ?? '32509820010025' }}</span>
                            <span class="badge badge-amber">Turi: {{ ucfirst($med->card_type) }}</span>
                            <span class="badge badge-blue">
                                {{ $med->user?->birth_date ? $med->user->birth_date->format('d.m.Y') : 'Noma\'lum sana' }} 
                                ({{ $med->user?->birth_date ? $med->user->birth_date->age . ' yosh' : '' }})
                            </span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; align-items: center;">
                    <div class="blood-card">
                        <div class="val">{{ $med->blood_group }}</div>
                        <div class="lbl">{{ $med->rhesus_factor === 'positive' ? 'Rh+ Musbat' : 'Rh- Manfiy' }}</div>
                    </div>
                </div>
            </div>

            <!-- Urgent parameters row -->
            <div class="critical-grid">
                <div class="critical-box">
                    <div class="critical-box-title" style="color: #fca5a5;">
                        <span>⚠️</span> Hayotiy Xavfli Allergiyalar
                    </div>
                    <div class="pill-list">
                        @if(!empty($med->allergies) && count($med->allergies) > 0)
                            @foreach($med->allergies as $allergy)
                                <span class="pill pill-allergy">⚠️ {{ $allergy }}</span>
                            @endforeach
                        @else
                            <span style="font-size: 0.78rem; color: var(--text-dim);">Allergik reaksiya qayd etilmagan</span>
                        @endif
                    </div>
                </div>

                <div class="critical-box">
                    <div class="critical-box-title" style="color: #fde68a;">
                        <span>📋</span> Surunkali Kasalliklar
                    </div>
                    <div class="pill-list">
                        @if(!empty($med->chronic_diseases) && count($med->chronic_diseases) > 0)
                            @foreach($med->chronic_diseases as $chronic)
                                <span class="pill pill-chronic">{{ $chronic }}</span>
                            @endforeach
                        @else
                            <span style="font-size: 0.78rem; color: var(--text-dim);">Surunkali kasallik yo'q</span>
                        @endif
                    </div>
                </div>

                <div class="critical-box">
                    <div class="critical-box-title" style="color: var(--primary-light);">
                        <span>📞</span> Favqulodda Bog'lanish (Yaqini)
                    </div>
                    <div style="font-size: 0.85rem; font-weight: 600; color: #fff;">
                        {{ $med->emergency_contact_name ?? 'Ko\'rsatilmagan' }} 
                        <span style="font-size: 0.75rem; color: var(--text-dim);">({{ $med->emergency_contact_relation ?? 'Yaqini' }})</span>
                    </div>
                    @if($med->emergency_contact_phone)
                        <a href="tel:{{ $med->emergency_contact_phone }}" style="color: var(--primary-light); font-family: 'JetBrains Mono', monospace; font-size: 0.9rem; text-decoration: none; display: inline-block; margin-top: 4px;">
                            ☎️ {{ $med->emergency_contact_phone }}
                        </a>
                    @else
                        <span style="font-size: 0.75rem; color: var(--text-dim);">Telefon kiritilmagan</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Mode Navigation (Kasalxona, Dorixona, Tahlillar, Emlashlar, Dori jadvali) -->
        <nav class="mode-nav no-print">
            <button type="button" class="mode-btn {{ request('tab', 'hospital') === 'hospital' ? 'active' : '' }}" onclick="switchMode('tabHospital', this)">
                <span>🏥</span> Kasalxona & Ko'rik
                <span class="count">{{ $med->records->count() }}</span>
            </button>
            <button type="button" class="mode-btn {{ request('tab') === 'pharmacy' ? 'active' : '' }}" onclick="switchMode('tabPharmacy', this)">
                <span>🏪</span> Dorixona
                <span class="count">{{ $activePrescriptions->count() }} faol</span>
            </button>
            <button type="button" class="mode-btn {{ request('tab') === 'prescriptions' ? 'active' : '' }}" onclick="switchMode('tabPrescriptions', this)">
                <span>💊</span> Dorilari
                <span class="count">{{ $med->prescriptions->count() }}</span>
            </button>
            <button type="button" class="mode-btn {{ request('tab') === 'analyses' ? 'active' : '' }}" onclick="switchMode('tabAnalyses', this)">
                <span>🔬</span> Tahlillar
                <span class="count">{{ $med->analyses->count() }}</span>
            </button>
            <button type="button" class="mode-btn {{ request('tab') === 'vaccinations' ? 'active' : '' }}" onclick="switchMode('tabVaccinations', this)">
                <span>💉</span> Emlash & Yo'llanma
                <span class="count">{{ $med->vaccinations->count() }}</span>
            </button>
            <button type="button" class="mode-btn {{ request('tab') === 'schedule' ? 'active' : '' }}" onclick="switchMode('tabSchedule', this)">
                <span>⏰</span> Kunlik Jadval
            </button>
        </nav>

        <!-- ============================================== -->
        <!-- TAB 1: KASALXONA & SHIFOKOR KO'RIGI (HOSPITAL) -->
        <!-- ============================================== -->
        <div id="tabHospital" class="tab-pane {{ request('tab', 'hospital') === 'hospital' ? 'active' : '' }}">
            <div class="grid-layout">
                <!-- Left: Clinical history & current medications -->
                <div>
                    <div class="content-card">
                        <div class="card-header">
                            <h2><span>🩺</span> Bemorning Kasallik Tarixi va Tashxislar (EHR)</h2>
                            <span style="font-size: 0.8rem; color: var(--text-dim);">Jami: {{ $med->records->count() }} ta ko'rik</span>
                        </div>

                        @forelse($med->records as $record)
                            <div class="record-card">
                                <div class="record-diagnosis">{{ $record->diagnosis }}</div>
                                <div class="record-meta">
                                    <span>👨‍⚕️ {{ $record->doctor->name ?? 'Navbatchi Shifokor' }} ({{ $record->doctor->specialty ?? 'Terapevt' }})</span>
                                    <span>🏥 {{ $record->clinic->name ?? 'Shifoxona' }}</span>
                                    <span>📅 {{ $record->visit_date ? $record->visit_date->format('d.m.Y H:i') : '' }}</span>
                                    @if($record->icd10_code)
                                        <span class="badge badge-blue">ICD-10: {{ $record->icd10_code }}</span>
                                    @endif
                                </div>

                                @if($record->vitals && count($record->vitals) > 0)
                                    <div style="display: flex; gap: 0.75rem; margin-bottom: 0.6rem; flex-wrap: wrap;">
                                        @if(isset($record->vitals['blood_pressure']))
                                            <span class="badge badge-amber">Bosim: {{ $record->vitals['blood_pressure'] }}</span>
                                        @endif
                                        @if(isset($record->vitals['heart_rate']))
                                            <span class="badge badge-red">Puls: {{ $record->vitals['heart_rate'] }}</span>
                                        @endif
                                        @if(isset($record->vitals['temperature']))
                                            <span class="badge badge-blue">Harorat: {{ $record->vitals['temperature'] }}</span>
                                        @endif
                                    </div>
                                @endif

                                @if($record->symptoms)
                                    <div style="margin-bottom: 0.5rem;">
                                        <strong style="font-size: 0.75rem; color: var(--text-dim); text-transform: uppercase;">Shikoyatlar:</strong>
                                        <div class="record-box">{{ $record->symptoms }}</div>
                                    </div>
                                @endif

                                @if($record->treatment_plan)
                                    <div>
                                        <strong style="font-size: 0.75rem; color: var(--text-dim); text-transform: uppercase;">Tavsiya va Davolash Rejasi:</strong>
                                        <div class="record-box" style="border-left: 3px solid var(--primary-light);">{{ $record->treatment_plan }}</div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                                📝 Hozircha ushbu med-kartada avvalgi ko'rik qaydlari yo'q. Shifokor o'ng tarafdagi forma orqali yangi ko'rik qo'shishi mumkin.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Right: Doctor action panel (Quick diagnosis & prescription) -->
                <div>
                    <div class="content-card no-print" style="position: sticky; top: 80px;">
                        <div class="card-header">
                            <h2><span>✍️</span> Shifokor Kabineti</h2>
                            <span class="badge badge-blue">Noutbuk / PC</span>
                        </div>
                        <p style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 1.25rem;">
                            Kasalxona shifokori bemor ko'rigi bo'yicha tashxis va davolash rejasini shu yerdan kiritadi.
                        </p>

                        <form action="{{ route('med.scan.addRecord', $med->qr_token) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Qabul Qiluvchi Shifokor</label>
                                <select name="doctor_id" class="form-control" required>
                                    @foreach($doctors as $doc)
                                        <option value="{{ $doc->id }}">{{ $doc->name }} ({{ $doc->specialty }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Shifoxona / Poliklinika</label>
                                <select name="clinic_id" class="form-control" required>
                                    @foreach($clinics as $cl)
                                        <option value="{{ $cl->id }}">{{ $cl->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Klinik Tashxis</label>
                                <input type="text" name="diagnosis" class="form-control" placeholder="Masalan: O'tkir bronxit, Gipertoniya..." required>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Qon Bosimi (BP)</label>
                                    <input type="text" name="blood_pressure" class="form-control" placeholder="120/80">
                                </div>
                                <div class="form-group">
                                    <label>Puls (HR)</label>
                                    <input type="text" name="heart_rate" class="form-control" placeholder="78">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Bemor Shikoyatlari</label>
                                <textarea name="symptoms" class="form-control" rows="2" placeholder="Og'riq, holsizlik, yo'tal..."></textarea>
                            </div>

                            <div class="form-group">
                                <label>Tavsiya va Davolash Rejasi</label>
                                <textarea name="treatment_plan" class="form-control" rows="2" placeholder="Tavsiyalar..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.75rem;">
                                🩺 Ko'rikni Saqlash
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 2: DORIXONA (FARMATSEVT DORI BERISHI)      -->
        <!-- ============================================== -->
        <div id="tabPharmacy" class="tab-pane {{ request('tab') === 'pharmacy' ? 'active' : '' }}">
            <div class="content-card">
                <div class="card-header">
                    <div>
                        <h2><span>🏪</span> Dorixona Bo'limi — Shifokor Retseptlari Bo'yicha Dori Berish</h2>
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">
                            Bemor dorixonaga QR-kodni ko'rsatganda, farmatsevt shifokor yozgan dori retseptlarini ko'radi va 1-klik bilan berilgan deb tasdiqlaydi.
                        </p>
                    </div>
                    <div style="display: flex; gap: 0.5rem;">
                        <span class="badge badge-green">Faol Retseptlar: {{ $activePrescriptions->count() }}</span>
                        <span class="badge badge-blue">Berilgan: {{ $dispensedPrescriptions->count() }}</span>
                    </div>
                </div>

                @if($activePrescriptions->count() > 0)
                    <div style="margin-bottom: 1.5rem;">
                        <h3 style="font-size: 1rem; color: var(--primary-light); margin-bottom: 1rem; display: flex; align-items: center; gap: 6px;">
                            <span>⏳</span> Bemorga Berilishi Kutilayotgan Dorilar (Faol Retseptlar)
                        </h3>

                        @foreach($activePrescriptions as $rx)
                            <div class="med-item-card active-rx">
                                <div class="med-header-row">
                                    <div>
                                        <span class="med-title">{{ $rx->medication_name }}</span>
                                        <span class="med-dosage-tag">{{ $rx->dosage }}</span>
                                        <span class="badge badge-amber" style="margin-left: 0.5rem;">● Berilmagan</span>
                                    </div>
                                    <div class="rx-number">{{ $rx->prescription_number }}</div>
                                </div>

                                <div class="schedule-badge">
                                    <span>🕒</span> Ichish Tartibi: <strong>{{ $rx->frequency }}</strong>
                                </div>

                                <div class="med-meta-text">
                                    <div><strong>📅 Davomiyligi:</strong> {{ $rx->duration_days }} kunlik kurs</div>
                                    <div><strong>👨‍⚕️ Retsept bergan shifokor:</strong> {{ $rx->doctor->name ?? 'Shifokor' }} ({{ $rx->doctor->specialty ?? 'Mutaxassis' }})</div>
                                    @if($rx->instructions)
                                        <div style="margin-top: 4px; color: #fcd34d;"><strong>⚠️ Ko'rsatma:</strong> {{ $rx->instructions }}</div>
                                    @endif
                                </div>

                                <div style="margin-top: 1rem; padding-top: 0.85rem; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                                    <div style="font-size: 0.78rem; color: var(--text-dim);">
                                        Berilgan sana: {{ $rx->created_at ? $rx->created_at->format('d.m.Y H:i') : '' }}
                                    </div>

                                    <form action="{{ route('med.scan.dispense', ['qrToken' => $med->qr_token, 'prescriptionNumber' => $rx->prescription_number]) }}" method="POST" class="no-print">
                                        @csrf
                                        <input type="hidden" name="pharmacy_id" value="{{ $pharmacies->first()->id ?? 1 }}">
                                        <button type="submit" class="btn btn-green">
                                            <span>✓</span> Dorini Berish (1-Klik)
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div style="background: rgba(16, 185, 129, 0.08); border: 1px dashed rgba(16, 185, 129, 0.3); border-radius: 12px; padding: 2rem; text-align: center; margin-bottom: 1.5rem;">
                        <div style="font-size: 2rem; margin-bottom: 0.5rem;">🎉</div>
                        <h4 style="color: #6ee7b7; font-size: 1.1rem; margin-bottom: 0.25rem;">Hozirda berilmagan faol retsept yo'q</h4>
                        <p style="font-size: 0.82rem; color: var(--text-muted);">Ushbu bemorga yozilgan barcha dorilar allaqachon dorixonadan olingan yoki hali yangi retsept kiritilmagan.</p>
                    </div>
                @endif

                <!-- Dispensed Prescriptions Archive -->
                @if($dispensedPrescriptions->count() > 0)
                    <div style="margin-top: 2rem;">
                        <h3 style="font-size: 0.95rem; color: var(--text-dim); margin-bottom: 1rem; text-transform: uppercase;">
                            <span>📦</span> Avval Dorixonada Berilgan Dorilar Tarixi:
                        </h3>

                        @foreach($dispensedPrescriptions as $rx)
                            <div class="med-item-card dispensed">
                                <div class="med-header-row">
                                    <div>
                                        <span class="med-title">{{ $rx->medication_name }}</span>
                                        <span class="med-dosage-tag">{{ $rx->dosage }}</span>
                                        <span class="badge badge-green" style="margin-left: 0.5rem;">✓ Berilgan</span>
                                    </div>
                                    <div class="rx-number">{{ $rx->prescription_number }}</div>
                                </div>

                                <div class="med-meta-text">
                                    <div><strong>Tartib:</strong> {{ $rx->frequency }} | <strong>Muddati:</strong> {{ $rx->duration_days }} kun</div>
                                    <div><strong>Bergan dorixona:</strong> {{ $rx->pharmacy->name ?? '"Dori-Darmon" Dorixonasi' }}</div>
                                    <div style="color: #34d399;"><strong>Topshirilgan vaqt:</strong> {{ $rx->dispensed_at ? $rx->dispensed_at->format('d.m.Y H:i') : 'Muvaffaqiyatli berilgan' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 3: HOZIR ICHAYOTGAN DORILARI & RETSEPTLAR  -->
        <!-- ============================================== -->
        <div id="tabPrescriptions" class="tab-pane {{ request('tab') === 'prescriptions' ? 'active' : '' }}">
            <div class="grid-layout">
                <!-- Left: List of medications -->
                <div>
                    <div class="content-card">
                        <div class="card-header">
                            <div>
                                <h2><span>💊</span> Bemor Hozir Qabul Qilayotgan Dori Vositalari</h2>
                                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 3px;">
                                    Shifokorlar tomonidan tavsiya etilgan barcha dorilar, ularning dozasi va ichish vaqti
                                </p>
                            </div>
                            <span class="badge badge-blue">{{ $med->prescriptions->count() }} ta retsept</span>
                        </div>

                        @forelse($med->prescriptions as $rx)
                            <div class="med-item-card {{ $rx->status === 'active' ? 'active-rx' : 'dispensed' }}">
                                <div class="med-header-row">
                                    <div>
                                        <span class="med-title">{{ $rx->medication_name }}</span>
                                        <span class="med-dosage-tag">{{ $rx->dosage }}</span>
                                        @if($rx->status === 'active')
                                            <span class="badge badge-green" style="margin-left: 0.5rem;">● Hozir ichmoqda</span>
                                        @else
                                            <span class="badge badge-blue" style="margin-left: 0.5rem;">✓ Berilgan / Kursda</span>
                                        @endif
                                    </div>
                                    <div class="rx-number">{{ $rx->prescription_number }}</div>
                                </div>

                                <div class="schedule-badge">
                                    <span>🕒</span> Ichish Rejimi: <strong>{{ $rx->frequency }}</strong>
                                </div>

                                <div class="med-meta-text">
                                    <div><strong>⏱️ Davomiyligi:</strong> {{ $rx->duration_days }} kunlik davolash kursi</div>
                                    <div><strong>👨‍⚕️ Tayinlagan shifokor:</strong> {{ $rx->doctor->name ?? 'Dr. Navbatchi' }}</div>
                                    @if($rx->instructions)
                                        <div style="margin-top: 4px; color: #93c5fd;"><strong>ℹ️ Shifokor Ko'rsatmasi:</strong> {{ $rx->instructions }}</div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div style="text-align: center; padding: 2.5rem; color: var(--text-muted);">
                                💊 Hozircha retseptlar mavjud emas. Shifokor yangi dori yozishi mumkin.
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Right: Add new prescription form (For Doctor on laptop/PC) -->
                <div>
                    <div class="content-card no-print" style="position: sticky; top: 80px;">
                        <div class="card-header">
                            <h2><span>📝</span> Yangi Retsept Yozish</h2>
                            <span class="badge badge-green">Shifokor</span>
                        </div>
                        <p style="font-size: 0.78rem; color: var(--text-muted); margin-bottom: 1.25rem;">
                            Shifokor noutbuk yoki planshetdan bemorga dori yozadi. Kiritilgan dori darhol dorixona ro'yxatida ko'rinadi.
                        </p>

                        <form action="{{ route('med.scan.addPrescription', $med->qr_token) }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label>Tayinlovchi Shifokor</label>
                                <select name="doctor_id" class="form-control" required>
                                    @foreach($doctors as $doc)
                                        <option value="{{ $doc->id }}">{{ $doc->name }} ({{ $doc->specialty }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Dori Vositasi Nomi</label>
                                <input type="text" name="medication_name" class="form-control" placeholder="Masalan: Kardiomagnil, Azitromitsin..." required>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>Dozasi</label>
                                    <input type="text" name="dosage" class="form-control" placeholder="75 mg, 500 mg" required>
                                </div>
                                <div class="form-group">
                                    <label>Kurs Muddati (Kun)</label>
                                    <input type="number" name="duration_days" class="form-control" value="10" min="1" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Qachon va Qanday Ichishi (Jadvali)</label>
                                <input type="text" name="frequency" class="form-control" placeholder="1 kunda 2 mahal ovqatdan keyin (ertalab va kechqurun)" required>
                            </div>

                            <div class="form-group">
                                <label>Maxsus Ko'rsatmalar</label>
                                <textarea name="instructions" class="form-control" rows="2" placeholder="Ko'p suv bilan, sut mahsulotlaridan 2 soat oldin..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.75rem;">
                                💊 Retseptni Tasdiqlash
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 4: KUNLIK DORI ICHISH JADVALI (ROUTINE)    -->
        <!-- ============================================== -->
        <div id="tabSchedule" class="tab-pane {{ request('tab') === 'schedule' ? 'active' : '' }}">
            <div class="content-card">
                <div class="card-header">
                    <div>
                        <h2><span>⏰</span> Bemor Uchun Kunlik Dori Qabul Qilish Eslatmasi</h2>
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 4px;">
                            Bemor qaysi paytda qaysi dorini ichishi kerakligi bo'yicha qulay kun tartibi
                        </p>
                    </div>
                    <button type="button" class="btn btn-secondary no-print" onclick="window.print()">
                        <span>🖨️</span> Jadvalni Chop Etish
                    </button>
                </div>

                <div class="daily-routine-grid">
                    <!-- Morning -->
                    <div class="routine-slot">
                        <div class="routine-slot-header" style="color: #fbbf24;">
                            <span>🌅</span> Ertalab (08:00 - 09:00)
                        </div>
                        <ul style="font-size: 0.85rem; color: #cbd5e1; padding-left: 1.25rem; line-height: 1.7;">
                            @php $morningCount = 0; @endphp
                            @foreach($med->prescriptions as $rx)
                                @if(stripos($rx->frequency, 'ertalab') !== false || stripos($rx->frequency, 'mahal') !== false)
                                    @php $morningCount++; @endphp
                                    <li>
                                        <strong>{{ $rx->medication_name }}</strong> ({{ $rx->dosage }})
                                        <div style="font-size: 0.75rem; color: var(--text-dim);">{{ $rx->frequency }}</div>
                                    </li>
                                @endif
                            @endforeach
                            @if($morningCount === 0)
                                <li style="color: var(--text-dim);">Dori rejalashtirilmagan</li>
                            @endif
                        </ul>
                    </div>

                    <!-- Afternoon -->
                    <div class="routine-slot">
                        <div class="routine-slot-header" style="color: #38bdf8;">
                            <span>☀️</span> Tushlik (13:00 - 14:00)
                        </div>
                        <ul style="font-size: 0.85rem; color: #cbd5e1; padding-left: 1.25rem; line-height: 1.7;">
                            @php $noonCount = 0; @endphp
                            @foreach($med->prescriptions as $rx)
                                @if(stripos($rx->frequency, 'tush') !== false || stripos($rx->frequency, '3 mahal') !== false)
                                    @php $noonCount++; @endphp
                                    <li>
                                        <strong>{{ $rx->medication_name }}</strong> ({{ $rx->dosage }})
                                        <div style="font-size: 0.75rem; color: var(--text-dim);">{{ $rx->frequency }}</div>
                                    </li>
                                @endif
                            @endforeach
                            @if($noonCount === 0)
                                <li style="color: var(--text-dim);">Dori rejalashtirilmagan</li>
                            @endif
                        </ul>
                    </div>

                    <!-- Evening -->
                    <div class="routine-slot">
                        <div class="routine-slot-header" style="color: #a855f7;">
                            <span>🌙</span> Kechqurun (19:00 - 20:00)
                        </div>
                        <ul style="font-size: 0.85rem; color: #cbd5e1; padding-left: 1.25rem; line-height: 1.7;">
                            @php $eveningCount = 0; @endphp
                            @foreach($med->prescriptions as $rx)
                                @if(stripos($rx->frequency, 'kech') !== false || stripos($rx->frequency, '2 mahal') !== false || stripos($rx->frequency, '3 mahal') !== false)
                                    @php $eveningCount++; @endphp
                                    <li>
                                        <strong>{{ $rx->medication_name }}</strong> ({{ $rx->dosage }})
                                        <div style="font-size: 0.75rem; color: var(--text-dim);">{{ $rx->frequency }}</div>
                                    </li>
                                @endif
                            @endforeach
                            @if($eveningCount === 0)
                                <li style="color: var(--text-dim);">Dori rejalashtirilmagan</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 5: LABORATORIYA & DIAGNOSTIKA (ANALYSES)  -->
        <!-- ============================================== -->
        <div id="tabAnalyses" class="tab-pane {{ request('tab') === 'analyses' ? 'active' : '' }}">
            <div class="content-card">
                <div class="card-header">
                    <div class="card-title">
                        <span>🔬</span> Bemorning Laboratoriya & Instrumental Tahlillari
                    </div>
                    <span class="badge badge-blue">{{ $med->analyses->count() }} ta tahlil</span>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1rem;">
                    @forelse($med->analyses as $analysis)
                        <div class="med-item-card">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 0.6rem;">
                                <div>
                                    <h3 style="font-size: 1rem; font-weight: 700; color: #fff;">{{ $analysis->title }}</h3>
                                    <div style="font-size: 0.75rem; color: var(--text-dim); margin-top: 2px;">
                                        {{ $analysis->performed_at ? $analysis->performed_at->format('d.m.Y H:i') : '' }} | {{ $analysis->clinic?->name ?? 'Laboratoriya' }}
                                    </div>
                                </div>
                                <span class="badge {{ $analysis->status === 'normal' ? 'badge-green' : 'badge-amber' }}">
                                    {{ $analysis->status === 'normal' ? 'Me\'yorda' : 'Ogohlantirish' }}
                                </span>
                            </div>

                            @if(!empty($analysis->indicators))
                                <div style="background: rgba(0,0,0,0.25); border-radius: 8px; padding: 0.5rem 0.75rem; font-size: 0.8rem; margin: 0.6rem 0;">
                                    @foreach($analysis->indicators as $ind)
                                        <div style="display: flex; justify-content: space-between; padding: 2px 0; border-bottom: 1px solid rgba(255,255,255,0.05);">
                                            <span style="color: #cbd5e1;">{{ $ind['name'] ?? '' }}</span>
                                            <span><strong>{{ $ind['value'] ?? '' }}</strong> {{ $ind['unit'] ?? '' }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if($analysis->conclusion)
                                <div style="font-size: 0.8rem; color: #94a3b8; background: rgba(2, 132, 199, 0.08); border-left: 3px solid var(--primary-light); padding: 0.5rem 0.75rem; border-radius: 4px; margin-top: 0.5rem;">
                                    <strong>Xulosa:</strong> {{ $analysis->conclusion }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <p style="color: var(--text-muted); text-align: center; grid-column: 1 / -1; padding: 2rem;">Laboratoriya tahlillari topilmadi.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- TAB 6: EMLASHLAR & YO'LLANMALAR (VACCINATIONS)-->
        <!-- ============================================== -->
        <div id="tabVaccinations" class="tab-pane {{ request('tab') === 'vaccinations' ? 'active' : '' }}">
            <div class="content-card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <div class="card-title">
                        <span>💉</span> Milliy Emlash Tarixi & Vaksinalar
                    </div>
                    <span class="badge badge-green">{{ $med->vaccinations->count() }} ta emlash</span>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                    @forelse($med->vaccinations as $vac)
                        <div class="med-item-card">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                                <strong style="color: #fff; font-size: 0.95rem;">{{ $vac->vaccine_name }}</strong>
                                <span class="badge badge-blue">{{ $vac->dose_number }}-doza</span>
                            </div>
                            <div style="font-size: 0.78rem; color: var(--text-dim); line-height: 1.6;">
                                <div>Partiya kodi: <code style="color: #38bdf8;">{{ $vac->batch_number ?? 'Mavjud emas' }}</code></div>
                                <div>Emlangan sana: {{ $vac->administered_at ? $vac->administered_at->format('d.m.Y') : 'Noma\'lum' }}</div>
                                <div>Muassasa: {{ $vac->clinic?->name ?? 'Poliklinika' }}</div>
                                <div>Izoh: {{ $vac->notes ?? 'Asoratsiz o\'tgan' }}</div>
                            </div>
                        </div>
                    @empty
                        <p style="color: var(--text-muted); text-align: center; grid-column: 1 / -1; padding: 1.5rem;">Emlash yozuvlari mavjud emas.</p>
                    @endforelse
                </div>
            </div>

            @if($med->referrals->count() > 0)
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-title">
                            <span>📑</span> Rasmiy Tibbiy Yo'llanmalar (Referrals)
                        </div>
                        <span class="badge badge-amber">{{ $med->referrals->count() }} ta yo'llanma</span>
                    </div>

                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                        @foreach($med->referrals as $ref)
                            <div class="med-item-card">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem;">
                                    <strong style="color: #fff; font-size: 0.95rem;">{{ $ref->specialty_needed }}</strong>
                                    <span class="badge {{ $ref->status === 'completed' ? 'badge-green' : 'badge-amber' }}">
                                        {{ $ref->status === 'completed' ? 'Bajarilgan' : 'Kutilmoqda' }}
                                    </span>
                                </div>
                                <div style="font-size: 0.78rem; color: var(--text-dim); line-height: 1.6;">
                                    <div>Muassasa: <strong>{{ $ref->targetClinic?->name ?? 'Shifoxona' }}</strong></div>
                                    <div>Shoshilinchlik: {{ ucfirst($ref->urgency) }}</div>
                                    <div>Sabab: {{ $ref->reason }}</div>
                                    <div>Muddati: {{ $ref->expires_at ? $ref->expires_at->format('d.m.Y') : 'Muddatsiz' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

    </div>

    <script>
        function switchMode(tabId, btn) {
            document.querySelectorAll('.tab-pane').forEach(el => el.classList.remove('active'));
            document.querySelectorAll('.mode-btn').forEach(el => el.classList.remove('active'));
            
            const target = document.getElementById(tabId);
            if (target) {
                target.classList.add('active');
            }
            btn.classList.add('active');
            
            // update URL query parameter without refresh
            const url = new URL(window.location);
            let tabParam = 'hospital';
            if (tabId === 'tabPharmacy') tabParam = 'pharmacy';
            if (tabId === 'tabPrescriptions') tabParam = 'prescriptions';
            if (tabId === 'tabAnalyses') tabParam = 'analyses';
            if (tabId === 'tabVaccinations') tabParam = 'vaccinations';
            if (tabId === 'tabSchedule') tabParam = 'schedule';
            url.searchParams.set('tab', tabParam);
            window.history.replaceState({}, '', url);
        }
    </script>
</body>
</html>
