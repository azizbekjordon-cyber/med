<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#0284c7">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="MED E-Karta">
    <link rel="manifest" href="/manifest.json">
    <title>MED — Milliy Raqamli Med-Karta va Sog'liqni Saqlash Tizimi</title>
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
            
            --card-gradient: linear-gradient(135deg, #0369a1 0%, #0f172a 60%, #042f2e 100%);
            --card-pediatric: linear-gradient(135deg, #7c3aed 0%, #1e1b4b 60%, #4338ca 100%);
            --glass-bg: rgba(22, 32, 50, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
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
            overflow-x: hidden;
        }

        /* Top Navigation */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2.5rem;
            background: rgba(17, 24, 39, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border-color);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            text-decoration: none;
            color: var(--text-main);
        }

        .brand-logo {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--primary-light), var(--primary));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px var(--primary-glow);
            color: #fff;
            font-weight: 800;
            font-size: 1.3rem;
            letter-spacing: -0.5px;
        }

        .brand-text h1 {
            font-size: 1.25rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .brand-badge {
            background: rgba(56, 189, 248, 0.15);
            color: var(--primary-light);
            font-size: 0.68rem;
            padding: 2px 8px;
            border-radius: 6px;
            font-weight: 600;
            border: 1px solid rgba(56, 189, 248, 0.3);
            text-transform: uppercase;
        }

        .brand-text p {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.65rem 1.25rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
            text-decoration: none;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), #0284c7);
            color: #fff;
            box-shadow: 0 4px 14px var(--primary-glow);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(2, 132, 199, 0.45);
        }

        .btn-emergency {
            background: linear-gradient(135deg, #dc2626, #b91c1c);
            color: #fff;
            box-shadow: 0 4px 14px var(--medical-red-glow);
            animation: pulse-border 2.5s infinite;
        }

        .btn-emergency:hover {
            transform: translateY(-2px);
            background: #ef4444;
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-main);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        @keyframes pulse-border {
            0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.6); }
            70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
            100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
        }

        /* Container & Grid Layout */
        .main-container {
            max-width: 1440px;
            margin: 0 auto;
            padding: 2rem 2.5rem;
        }

        /* Stats Strip */
        .stats-strip {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.25rem;
            margin-bottom: 2rem;
        }

        .stat-box {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.2rem;
            transition: border-color 0.2s;
        }

        .stat-box:hover {
            border-color: rgba(56, 189, 248, 0.3);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .stat-val {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .stat-lbl {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 2px;
        }

        /* Hero Layout: Card + Quick Info */
        .hero-layout {
            display: grid;
            grid-template-columns: 460px 1fr;
            gap: 2rem;
            margin-bottom: 2.5rem;
        }

        /* Digital Med-Card Widget */
        .med-card-showcase {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .med-card-3d {
            width: 100%;
            height: 275px;
            background: var(--card-gradient);
            border-radius: 20px;
            padding: 1.8rem;
            position: relative;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.7), 0 0 30px rgba(2, 132, 199, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .med-card-3d.pediatric {
            background: var(--card-pediatric);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.7), 0 0 30px rgba(124, 58, 237, 0.2);
        }

        .med-card-3d:hover {
            transform: translateY(-4px);
            box-shadow: 0 25px 50px -15px rgba(0, 0, 0, 0.8), 0 0 40px rgba(56, 189, 248, 0.3);
        }

        .card-hologram {
            position: absolute;
            top: -40px;
            right: -40px;
            width: 160px;
            height: 160px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.12) 0%, transparent 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .card-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .card-chip-row {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .smart-chip {
            width: 44px;
            height: 34px;
            background: linear-gradient(135deg, #fbbf24 0%, #d97706 100%);
            border-radius: 6px;
            position: relative;
            border: 1px solid rgba(0,0,0,0.2);
            box-shadow: inset 0 0 4px rgba(0,0,0,0.3);
        }

        .smart-chip::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            width: 100%;
            height: 1px;
            background: rgba(0,0,0,0.3);
        }

        .nfc-icon {
            font-size: 1.2rem;
            opacity: 0.7;
        }

        .blood-badge {
            background: rgba(239, 68, 68, 0.25);
            border: 1px solid rgba(239, 68, 68, 0.5);
            color: #fca5a5;
            padding: 0.35rem 0.8rem;
            border-radius: 8px;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            gap: 0.4rem;
        }

        .card-mid {
            margin-top: 0.5rem;
        }

        .med-label {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 4px;
        }

        .med-card-number {
            font-family: 'JetBrains Mono', monospace;
            font-size: 1.45rem;
            font-weight: 700;
            letter-spacing: 3px;
            color: #ffffff;
            text-shadow: 0 2px 4px rgba(0,0,0,0.5);
        }

        .card-bottom {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .holder-name {
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            color: #f1f5f9;
        }

        .pinfl-text {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.65);
            font-family: 'JetBrains Mono', monospace;
        }

        .expiry-box {
            text-align: right;
        }

        .expiry-val {
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.85rem;
            font-weight: 600;
        }

        /* Card Selector Pills */
        .card-switcher {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 0.6rem;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .switcher-header {
            font-size: 0.75rem;
            color: var(--text-dim);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.2rem 0.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .switcher-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.65rem 0.85rem;
            border-radius: 8px;
            text-decoration: none;
            color: var(--text-main);
            background: transparent;
            transition: all 0.2s;
            border: 1px solid transparent;
        }

        .switcher-item:hover {
            background: var(--bg-card);
        }

        .switcher-item.active {
            background: rgba(2, 132, 199, 0.15);
            border-color: rgba(56, 189, 248, 0.4);
        }

        .switcher-title {
            font-weight: 600;
            font-size: 0.85rem;
        }

        .switcher-sub {
            font-size: 0.75rem;
            color: var(--text-muted);
            font-family: 'JetBrains Mono', monospace;
        }

        /* Clinical Summary Panel (Right of Card) */
        .clinical-summary {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 1.8rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .summary-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 1.25rem;
            margin-bottom: 1.25rem;
        }

        .patient-title h2 {
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .patient-tags {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.4rem;
        }

        .tag {
            font-size: 0.72rem;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 6px;
        }

        .tag-green { background: rgba(16, 185, 129, 0.15); color: #34d399; }
        .tag-blue { background: rgba(2, 132, 199, 0.15); color: #38bdf8; }
        .tag-red { background: rgba(239, 68, 68, 0.15); color: #f87171; }
        .tag-amber { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }

        .vitals-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .vital-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1rem;
            text-align: center;
        }

        .vital-title {
            font-size: 0.72rem;
            color: var(--text-dim);
            text-transform: uppercase;
        }

        .vital-value {
            font-size: 1.25rem;
            font-weight: 800;
            margin: 0.3rem 0;
            color: var(--primary-light);
        }

        .vital-state {
            font-size: 0.7rem;
            color: var(--medical-green);
            font-weight: 600;
        }

        .critical-strip {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.2rem;
            background: rgba(239, 68, 68, 0.05);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 12px;
            padding: 1rem 1.25rem;
        }

        .alert-section-title {
            font-size: 0.78rem;
            font-weight: 700;
            color: #fca5a5;
            display: flex;
            align-items: center;
            gap: 0.4rem;
            margin-bottom: 0.4rem;
        }

        .pill-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.4rem;
        }

        .pill-item {
            background: rgba(239, 68, 68, 0.15);
            color: #fecaca;
            font-size: 0.72rem;
            padding: 2px 8px;
            border-radius: 5px;
            font-weight: 500;
        }

        /* Tabs Section */
        .tabs-container {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            overflow: hidden;
        }

        .tabs-header {
            display: flex;
            background: rgba(17, 24, 39, 0.95);
            border-bottom: 1px solid var(--border-color);
            padding: 0 1.5rem;
            overflow-x: auto;
        }

        .tab-btn {
            padding: 1.1rem 1.4rem;
            background: transparent;
            border: none;
            color: var(--text-muted);
            font-size: 0.88rem;
            font-weight: 600;
            cursor: pointer;
            position: relative;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: color 0.2s;
        }

        .tab-btn:hover {
            color: var(--text-main);
        }

        .tab-btn.active {
            color: var(--primary-light);
        }

        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--primary-light);
            box-shadow: 0 -2px 8px var(--primary-glow);
        }

        .tab-count {
            background: rgba(255, 255, 255, 0.08);
            padding: 1px 7px;
            border-radius: 10px;
            font-size: 0.72rem;
        }

        .tab-content {
            padding: 2rem;
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Clinical Records List */
        .timeline-list {
            display: flex;
            flex-direction: column;
            gap: 1.25rem;
        }

        .timeline-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
            transition: border-color 0.2s;
        }

        .timeline-card:hover {
            border-color: rgba(56, 189, 248, 0.3);
        }

        .timeline-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .diagnosis-text {
            font-size: 1.1rem;
            font-weight: 700;
            color: #ffffff;
        }

        .icd-badge {
            background: rgba(2, 132, 199, 0.2);
            color: var(--primary-light);
            border: 1px solid rgba(56, 189, 248, 0.3);
            padding: 2px 8px;
            border-radius: 6px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 0.78rem;
            font-weight: 600;
        }

        .timeline-meta {
            display: flex;
            gap: 1.5rem;
            font-size: 0.8rem;
            color: var(--text-muted);
        }

        .timeline-body {
            font-size: 0.88rem;
            line-height: 1.6;
            color: #cbd5e1;
            background: rgba(10, 14, 23, 0.5);
            padding: 0.85rem 1.1rem;
            border-radius: 8px;
        }

        /* Prescriptions Table */
        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            font-size: 0.75rem;
            text-transform: uppercase;
            color: var(--text-dim);
            padding: 0.85rem 1rem;
            border-bottom: 1px solid var(--border-color);
            letter-spacing: 0.5px;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            font-size: 0.88rem;
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .rx-code {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 700;
            color: var(--primary-light);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            padding: 3px 9px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-active { background: rgba(16, 185, 129, 0.15); color: #34d399; }
        .status-dispensed { background: rgba(148, 163, 184, 0.15); color: #94a3b8; }
        .status-warning { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }

        .toast-notification {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: rgba(16, 185, 129, 0.95);
            color: #fff;
            padding: 0.85rem 1.4rem;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
            font-weight: 600;
            z-index: 99999;
            opacity: 0;
            transform: translateY(15px);
            transition: opacity 0.25s ease, transform 0.25s ease;
            pointer-events: none;
        }
        .toast-notification.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* Analyses Cards */
        .analyses-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.25rem;
        }

        .analysis-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 14px;
            padding: 1.4rem;
        }

        .analysis-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .indicators-table {
            width: 100%;
            margin-top: 0.5rem;
            font-size: 0.82rem;
        }

        .indicators-table td {
            padding: 0.5rem 0.6rem;
        }

        /* Modal Styles */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(8px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-backdrop.active {
            display: flex;
        }

        .modal-box {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            max-width: 650px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            padding: 2.2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.9);
            position: relative;
        }

        .modal-box.emergency-theme {
            border-color: rgba(239, 68, 68, 0.4);
            box-shadow: 0 0 50px rgba(239, 68, 68, 0.3);
        }

        .modal-close {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            background: transparent;
            border: none;
            color: var(--text-dim);
            font-size: 1.5rem;
            cursor: pointer;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.4rem;
        }

        .form-control {
            width: 100%;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            color: #fff;
            font-size: 0.88rem;
            outline: none;
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: var(--primary-light);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        /* Live API Console Tab */
        .api-console {
            background: #0d131f;
            border-radius: 12px;
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            font-family: 'JetBrains Mono', monospace;
        }

        .api-endpoint-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .method-badge {
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 700;
        }

        .method-get { background: #0284c7; color: #fff; }
        .method-post { background: #10b981; color: #fff; }
        .method-put { background: #f59e0b; color: #fff; }

        /* Toast Alert */
        .toast {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: #10b981;
            color: #fff;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            box-shadow: 0 10px 25px rgba(16, 185, 129, 0.4);
            z-index: 2000;
            animation: slide-up 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .toast.toast-warning {
            background: #f59e0b;
            box-shadow: 0 10px 25px rgba(245, 158, 11, 0.4);
        }

        .toast.toast-error {
            background: #ef4444;
            box-shadow: 0 10px 25px rgba(239, 68, 68, 0.4);
        }

        .toast.toast-info {
            background: #0284c7;
            box-shadow: 0 10px 25px rgba(2, 132, 199, 0.4);
        }

        @keyframes slide-up {
            from { transform: translateY(100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Mobile-First Responsive Breakpoints */
        @media (max-width: 1024px) {
            .hero-layout { grid-template-columns: 1fr; gap: 1.5rem; }
            .stats-strip { grid-template-columns: 1fr 1fr; }
            .analyses-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .main-container { padding: 1rem 0.85rem 5rem 0.85rem; }
            .navbar {
                flex-direction: column;
                align-items: stretch;
                gap: 0.75rem;
                padding: 0.85rem 1rem;
            }
            .brand { justify-content: flex-start; }
            .brand-text h1 { font-size: 1.1rem; }
            .brand-text p { font-size: 0.7rem; }
            .nav-actions {
                display: flex;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                padding-bottom: 0.4rem;
                gap: 0.5rem;
                scrollbar-width: none;
            }
            .nav-actions::-webkit-scrollbar { display: none; }
            .nav-actions .btn {
                flex-shrink: 0;
                font-size: 0.78rem;
                padding: 0.55rem 0.85rem;
            }
            .user-profile-badge {
                flex-shrink: 0;
                margin-left: 0;
            }
            .stats-strip {
                grid-template-columns: 1fr 1fr;
                gap: 0.65rem;
                margin-bottom: 1.25rem;
            }
            .stat-box { padding: 0.85rem 1rem; gap: 0.75rem; }
            .stat-val { font-size: 1.3rem; }
            .stat-lbl { font-size: 0.72rem; }
            .stat-icon { width: 38px; height: 38px; font-size: 1.2rem; }

            .summary-header {
                flex-direction: column;
                gap: 1rem;
                align-items: stretch;
            }
            .summary-header > div:last-child {
                justify-content: flex-start;
            }

            .vitals-grid {
                grid-template-columns: 1fr 1fr;
                gap: 0.6rem;
                margin-bottom: 1rem;
            }
            .vital-card { padding: 0.75rem 0.5rem; }
            .vital-value { font-size: 1.1rem; }

            .critical-strip {
                grid-template-columns: 1fr;
                gap: 0.85rem;
                padding: 0.85rem;
            }

            .tabs-header {
                overflow-x: auto;
                white-space: nowrap;
                -webkit-overflow-scrolling: touch;
                scrollbar-width: none;
                padding: 0.4rem;
            }
            .tabs-header::-webkit-scrollbar { display: none; }
            .tab-btn {
                flex-shrink: 0;
                padding: 0.65rem 0.9rem;
                font-size: 0.78rem;
            }

            .modal-backdrop {
                padding: 0.5rem;
                align-items: flex-end;
            }
            .modal-box {
                max-width: 100%;
                width: 100%;
                border-radius: 20px 20px 10px 10px;
                max-height: 88vh;
                padding: 1.5rem 1.1rem;
            }
            .modal-close {
                top: 1rem;
                right: 1rem;
            }

            .form-row {
                grid-template-columns: 1fr !important;
                gap: 0 !important;
            }
        }

        @media (max-width: 480px) {
            .med-card-3d {
                height: 220px;
                padding: 1.25rem;
            }
            .med-card-number {
                font-size: 1.15rem;
                letter-spacing: 2px;
            }
            .holder-name {
                font-size: 0.95rem;
            }
            .qr-action-card {
                padding: 0.9rem;
            }
            .qr-thumb-wrapper {
                width: 65px;
                height: 65px;
            }
        }

        /* Floating AI Action Button (Mobile & Desktop) */
        .ai-fab-btn {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            background: linear-gradient(135deg, #06b6d4, #0284c7);
            color: #fff;
            border: 1px solid rgba(56, 189, 248, 0.5);
            border-radius: 50px;
            padding: 0.85rem 1.4rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            font-size: 0.9rem;
            font-weight: 700;
            box-shadow: 0 10px 30px rgba(6, 182, 212, 0.45);
            cursor: pointer;
            z-index: 990;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
            animation: pulse-fab 2.5s infinite;
        }

        .ai-fab-btn:hover {
            transform: translateY(-3px) scale(1.03);
            box-shadow: 0 15px 35px rgba(6, 182, 212, 0.6);
        }

        @keyframes pulse-fab {
            0% { box-shadow: 0 0 0 0 rgba(6, 182, 212, 0.5); }
            70% { box-shadow: 0 0 0 12px rgba(6, 182, 212, 0); }
            100% { box-shadow: 0 0 0 0 rgba(6, 182, 212, 0); }
        }

        @media (max-width: 768px) {
            .ai-fab-btn {
                bottom: 1rem;
                right: 1rem;
                padding: 0.7rem 1.1rem;
                font-size: 0.82rem;
            }
        }

        /* Pill Tracker Styles */
        .pill-tracker-card {
            background: var(--bg-surface);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .pill-progress-bar-bg {
            height: 10px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            overflow: hidden;
            margin: 0.8rem 0 1.25rem 0;
        }

        .pill-progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #10b981, #06b6d4);
            border-radius: 10px;
            transition: width 0.4s ease;
        }

        .pill-slot-group {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin-bottom: 0.85rem;
        }

        .pill-slot-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 0.75rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 0.85rem;
            font-weight: 700;
        }

        .pill-item-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.65rem 0.85rem;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
        }

        .pill-item-row.taken {
            background: rgba(16, 185, 129, 0.12);
            border-color: rgba(16, 185, 129, 0.3);
        }

        .pill-check-btn {
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-muted);
            border-radius: 20px;
            padding: 5px 14px;
            font-size: 0.75rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .pill-check-btn.active {
            background: #10b981;
            border-color: #34d399;
            color: #fff;
            box-shadow: 0 0 12px rgba(16, 185, 129, 0.4);
        }

        /* AI Chat / Symptom Chips */
        .ai-chip {
            background: rgba(6, 182, 212, 0.12);
            border: 1px solid rgba(6, 182, 212, 0.3);
            color: #38bdf8;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin: 3px;
        }

        .ai-chip:hover {
            background: rgba(6, 182, 212, 0.25);
            border-color: #38bdf8;
            transform: translateY(-1px);
        }

        /* QR Action Card & Pass Styles */
        .qr-action-card {
            background: var(--bg-surface);
            border: 1px solid rgba(56, 189, 248, 0.25);
            border-radius: 16px;
            padding: 1.15rem;
            margin-top: 1rem;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.4);
            position: relative;
            overflow: hidden;
        }

        .qr-thumb-wrapper {
            width: 80px;
            height: 80px;
            background: #fff;
            border-radius: 10px;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 0 15px rgba(56, 189, 248, 0.25);
            flex-shrink: 0;
            transition: transform 0.2s;
        }

        .qr-thumb-wrapper:hover {
            transform: scale(1.05);
        }

        .qr-thumb-wrapper img {
            width: 100%;
            height: 100%;
            display: block;
            object-fit: contain;
            cursor: pointer;
        }

        .qr-modal-animated {
            animation: qrModalPop 0.28s cubic-bezier(0.16, 1, 0.3, 1);
        }

        @keyframes qrModalPop {
            from {
                opacity: 0;
                transform: scale(0.82);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        /* Appointment & Time Slot Styles */
        .slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(88px, 1fr));
            gap: 0.6rem;
            margin-top: 0.75rem;
        }

        .slot-btn {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0.6rem 0.3rem;
            color: #fff;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.82rem;
            font-weight: 600;
        }

        .slot-btn.available {
            border-color: rgba(16, 185, 129, 0.4);
            color: #34d399;
            background: rgba(16, 185, 129, 0.05);
        }

        .slot-btn.available:hover {
            background: rgba(16, 185, 129, 0.2);
            border-color: #10b981;
            transform: translateY(-2px);
        }

        .slot-btn.selected {
            background: linear-gradient(135deg, #10b981, #059669) !important;
            border-color: #34d399 !important;
            color: #fff !important;
            box-shadow: 0 0 15px rgba(16, 185, 129, 0.4);
            transform: scale(1.04);
        }

        .slot-btn.occupied {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.08);
            color: var(--text-dim);
            cursor: not-allowed;
            opacity: 0.6;
        }

        /* E-Talon Ticket Styles */
        .e-ticket-card {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            border: 2px dashed rgba(56, 189, 248, 0.4);
            border-radius: 18px;
            padding: 1.5rem;
            position: relative;
            margin-bottom: 1.25rem;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.4);
        }

        .ticket-queue-badge {
            width: 72px;
            height: 72px;
            border-radius: 14px;
            background: linear-gradient(135deg, #0284c7, #0369a1);
            color: #fff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 20px var(--primary-glow);
            font-weight: 800;
        }

        .ticket-queue-num {
            font-size: 1.6rem;
            line-height: 1;
        }

        .ticket-queue-sub {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.85;
        }
    </style>
<body>

    <!-- Top Navigation Bar -->
    <header class="navbar">
        <a href="{{ route('med.portal') }}" class="brand">
            <div class="brand-logo">⚕</div>
            <div class="brand-text">
                <h1>MED <span class="brand-badge">Sog'liqni Saqlash E-Tizimi</span></h1>
                <p>Elektron Tibbiy Karta & Bemorlar Ma'lumotlar Bazasi</p>
            </div>
        </a>
        <div class="nav-actions">
            <!-- AI Consultant Trigger -->
            <button type="button" class="btn btn-primary" onclick="openModal('aiConsultantModal')" style="background: linear-gradient(135deg, #06b6d4, #0284c7); box-shadow: 0 4px 14px rgba(6, 182, 212, 0.35);">
                <span>🤖</span> AI Maslahatchi
            </button>
            <!-- Medical Passport PDF Trigger -->
            <button type="button" class="btn btn-secondary" onclick="openModal('medicalPassportModal')" style="border-color: rgba(56, 189, 248, 0.4); color: var(--primary-light);">
                <span>📄</span> Med-Pasport (PDF)
            </button>
            <!-- Online Doctor Queue Booking Trigger -->
            <button type="button" class="btn btn-primary" onclick="openAppointmentModal()" style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);">
                <span>🎫</span> Online Navbat
            </button>
            <!-- Kasalxona & Dorixona QR Scan Portal Trigger -->
            <a href="{{ route('med.scan', $activeMed?->qr_token ?? 'default') }}" target="_blank" class="btn btn-secondary" style="border-color: rgba(56, 189, 248, 0.4); color: var(--primary-light);">
                <span>📱</span> Kasalxona & Dorixona QR Pass
            </a>
            <!-- 103 Emergency Triage Modal Trigger -->
            <button type="button" class="btn btn-emergency" onclick="openEmergencyModal()">
                <span>🚨</span> 103 Triage (QR)
            </button>
            <!-- PWA Install Trigger -->
            <button type="button" class="btn btn-secondary" id="pwaInstallBtn" onclick="installPwa()" style="display: none; border-color: rgba(16, 185, 129, 0.4); color: #34d399;">
                <span>📲</span> O'rnatish
            </button>
            @if(!Auth::check() || !Auth::user()->isPatient())
            <button type="button" class="btn btn-primary" onclick="openModal('newCardModal')">
                <span>+</span> Yangi Med-Karta
            </button>
            @endif

            @auth
                <!-- Logged-in Admin / Specialist Profile Widget -->
                <div class="user-profile-badge" style="display: flex; align-items: center; gap: 0.75rem; background: rgba(15, 23, 42, 0.85); border: 1px solid rgba(56, 189, 248, 0.3); border-radius: 12px; padding: 0.35rem 0.85rem; margin-left: 0.5rem;">
                    <div style="width: 34px; height: 34px; border-radius: 8px; background: linear-gradient(135deg, #0284c7, #0369a1); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.82rem; color: #fff; box-shadow: 0 0 12px rgba(56, 189, 248, 0.35);">
                        {{ strtoupper(substr(Auth::user()->name ?? 'AD', 0, 2)) }}
                    </div>
                    <div style="display: flex; flex-direction: column; text-align: left;">
                        <div style="font-size: 0.82rem; font-weight: 700; color: #fff; line-height: 1.2;">
                            {{ Auth::user()->name ?? 'Administrator' }}
                        </div>
                        <div style="font-size: 0.68rem; color: #38bdf8; font-weight: 600;">
                            @if(Auth::user()->isAdmin())
                                🛡️ Tizim Administratori
                            @elseif(Auth::user()->isDoctor())
                                🩺 Shifokor
                            @elseif(Auth::user()->isEmergency103())
                                🚨 103 Shifokori
                            @elseif(Auth::user()->isPharmacist())
                                💊 Provizor
                            @else
                                👤 Bemor
                            @endif
                        </div>
                    </div>
                    <!-- Logout Form & Button -->
                    <form action="{{ route('logout') }}" method="POST" style="margin: 0; display: inline;">
                        @csrf
                        <button type="submit" class="btn-logout" title="Tizimdan chiqish" style="background: rgba(239, 68, 68, 0.15); border: 1px solid rgba(239, 68, 68, 0.35); color: #fca5a5; border-radius: 8px; padding: 0.45rem 0.75rem; cursor: pointer; display: flex; align-items: center; gap: 0.4rem; font-size: 0.75rem; font-weight: 600; transition: all 0.2s;">
                            <span>🚪</span> Chiqish
                        </button>
                    </form>
                </div>
            @else
                <a href="{{ route('login') }}" class="btn btn-secondary" style="border-color: rgba(56, 189, 248, 0.4); color: var(--primary-light); margin-left: 0.5rem;">
                    <span>🔑</span> Admin Kirish
                </a>
            @endauth
        </div>
    </header>

    <main class="main-container">

        @if(session('success'))
            <div class="toast" id="sessionToast">
                <span>✓</span> {{ session('success') }}
            </div>
            <script>setTimeout(() => { document.getElementById('sessionToast')?.remove(); }, 4000);</script>
        @endif

        @if(session('warning'))
            <div class="toast toast-warning" id="sessionToastWarning">
                <span>⚠️</span> {{ session('warning') }}
            </div>
            <script>setTimeout(() => { document.getElementById('sessionToastWarning')?.remove(); }, 5000);</script>
        @endif

        @if(session('error'))
            <div class="toast toast-error" id="sessionToastError">
                <span>🛑</span> {{ session('error') }}
            </div>
            <script>setTimeout(() => { document.getElementById('sessionToastError')?.remove(); }, 5000);</script>
        @endif

        @if(session('status'))
            <div class="toast toast-info" id="sessionToastStatus">
                <span>ℹ️</span> {{ session('status') }}
            </div>
            <script>setTimeout(() => { document.getElementById('sessionToastStatus')?.remove(); }, 4000);</script>
        @endif

        <!-- Stats Metric Bar -->
        <section class="stats-strip">
            <div class="stat-box">
                <div class="stat-icon" style="background: rgba(2, 132, 199, 0.15); color: #38bdf8;">🪪</div>
                <div>
                    <div class="stat-val">{{ $stats['total_meds'] }}</div>
                    <div class="stat-lbl">Faol Med-Kartalar</div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #34d399;">🩺</div>
                <div>
                    <div class="stat-val">{{ $stats['active_records'] }}</div>
                    <div class="stat-lbl">Tibbiy Qabullar & Anamnez</div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon" style="background: rgba(245, 158, 11, 0.15); color: #fbbf24;">💊</div>
                <div>
                    <div class="stat-val">{{ $stats['active_prescriptions'] }}</div>
                    <div class="stat-lbl">Faol Elektron Retseptlar</div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon" style="background: rgba(168, 85, 247, 0.15); color: #c084fc;">🔬</div>
                <div>
                    <div class="stat-val">{{ $stats['completed_analyses'] }}</div>
                    <div class="stat-lbl">Laboratoriya Tahlillari</div>
                </div>
            </div>
            <div class="stat-box">
                <div class="stat-icon" style="background: rgba(16, 185, 129, 0.15); color: #34d399;">🎫</div>
                <div>
                    <div class="stat-val">{{ $stats['total_appointments'] ?? 0 }}</div>
                    <div class="stat-lbl">Kutilayotgan E-Navbatlar</div>
                </div>
            </div>
        </section>

        <!-- Hero Section: Digital Med Card & Clinical Summary -->
        <section class="hero-layout">
            
            <!-- Left Column: Realistic 3D Med-Card & Selector -->
            <div class="med-card-showcase">
                
                <div class="med-card-3d {{ $activeMed->card_type === 'pediatric' ? 'pediatric' : '' }}">
                    <div class="card-hologram"></div>
                    
                    <div class="card-top">
                        <div class="card-chip-row">
                            <div class="smart-chip"></div>
                            <span class="nfc-icon">📶</span>
                            <span style="font-size: 0.7rem; font-weight: 700; letter-spacing: 1px; opacity: 0.8;">MED-ID SMART</span>
                        </div>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <button type="button" onclick="openQrModal()" title="QR kodni kattalashtirish" style="background: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.3); border-radius: 8px; color: #fff; padding: 3px 8px; font-size: 0.72rem; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 4px; transition: all 0.2s;">
                                <span>📱</span> QR
                            </button>
                            <div class="blood-badge">
                                <span>🩸</span> {{ $activeMed->blood_group }} ({{ $activeMed->rhesus_factor === 'positive' ? 'Rh+' : 'Rh-' }})
                            </div>
                        </div>
                    </div>

                    <div class="card-mid">
                        <div class="med-label">TIBBIY KARTA RAQAMI</div>
                        <div class="med-card-number">{{ $activeMed->med_number }}</div>
                    </div>

                    <div class="card-bottom">
                        <div>
                            <div class="med-label">FUQARO / BEMOR</div>
                            <div class="holder-name">{{ strtoupper($activeMed->user?->name ?? 'BEMOR') }}</div>
                            <div class="pinfl-text">JSHSHIR: {{ $activeMed->user?->pinfl ?? '32509820010025' }}</div>
                        </div>
                        <div class="expiry-box">
                            <div class="med-label">AMAL QILISHI</div>
                            <div class="expiry-val">{{ $activeMed->expires_at ? $activeMed->expires_at->format('m/y') : '01/34' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Interactive QR-Code Pass Box -->
                <div class="qr-action-card">
                    <div style="display: flex; gap: 1rem; align-items: center;">
                        <div class="qr-thumb-wrapper" onclick="openQrModal()" title="Kattalashtirish uchun bosing" style="cursor: pointer;">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode(route('med.scan', $activeMed?->qr_token ?? 'default')) }}" alt="QR Code" id="cardQrThumb" onclick="openQrModal()" style="cursor: pointer;">
                        </div>
                        <div style="flex: 1;">
                            <div style="font-size: 0.72rem; color: var(--primary-light); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                                RASMIY QR MED-PASS
                            </div>
                            <h4 style="font-size: 0.95rem; font-weight: 700; color: #fff; margin: 2px 0 4px 0; cursor: pointer;" onclick="openQrModal()" title="Kattalashtirish uchun bosing">
                                Kasalxona & Dorixona QR-Kodi 🔍
                            </h4>
                            <p style="font-size: 0.75rem; color: var(--text-muted); line-height: 1.35; margin-bottom: 8px;">
                                Kasalxonada ko'rsatilsa — shifokor ko'riklari va dorilar; Dorixonada — retseptlar chiqadi va dori beriladi.
                            </p>
                            <div style="display: flex; gap: 6px; flex-wrap: wrap;">
                                <button type="button" class="btn btn-primary" onclick="openQrModal()" style="padding: 6px 14px; font-size: 0.8rem; font-weight: 700; box-shadow: 0 4px 15px var(--primary-glow);">
                                    <span>🔍</span> QR Kattalashtirish
                                </button>
                                <a href="{{ route('med.scan', $activeMed?->qr_token ?? 'default') }}" target="_blank" class="btn btn-secondary" style="padding: 6px 10px; font-size: 0.78rem;">
                                    <span>📱</span> Skaner Sahifasi
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Patient Card Switcher -->
                <div class="card-switcher">
                    <div class="switcher-header">
                        <span>Tizimdagi Med-Kartalar</span>
                        <span>Tanlangan: {{ $activeMed->med_number }}</span>
                    </div>
                    @foreach($allMeds as $m)
                        <a href="{{ route('med.portal', ['med' => $m->med_number]) }}" class="switcher-item {{ $m->id === $activeMed->id ? 'active' : '' }}">
                            <div>
                                <div class="switcher-title">{{ $m->user->name }}</div>
                                <div class="switcher-sub">{{ $m->med_number }}</div>
                            </div>
                            <span class="blood-badge" style="padding: 2px 6px; font-size: 0.72rem;">{{ $m->blood_group }}</span>
                        </a>
                    @endforeach
                </div>

            </div>

            <!-- Right Column: Clinical Profile & Vitals Overview -->
            <div class="clinical-summary">
                <div>
                    <div class="summary-header">
                        <div class="patient-title">
                            <h2>{{ $activeMed->user?->name ?? 'Bemor' }}</h2>
                            <div class="patient-tags">
                                <span class="tag tag-blue">ID: {{ $activeMed->med_number }}</span>
                                @if($activeMed->status === 'active')
                                    <span class="tag tag-green">Holati: Faol (Tasdiqlangan)</span>
                                @elseif($activeMed->status === 'locked')
                                    <span class="tag" style="background: rgba(239, 68, 68, 0.2); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.4);">Holati: Bloklangan</span>
                                @else
                                    <span class="tag tag-amber">Holati: {{ ucfirst($activeMed->status) }}</span>
                                @endif
                                <span class="tag tag-amber">Turi: {{ ucfirst($activeMed->card_type) }}</span>
                                @if($activeMed->organ_donor)
                                    <span class="tag tag-green">Organ Donori: Rozilik berilgan</span>
                                @endif
                            </div>
                        </div>
                        <div style="text-align: right; display: flex; gap: 0.5rem; justify-content: flex-end; flex-wrap: wrap; align-items: center;">
                            @if(!Auth::check() || !Auth::user()->isPatient())
                            <form action="{{ route('med.toggleStatus', $activeMed->med_number) }}" method="POST" style="display:inline;" onsubmit="return confirm('Karta holatini o\'zgartirishni tasdiqlaysizmi?');">
                                @csrf
                                <button type="submit" class="btn btn-secondary" style="{{ $activeMed->status === 'active' ? 'border-color: rgba(239, 68, 68, 0.4); color: #f87171;' : 'border-color: rgba(16, 185, 129, 0.4); color: #34d399;' }}">
                                    <span>{{ $activeMed->status === 'active' ? '🔒 Bloklash' : '🔓 Faollashtirish' }}</span>
                                </button>
                            </form>
                            @endif
                            <button type="button" class="btn btn-primary" onclick="openAppointmentModal()" style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);">
                                <span>🎫</span> Navbat Olish
                            </button>
                            @if(!Auth::check() || !Auth::user()->isPatient())
                            <button type="button" class="btn btn-secondary" onclick="openModal('addRecordModal')">
                                <span>🩺</span> Ko'rik Qo'shish
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="openModal('addPrescriptionModal')">
                                <span>💊</span> Retsept Yozish
                            </button>
                            @endif
                        </div>
                    </div>

                    <!-- Vitals Summary (Arterial bosim, Puls, Harorat, SpO2) -->
                    @php
                        $latestRecord = $activeMed->records->first();
                        $vitals = $latestRecord->vitals ?? [
                            'blood_pressure' => '120/80 mmHg',
                            'heart_rate' => '76 bpm',
                            'temperature' => '36.6 °C',
                            'spo2' => '98%'
                        ];
                    @endphp
                    <div class="vitals-grid">
                        <div class="vital-card">
                            <div class="vital-title">Qon Bosimi (BP)</div>
                            <div class="vital-value">{{ $vitals['blood_pressure'] ?? '120/80 mmHg' }}</div>
                            <div class="vital-state">So'nggi o'lchov</div>
                        </div>
                        <div class="vital-card">
                            <div class="vital-title">Puls (HR)</div>
                            <div class="vital-value">{{ $vitals['heart_rate'] ?? '76 bpm' }}</div>
                            <div class="vital-state">Normada</div>
                        </div>
                        <div class="vital-card">
                            <div class="vital-title">Tana Harorati</div>
                            <div class="vital-value">{{ $vitals['temperature'] ?? '36.6 °C' }}</div>
                            <div class="vital-state">Optimal</div>
                        </div>
                        <div class="vital-card">
                            <div class="vital-title">Kislorod (SpO2)</div>
                            <div class="vital-value">{{ $vitals['spo2'] ?? '98%' }}</div>
                            <div class="vital-state">Yaxshi</div>
                        </div>
                    </div>

                    <!-- Critical Life-Saving Parameters (Allergiyalar & Surunkali kasalliklar) -->
                    <div class="critical-strip">
                        <div>
                            <div class="alert-section-title">
                                <span>⚠️</span> O'ta Xavfli Allergiyalar
                            </div>
                            <div class="pill-list">
                                @if(!empty($activeMed->allergies) && count($activeMed->allergies) > 0)
                                    @foreach($activeMed->allergies as $allergy)
                                        <span class="pill-item">{{ $allergy }}</span>
                                    @endforeach
                                @else
                                    <span style="font-size: 0.75rem; color: var(--text-dim);">Allergik anamnez toza</span>
                                @endif
                            </div>
                        </div>
                        <div>
                            <div class="alert-section-title" style="color: #fed7aa;">
                                <span>📋</span> Surunkali Kasalliklar
                            </div>
                            <div class="pill-list">
                                @if(!empty($activeMed->chronic_diseases) && count($activeMed->chronic_diseases) > 0)
                                    @foreach($activeMed->chronic_diseases as $chronic)
                                        <span class="pill-item" style="background: rgba(245, 158, 11, 0.15); color: #fed7aa;">{{ $chronic }}</span>
                                    @endforeach
                                @else
                                    <span style="font-size: 0.75rem; color: var(--text-dim);">Surunkali patologiya yo'q</span>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Emergency Contact Footer -->
                <div style="margin-top: 1.25rem; display: flex; justify-content: space-between; align-items: center; font-size: 0.82rem; color: var(--text-muted); border-top: 1px solid var(--border-color); padding-top: 1rem;">
                    <div>
                        <strong>Favqulodda Aloqa:</strong> {{ $activeMed->emergency_contact_name ?? 'Ko\'rsatilmagan' }} 
                        ({{ $activeMed->emergency_contact_relation ?? 'Yaqini' }}) — 
                        <span style="color: var(--primary-light); font-family: 'JetBrains Mono', monospace;">{{ $activeMed->emergency_contact_phone ?? 'Noma\'lum' }}</span>
                    </div>
                    <div>
                        <strong>Tibbiy Sug'urta:</strong> {{ $activeMed->insurance_company ?? 'Davlat kafolati' }}
                    </div>
                </div>

            </div>

        </section>

        <!-- Detailed Medical Tabs Section -->
        <section class="tabs-container">
            
            <div class="tabs-header">
                <button type="button" class="tab-btn active" onclick="switchTab(event, 'tabRecords')">
                    <span>🩺</span> Ko'riklar & Tashxislar (EHR) <span class="tab-count">{{ $activeMed->records->count() }}</span>
                </button>
                <button type="button" class="tab-btn" onclick="switchTab(event, 'tabPrescriptions')">
                    <span>💊</span> Elektron Retseptlar <span class="tab-count">{{ $activeMed->prescriptions->count() }}</span>
                </button>
                <button type="button" class="tab-btn" onclick="switchTab(event, 'tabAnalyses')">
                    <span>🔬</span> Laboratoriya & Diagnostika <span class="tab-count">{{ $activeMed->analyses->count() }}</span>
                </button>
                <button type="button" class="tab-btn" onclick="switchTab(event, 'tabVaccinations')">
                    <span>💉</span> Emlash Jurnali <span class="tab-count">{{ $activeMed->vaccinations->count() }}</span>
                </button>
                <button type="button" class="tab-btn" onclick="switchTab(event, 'tabReferrals')">
                    <span>📑</span> Yo'llanmalar <span class="tab-count">{{ $activeMed->referrals->count() }}</span>
                </button>
                <button type="button" class="tab-btn" onclick="switchTab(event, 'tabAudit')">
                    <span>🛡️</span> Xavfsizlik & Kirish Jurnali <span class="tab-count">{{ $activeMed->accessLogs->count() }}</span>
                </button>
                <button type="button" class="tab-btn" onclick="switchTab(event, 'tabAppointments')">
                    <span>🎫</span> E-Navbatlarim <span class="tab-count">{{ $activeMed->appointments->where('status', '!=', 'cancelled')->count() }}</span>
                </button>
                <button type="button" class="tab-btn" onclick="switchTab(event, 'tabPillTracker')">
                    <span>⏰</span> Dori Jadvali (Tracker) <span class="tab-count" style="background: rgba(16, 185, 129, 0.2); color: #34d399;">Bugun</span>
                </button>
            </div>

            <!-- Tab 1: Medical Records (Kasallik tarixi) -->
            <div id="tabRecords" class="tab-content active">
                <div class="timeline-list">
                    @forelse($activeMed->records as $record)
                        <div class="timeline-card">
                            <div class="timeline-top">
                                <div>
                                    <div class="diagnosis-text">{{ $record->diagnosis }}</div>
                                    <div class="timeline-meta" style="margin-top: 4px;">
                                        <span>👨‍⚕️ Shifokor: {{ $record->doctor->name ?? 'Dr. Navbatchi' }} ({{ $record->doctor->specialty ?? 'Terapevt' }})</span>
                                        <span>🏥 Muassasa: {{ $record->clinic->name ?? 'Markaziy Shifoxona' }}</span>
                                        <span>📅 Sana: {{ $record->visit_date ? $record->visit_date->format('d.m.Y H:i') : '' }}</span>
                                    </div>
                                </div>
                                @if($record->icd10_code)
                                    <span class="icd-badge">ICD-10: {{ $record->icd10_code }}</span>
                                @endif
                            </div>

                            @if($record->symptoms)
                                <div>
                                    <strong style="font-size: 0.78rem; color: var(--text-dim); text-transform: uppercase;">Shikoyatlar:</strong>
                                    <div class="timeline-body">{{ $record->symptoms }}</div>
                                </div>
                            @endif

                            @if($record->treatment_plan)
                                <div>
                                    <strong style="font-size: 0.78rem; color: var(--text-dim); text-transform: uppercase;">Tavsiya va Davolash Rejasi:</strong>
                                    <div class="timeline-body" style="border-left: 3px solid var(--primary-light);">{{ $record->treatment_plan }}</div>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p style="color: var(--text-muted); text-align: center; padding: 2rem;">Ushbu med-karta uchun hali ko'rik qaydlari kiritilmagan.</p>
                    @endforelse
                </div>
            </div>

            <!-- Tab 2: E-Prescriptions (Retseptlar) -->
            <div id="tabPrescriptions" class="tab-content">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Retsept Kodi</th>
                                <th>Dori Vositasi</th>
                                <th>Dozasi & Qabul Tartibi</th>
                                <th>Davomiyligi</th>
                                <th>Yozgan Shifokor</th>
                                <th>Holati</th>
                                <th>Amal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeMed->prescriptions as $rx)
                                <tr>
                                    <td class="rx-code">{{ $rx->prescription_number }}</td>
                                    <td><strong>{{ $rx->medication_name }}</strong></td>
                                    <td>{{ $rx->dosage }} — {{ $rx->frequency }}</td>
                                    <td>{{ $rx->duration_days }} kun</td>
                                    <td>{{ $rx->doctor->name ?? 'Shifokor' }}</td>
                                    <td>
                                        @if($rx->status === 'active')
                                            <span class="status-badge status-active">● Faol (Berilmagan)</span>
                                        @else
                                            <span class="status-badge status-dispensed">✓ Dorixonada berilgan</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!Auth::check() || !Auth::user()->isPatient())
                                            @if($rx->status === 'active')
                                                <button type="button" class="btn btn-secondary" style="padding: 4px 10px; font-size: 0.75rem;" onclick="dispenseRx('{{ $rx->prescription_number }}', this)">
                                                    Dori berish (1-klik)
                                                </button>
                                            @else
                                                <span style="font-size: 0.75rem; color: var(--text-dim);">{{ $rx->dispensed_at ? $rx->dispensed_at->format('d.m.Y') : 'Berilgan' }}</span>
                                            @endif
                                        @else
                                            @if($rx->status === 'active')
                                                <span class="status-badge status-active" style="font-size: 0.72rem;">Dorixonadan olinadi</span>
                                            @else
                                                <span style="font-size: 0.75rem; color: var(--text-dim);">{{ $rx->dispensed_at ? $rx->dispensed_at->format('d.m.Y') : 'Qabul qilingan' }}</span>
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; color: var(--text-muted);">Faol retseptlar mavjud emas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 3: Analyses (Laboratoriya) -->
            <div id="tabAnalyses" class="tab-content">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem;">
                    <div>
                        <h3 style="font-size: 1.1rem; color: #fff;">Laboratoriya va Diagnostik Tekshiruvlar</h3>
                        <p style="font-size: 0.78rem; color: var(--text-muted);">Qon tahlillari, biokimyo, instrumental tekshiruvlar va klinik xulosalar</p>
                    </div>
                    @if(!Auth::check() || !Auth::user()->isPatient())
                    <button type="button" class="btn btn-primary" onclick="openModal('addAnalysisModal')" style="padding: 0.55rem 1rem; font-size: 0.85rem;">
                        <span>🔬</span> Yangi Tahlil Qo'shish
                    </button>
                    @endif
                </div>

                <div class="analyses-grid">
                    @forelse($activeMed->analyses as $analysis)
                        <div class="analysis-card">
                            <div class="analysis-header">
                                <div>
                                    <h3 style="font-size: 1.05rem; font-weight: 700;">{{ $analysis->title }}</h3>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 3px;">
                                        O'tkazilgan sana: {{ $analysis->performed_at ? $analysis->performed_at->format('d.m.Y H:i') : '' }}
                                    </div>
                                </div>
                                <span class="status-badge {{ $analysis->status === 'normal' ? 'status-active' : 'status-warning' }}">
                                    {{ $analysis->status === 'normal' ? 'Me\'yorda' : 'Ogohlantirish' }}
                                </span>
                            </div>

                            @if(!empty($analysis->indicators))
                                <table class="indicators-table">
                                    <thead>
                                        <tr>
                                            <th>Ko'rsatkich</th>
                                            <th>Natija</th>
                                            <th>Me'yor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($analysis->indicators as $ind)
                                            <tr>
                                                <td>{{ $ind['name'] ?? '' }}</td>
                                                <td><strong>{{ $ind['value'] ?? '' }}</strong> {{ $ind['unit'] ?? '' }}</td>
                                                <td style="color: var(--text-dim);">{{ $ind['reference'] ?? '' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif

                            @if($analysis->conclusion)
                                <div style="margin-top: 1rem; font-size: 0.82rem; color: #cbd5e1; background: rgba(0,0,0,0.3); padding: 0.75rem; border-radius: 6px;">
                                    <strong>Xulosa:</strong> {{ $analysis->conclusion }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <p style="color: var(--text-muted); text-align: center; grid-column: span 2; padding: 2rem;">Laboratoriya tahlillari topilmadi.</p>
                    @endforelse
                </div>
            </div>

            <!-- Tab 4: Vaccinations (Emlashlar) -->
            <div id="tabVaccinations" class="tab-content">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem;">
                    <div>
                        <h3 style="font-size: 1.1rem; color: #fff;">Milliy Emlash Taqvimi & Vaksinatsiya Tarixi</h3>
                        <p style="font-size: 0.78rem; color: var(--text-muted);">Bemor olgan profilaktik va rejali vaksinalar ro'yxati</p>
                    </div>
                    @if(!Auth::check() || !Auth::user()->isPatient())
                    <button type="button" class="btn btn-primary" onclick="openModal('addVaccinationModal')" style="padding: 0.55rem 1rem; font-size: 0.85rem;">
                        <span>💉</span> Emlash Qayd Qilish
                    </button>
                    @endif
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Vaksina Nomi</th>
                                <th>Doza Bosqichi</th>
                                <th>Seriya / Partiya Raqami</th>
                                <th>Emlangan Sana</th>
                                <th>Keyingi Emlash</th>
                                <th>Izoh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeMed->vaccinations as $vac)
                                <tr>
                                    <td><strong>{{ $vac->vaccine_name }}</strong></td>
                                    <td><span class="tag tag-blue">{{ $vac->dose_number }}-doza</span></td>
                                    <td style="font-family: 'JetBrains Mono', monospace;">{{ $vac->batch_number ?? 'B-9901' }}</td>
                                    <td>{{ $vac->administered_at ? $vac->administered_at->format('d.m.Y') : '' }}</td>
                                    <td>{{ $vac->next_due_date ? $vac->next_due_date->format('d.m.Y') : 'Rejalashtirilmagan' }}</td>
                                    <td style="color: var(--text-muted);">{{ $vac->notes ?? 'Asoratsiz' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; color: var(--text-muted);">Emlash ma'lumotlari mavjud emas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 5: Referrals (Yo'llanmalar) -->
            <div id="tabReferrals" class="tab-content">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 0.5rem;">
                    <div>
                        <h3 style="font-size: 1.1rem; color: #fff;">Tor Mutaxassislar va Shifoxonaga Yo'llanmalar</h3>
                        <p style="font-size: 0.78rem; color: var(--text-muted);">Birlamchi poliklinika yoki shifokor tomonidan berilgan rasmiy elektron yo'llanmalar</p>
                    </div>
                    @if(!Auth::check() || !Auth::user()->isPatient())
                    <button type="button" class="btn btn-primary" onclick="openModal('addReferralModal')" style="padding: 0.55rem 1rem; font-size: 0.85rem;">
                        <span>📑</span> Yangi Yo'llanma Berish
                    </button>
                    @endif
                </div>

                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Kerakli Mutaxassislik</th>
                                <th>Yo'naltirilgan Muassasa</th>
                                <th>Yo'llagan Shifokor</th>
                                <th>Shoshilinchlik</th>
                                <th>Sabab / Tashxis</th>
                                <th>Holati</th>
                                <th>Amal Qilish Muddati</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeMed->referrals as $ref)
                                <tr>
                                    <td><strong>{{ $ref->specialty_needed }}</strong></td>
                                    <td>{{ $ref->targetClinic?->name ?? 'Markaziy Shifoxona' }}</td>
                                    <td>{{ $ref->referringDoctor?->name ?? 'Shifokor' }}</td>
                                    <td>
                                        @if($ref->urgency === 'emergency')
                                            <span class="tag tag-red" style="background: rgba(239, 68, 68, 0.2); color: #f87171;">Favqulodda</span>
                                        @elseif($ref->urgency === 'urgent')
                                            <span class="tag tag-amber">Shoshilinch</span>
                                        @else
                                            <span class="tag tag-blue">Rejali (Oddiy)</span>
                                        @endif
                                    </td>
                                    <td style="max-width: 250px; font-size: 0.82rem; color: #cbd5e1;">{{ $ref->reason }}</td>
                                    <td>
                                        <span class="status-badge {{ $ref->status === 'completed' ? 'status-active' : 'status-warning' }}">
                                            {{ $ref->status === 'completed' ? 'Bajarilgan' : ($ref->status === 'accepted' ? 'Qabul qilingan' : 'Kutilmoqda') }}
                                        </span>
                                    </td>
                                    <td>{{ $ref->expires_at ? $ref->expires_at->format('d.m.Y') : 'Muddatsiz' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">Hozirda berilgan yo'llanmalar mavjud emas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 5: Security & Audit Logs (Tibbiy Sir Nazorati) -->
            <div id="tabAudit" class="tab-content">
                <div style="background: rgba(2, 132, 199, 0.05); border: 1px solid rgba(56, 189, 248, 0.2); border-radius: 10px; padding: 1rem 1.25rem; margin-bottom: 1.5rem; font-size: 0.85rem; color: #93c5fd;">
                    ℹ️ <strong>Tibbiy Sir va HIPAA / GDPR Xavfsizlik Standarti:</strong> Ushbu med-kartaning barcha ma'lumotlariga kim, qachon, qaysi maqsadda va qaysi IP-manzildan kirganligi doimiy tarzda qayd etiladi.
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Vaqt</th>
                                <th>Amal Turi</th>
                                <th>Foydalanuvchi / Shifokor</th>
                                <th>IP Manzil</th>
                                <th>Tafsilotlar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeMed->accessLogs as $log)
                                <tr>
                                    <td style="font-family: 'JetBrains Mono', monospace; font-size: 0.8rem;">{{ $log->created_at ? $log->created_at->format('d.m.Y H:i:s') : '' }}</td>
                                    <td>
                                        <span class="status-badge {{ str_contains($log->action, 'emergency') ? 'status-warning' : 'status-active' }}">
                                            {{ $log->action }}
                                        </span>
                                    </td>
                                    <td>{{ $log->user->name ?? 'Avtomatlashtirilgan Tizim / 103 Triage' }}</td>
                                    <td style="font-family: 'JetBrains Mono', monospace; color: var(--text-dim);">{{ $log->ip_address ?? '127.0.0.1' }}</td>
                                    <td style="font-size: 0.78rem; color: var(--text-muted);">
                                        {{ json_encode($log->details, JSON_UNESCAPED_UNICODE) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--text-muted);">Hozircha audit yozuvlari mavjud emas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab 7: E-Navbatlarim (Doctor Appointments & Queue) -->
            <div id="tabAppointments" class="tab-content">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                    <div>
                        <h3 style="font-size: 1.2rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 8px;">
                            <span>🎫</span> Bemorning Shifokor Qabuliga Navbatlari (E-Talonlar)
                        </h3>
                        <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 3px;">
                            Uydan turib olingan barcha elektron navbatlar, qabul vaqtlari va tartib raqamlari
                        </p>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="openAppointmentModal()" style="background: linear-gradient(135deg, #10b981, #059669); box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);">
                        <span>+</span> Yangi Navbat Olish
                    </button>
                </div>

                <div class="appointments-list">
                    @forelse($activeMed->appointments as $apt)
                        <div class="e-ticket-card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.25rem; {{ $apt->status === 'cancelled' ? 'opacity: 0.5; border-color: rgba(239, 68, 68, 0.4);' : '' }}">
                            <div style="display: flex; align-items: center; gap: 1.25rem;">
                                <div class="ticket-queue-badge" style="{{ $apt->status === 'cancelled' ? 'background: #475569;' : '' }}">
                                    <span class="ticket-queue-sub">Navbat</span>
                                    <span class="ticket-queue-num">№ {{ $apt->queue_number }}</span>
                                </div>
                                <div>
                                    <div style="font-size: 1.15rem; font-weight: 800; color: #fff;">
                                        {{ $apt->doctor->name ?? 'Shifokor' }}
                                        <span class="badge badge-blue" style="font-size: 0.75rem; margin-left: 0.4rem;">{{ $apt->doctor->specialty ?? 'Mutaxassis' }}</span>
                                    </div>
                                    <div style="font-size: 0.82rem; color: var(--text-muted); margin-top: 4px;">
                                        🏥 <strong>{{ $apt->clinic->name ?? 'Shifoxona' }}</strong> | 🚪 <span style="color: #fbbf24; font-weight: 700;">{{ $apt->room_number ?? '204-xona' }}</span>
                                    </div>
                                    <div style="font-size: 0.88rem; color: var(--primary-light); margin-top: 6px; font-weight: 600;">
                                        📅 Qabul kuni: <strong>{{ $apt->appointment_date ? $apt->appointment_date->format('d.m.Y') : '' }}</strong> | Soat: <span style="font-family: 'JetBrains Mono', monospace; font-weight: 800; color: #34d399; font-size: 1rem;">{{ $apt->appointment_time }}</span> da
                                    </div>
                                    @if($apt->reason)
                                        <div style="font-size: 0.78rem; color: var(--text-dim); margin-top: 4px;">
                                            Shikoyat: {{ $apt->reason }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div style="text-align: right; display: flex; flex-direction: column; align-items: flex-end; gap: 0.6rem;">
                                <div>
                                    <span class="badge {{ $apt->status === 'pending' ? 'badge-green' : ($apt->status === 'cancelled' ? 'badge-red' : 'badge-blue') }}" style="font-size: 0.8rem; padding: 4px 10px;">
                                        {{ $apt->status === 'pending' ? '● Kutilmoqda (Faol)' : ($apt->status === 'cancelled' ? '✕ Bekor qilingan' : '✓ Yakunlangan') }}
                                    </span>
                                </div>
                                <div style="font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; color: var(--text-dim);">
                                    Talon: {{ $apt->ticket_number }}
                                </div>
                                <div style="display: flex; gap: 0.5rem; margin-top: 4px;">
                                    <button type="button" class="btn btn-secondary" style="padding: 5px 12px; font-size: 0.78rem;" onclick="viewTicketModal('{{ $apt->ticket_number }}', '{{ $apt->queue_number }}', '{{ addslashes($apt->doctor?->name ?? 'Shifokor') }}', '{{ addslashes($apt->doctor?->specialty ?? 'Terapevt') }}', '{{ addslashes($apt->clinic?->name ?? 'Shifoxona') }}', '{{ $apt->room_number ?? '204-xona' }}', '{{ $apt->appointment_date ? $apt->appointment_date->format('d.m.Y') : '' }}', '{{ $apt->appointment_time }}', '{{ addslashes($activeMed->user?->name ?? 'Bemor') }}')">
                                        🎫 Talonni Ko'rish
                                    </button>
                                    @if($apt->status === 'pending')
                                        @if(!Auth::check() || !Auth::user()->isPatient())
                                        <form action="{{ route('med.appointments.complete', $apt->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Qabulni yakunlangan deb belgilaysizmi?');">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary" style="padding: 5px 12px; font-size: 0.78rem; color: #34d399; border-color: rgba(16, 185, 129, 0.3);">
                                                ✓ Yakunlash
                                            </button>
                                        </form>
                                        @endif
                                        <form action="{{ route('med.appointments.cancel', $apt->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Ushbu navbatni bekor qilishni xohlaysizmi?');">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary" style="padding: 5px 12px; font-size: 0.78rem; color: #f87171; border-color: rgba(239, 68, 68, 0.3);">
                                                Bekor qilish
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div style="text-align: center; padding: 3rem; color: var(--text-muted); background: rgba(0,0,0,0.2); border-radius: 14px; border: 1px dashed var(--border-color);">
                            <div style="font-size: 2.5rem; margin-bottom: 0.5rem;">🎫</div>
                            <h4 style="color: #fff; margin-bottom: 0.5rem;">Hozirda shifokorga olingan navbatlar yo'q</h4>
                            <p style="font-size: 0.82rem; margin-bottom: 1.25rem;">Uydan turib qulay vaqtda shifokor qabuliga navbat olishingiz mumkin.</p>
                            <button type="button" class="btn btn-primary" onclick="openAppointmentModal()" style="background: linear-gradient(135deg, #10b981, #059669);">
                                <span>🎫</span> Birinchi Navbatni Olish
                            </button>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Tab 8: Kunlik Dori Ichish Jadvali (Pill Tracker) -->
            <div id="tabPillTracker" class="tab-content">
                <div class="pill-tracker-card">
                    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem; margin-bottom: 0.5rem;">
                        <div>
                            <h3 style="font-size: 1.25rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 0.5rem;">
                                <span>⏰</span> Kunlik Dori Ichish Jadvali (Pill Tracker)
                            </h3>
                            <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 2px;">
                                Bugun: <strong style="color: var(--primary-light);">{{ date('d.m.Y') }} yil</strong> | Bemor: {{ $activeMed->user?->name ?? 'Bemor' }}
                            </p>
                        </div>
                        <div style="text-align: right;">
                            <span class="badge badge-green" id="pillStatsBadge">0 / 0 ta qabul qilindi</span>
                        </div>
                    </div>

                    <!-- Visual Progress Bar -->
                    <div class="pill-progress-bar-bg">
                        <div class="pill-progress-bar-fill" id="pillProgressBar" style="width: 0%;"></div>
                    </div>

                    <!-- Time Slots: Ertalab, Tushlik, Kechqurun -->
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                        
                        <!-- Ertalab Slot -->
                        <div class="pill-slot-group">
                            <div class="pill-slot-header" style="color: #f59e0b;">
                                <span>🌅 Ertalabki Qabul (08:00 - 09:00)</span>
                                <span style="font-size: 0.72rem; color: var(--text-dim);">Nonushta bilan</span>
                            </div>
                            <div id="morningPillsList">
                                @php $hasMorning = false; @endphp
                                @foreach($activeMed->prescriptions as $rx)
                                    @if($rx->status === 'active')
                                        @php $hasMorning = true; @endphp
                                        <div class="pill-item-row" id="pill_row_{{ $rx->id }}_morning">
                                            <div>
                                                <div style="font-weight: 700; font-size: 0.88rem; color: #fff;">{{ $rx->medication_name }} <span style="color: var(--primary-light); font-size: 0.78rem;">{{ $rx->dosage }}</span></div>
                                                <div style="font-size: 0.72rem; color: var(--text-muted);">{{ $rx->instructions ?? 'Qabul tartibi bo\'yicha' }}</div>
                                            </div>
                                            <button type="button" class="pill-check-btn" data-pill-id="{{ $rx->id }}_morning" onclick="togglePillTracker('{{ $rx->id }}', 'morning', this)">
                                                <span>○</span> Ichildi
                                            </button>
                                        </div>
                                    @endif
                                @endforeach
                                @if(!$hasMorning)
                                    <div style="font-size: 0.78rem; color: var(--text-dim); text-align: center; padding: 1rem;">Faol dori belgilanmagan</div>
                                @endif
                            </div>
                        </div>

                        <!-- Tushlik Slot -->
                        <div class="pill-slot-group">
                            <div class="pill-slot-header" style="color: #38bdf8;">
                                <span>☀️ Tushlik Qabuli (13:00 - 14:00)</span>
                                <span style="font-size: 0.72rem; color: var(--text-dim);">Tushlikdan so'ng</span>
                            </div>
                            <div id="afternoonPillsList">
                                @php $hasNoon = false; @endphp
                                @foreach($activeMed->prescriptions as $rx)
                                    @if($rx->status === 'active' && (str_contains(strtolower($rx->frequency), '2') || str_contains(strtolower($rx->frequency), '3') || str_contains(strtolower($rx->frequency), 'mahal')))
                                        @php $hasNoon = true; @endphp
                                        <div class="pill-item-row" id="pill_row_{{ $rx->id }}_afternoon">
                                            <div>
                                                <div style="font-weight: 700; font-size: 0.88rem; color: #fff;">{{ $rx->medication_name }} <span style="color: var(--primary-light); font-size: 0.78rem;">{{ $rx->dosage }}</span></div>
                                                <div style="font-size: 0.72rem; color: var(--text-muted);">{{ $rx->instructions ?? 'Ovqatdan so\'ng' }}</div>
                                            </div>
                                            <button type="button" class="pill-check-btn" data-pill-id="{{ $rx->id }}_afternoon" onclick="togglePillTracker('{{ $rx->id }}', 'afternoon', this)">
                                                <span>○</span> Ichildi
                                            </button>
                                        </div>
                                    @endif
                                @endforeach
                                @if(!$hasNoon)
                                    <div style="font-size: 0.78rem; color: var(--text-dim); text-align: center; padding: 1rem;">Tushlikka reja yo'q</div>
                                @endif
                            </div>
                        </div>

                        <!-- Kechqurun Slot -->
                        <div class="pill-slot-group">
                            <div class="pill-slot-header" style="color: #a78bfa;">
                                <span>🌙 Kechki Qabul (20:00 - 21:00)</span>
                                <span style="font-size: 0.72rem; color: var(--text-dim);">Uyqudan oldin</span>
                            </div>
                            <div id="eveningPillsList">
                                @php $hasEvening = false; @endphp
                                @foreach($activeMed->prescriptions as $rx)
                                    @if($rx->status === 'active')
                                        @php $hasEvening = true; @endphp
                                        <div class="pill-item-row" id="pill_row_{{ $rx->id }}_evening">
                                            <div>
                                                <div style="font-weight: 700; font-size: 0.88rem; color: #fff;">{{ $rx->medication_name }} <span style="color: var(--primary-light); font-size: 0.78rem;">{{ $rx->dosage }}</span></div>
                                                <div style="font-size: 0.72rem; color: var(--text-muted);">{{ $rx->instructions ?? 'Kechki tartib' }}</div>
                                            </div>
                                            <button type="button" class="pill-check-btn" data-pill-id="{{ $rx->id }}_evening" onclick="togglePillTracker('{{ $rx->id }}', 'evening', this)">
                                                <span>○</span> Ichildi
                                            </button>
                                        </div>
                                    @endif
                                @endforeach
                                @if(!$hasEvening)
                                    <div style="font-size: 0.78rem; color: var(--text-dim); text-align: center; padding: 1rem;">Kechga reja yo'q</div>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </section>

    </main>

    <!-- Modal 1: 103 Tez Tibbiy Yordam Triage Simulyatori -->
    <div class="modal-backdrop" id="emergencyModal">
        <div class="modal-box emergency-theme">
            <button type="button" class="modal-close" onclick="closeModal('emergencyModal')">&times;</button>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                <div style="width: 44px; height: 44px; background: rgba(239, 68, 68, 0.2); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">🚨</div>
                <div>
                    <h2 style="font-size: 1.35rem; color: #f87171;">103 Tez Yordam & Shoshilinch Triage</h2>
                    <p style="font-size: 0.78rem; color: var(--text-muted);">Bemorning med-kartasidagi QR-kod skanerlanganda ko'rinadigan hayotiy muhim profil</p>
                </div>
            </div>

            <div id="triageLoading" style="text-align: center; padding: 2rem; color: var(--text-muted);">
                Yuklanmoqda...
            </div>

            <div id="triageContent" style="display: none;">
                <!-- Filled by JavaScript -->
            </div>
        </div>
    </div>

    @if(!Auth::check() || !Auth::user()->isPatient())
    <!-- Modal 2: Yangi Med-Karta Ochish -->
    <div class="modal-backdrop" id="newCardModal">
        <div class="modal-box">
            <button type="button" class="modal-close" onclick="closeModal('newCardModal')">&times;</button>
            <h2 style="font-size: 1.35rem; margin-bottom: 0.4rem;">Yangi "Med" Karta Rasmiylashtirish</h2>
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.5rem;">Fuqaro / Bemor uchun yangi raqamli tibbiy profil ochish</p>

            <form action="{{ route('med.storeCard') }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label>Bemor F.I.Sh</label>
                        <input type="text" name="patient_name" class="form-control" placeholder="Masalan: Azizov Sardor" required>
                    </div>
                    <div class="form-group">
                        <label>JSHSHIR (14 xonali PINFL)</label>
                        <input type="text" name="pinfl" class="form-control" placeholder="32509900010012" maxlength="14" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Telefon Raqam</label>
                        <input type="text" name="phone" class="form-control" placeholder="+998 90 123 45 67">
                    </div>
                    <div class="form-group">
                        <label>Karta Turi</label>
                        <select name="card_type" class="form-control">
                            <option value="standard">Standard (Umumiy)</option>
                            <option value="pediatric">Pediatrik (Bolalar uchun)</option>
                            <option value="chronic">Surunkali Bemor Kartasi</option>
                            <option value="senior">Keksa yoshdagilar (Geriatriya)</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Qon Guruhi</label>
                        <select name="blood_group" class="form-control">
                            <option value="O+">O(I) Musbat (Rh+)</option>
                            <option value="O-">O(I) Manfiy (Rh-)</option>
                            <option value="A+">A(II) Musbat (Rh+)</option>
                            <option value="A-">A(II) Manfiy (Rh-)</option>
                            <option value="B+">B(III) Musbat (Rh+)</option>
                            <option value="B-">B(III) Manfiy (Rh-)</option>
                            <option value="AB+">AB(IV) Musbat (Rh+)</option>
                            <option value="AB-">AB(IV) Manfiy (Rh-)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Rezyus Omili</label>
                        <select name="rhesus_factor" class="form-control">
                            <option value="positive">Musbat (+)</option>
                            <option value="negative">Manfiy (-)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Allergiyalar (vergul bilan ajratilgan)</label>
                    <input type="text" name="allergies_raw" class="form-control" placeholder="Penitsillin, Analgin, Qulupnay">
                </div>

                <div class="form-group">
                    <label>Surunkali Kasalliklar (vergul bilan)</label>
                    <input type="text" name="chronic_raw" class="form-control" placeholder="Gipertoniya, Qandli diabet">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Favqulodda Bog'lanish Shaxsi</label>
                        <input type="text" name="emergency_contact_name" class="form-control" placeholder="Masalan: Otasi / Turmush o'rtog'i">
                    </div>
                    <div class="form-group">
                        <label>Favqulodda Aloqa Telefoni</label>
                        <input type="text" name="emergency_contact_phone" class="form-control" placeholder="+998 90 987 65 43">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.85rem; margin-top: 0.5rem;">
                    Med-Karta Yaratish
                </button>
            </form>
        </div>
    </div>

    <!-- Modal 3: Yangi Ko'rik / Tashxis Qo'shish -->
    <div class="modal-backdrop" id="addRecordModal">
        <div class="modal-box">
            <button type="button" class="modal-close" onclick="closeModal('addRecordModal')">&times;</button>
            <h2 style="font-size: 1.35rem; margin-bottom: 0.4rem;">Tibbiy Ko'rik va Tashxis Qo'shish</h2>
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.5rem;">Med-karta: {{ $activeMed->med_number }} ({{ $activeMed->user?->name ?? 'Bemor' }})</p>

            <form action="{{ route('med.addRecord', ['medNumber' => $activeMed->med_number]) }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label>Shifokor</label>
                        <select name="doctor_id" class="form-control" required>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->name }} ({{ $doc->specialty }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tibbiyot Muassasasi</label>
                        <select name="clinic_id" class="form-control" required>
                            @foreach($clinics as $cl)
                                <option value="{{ $cl->id }}">{{ $cl->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Klinik Tashxis</label>
                        <input type="text" name="diagnosis" class="form-control" placeholder="Masalan: Birlamchi gipertoniya II bosqich" required>
                    </div>
                    <div class="form-group">
                        <label>ICD-10 Kodi</label>
                        <input type="text" name="icd10_code" class="form-control" placeholder="I10, J06.9, E11">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Qon Bosimi (masalan: 130/85)</label>
                        <input type="text" name="blood_pressure" class="form-control" placeholder="130/85 mmHg">
                    </div>
                    <div class="form-group">
                        <label>Puls (urish/daq)</label>
                        <input type="text" name="heart_rate" class="form-control" placeholder="78">
                    </div>
                    <div class="form-group">
                        <label>Tana Harorati (°C)</label>
                        <input type="text" name="temperature" class="form-control" placeholder="36.6">
                    </div>
                </div>

                <div class="form-group">
                    <label>Bemor Shikoyatlari</label>
                    <textarea name="symptoms" class="form-control" rows="2" placeholder="Bosh og'rig'i, holsizlik, ko'ngil aynishi..."></textarea>
                </div>

                <div class="form-group">
                    <label>Davolash Rejasi va Tavsiyalar</label>
                    <textarea name="treatment_plan" class="form-control" rows="2" placeholder="Rejim, parhez, qabul qilinishi kerak bo'lgan dorilar..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.85rem; margin-top: 0.5rem;">
                    Ko'rik Qaydini Saqlash
                </button>
            </form>
        </div>
    </div>

    <!-- Modal 4: Retsept Yozish -->
    <div class="modal-backdrop" id="addPrescriptionModal">
        <div class="modal-box">
            <button type="button" class="modal-close" onclick="closeModal('addPrescriptionModal')">&times;</button>
            <h2 style="font-size: 1.35rem; margin-bottom: 0.4rem;">Elektron Retsept Yozish</h2>
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.5rem;">Med-karta: {{ $activeMed->med_number }} ({{ $activeMed->user?->name ?? 'Bemor' }})</p>

            <form action="{{ route('med.addPrescription', ['medNumber' => $activeMed->med_number]) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label>Shifokor</label>
                    <select name="doctor_id" class="form-control" required>
                        @foreach($doctors as $doc)
                            <option value="{{ $doc->id }}">{{ $doc->name }} ({{ $doc->specialty }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Dori Vositasi Nomi</label>
                        <input type="text" name="medication_name" class="form-control" placeholder="Masalan: Enalapril / Amoxicillin" required>
                    </div>
                    <div class="form-group">
                        <label>Dozasi</label>
                        <input type="text" name="dosage" class="form-control" placeholder="10 mg, 500 mg" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Qabul Tartibi</label>
                        <input type="text" name="frequency" class="form-control" placeholder="1 mahal ertalab och qoringa" required>
                    </div>
                    <div class="form-group">
                        <label>Muddati (Kun)</label>
                        <input type="number" name="duration_days" class="form-control" value="14" min="1" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Maxsus Ko'rsatmalar</label>
                    <textarea name="instructions" class="form-control" rows="2" placeholder="Ko'p suv bilan qabul qilinsin..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.85rem;">
                    Elektron Retseptni Tasdiqlash
                </button>
            </form>
        </div>
    </div>

    <!-- Modal: Laboratoriya / Diagnostika Qo'shish -->
    <div class="modal-backdrop" id="addAnalysisModal">
        <div class="modal-box">
            <button type="button" class="modal-close" onclick="closeModal('addAnalysisModal')">&times;</button>
            <h2 style="font-size: 1.35rem; margin-bottom: 0.4rem;">Laboratoriya / Diagnostika Tahlili Kiritish</h2>
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.5rem;">Med-karta: {{ $activeMed->med_number }} ({{ $activeMed->user?->name ?? 'Bemor' }})</p>

            <form action="{{ route('med.addAnalysis', ['medNumber' => $activeMed->med_number]) }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label>Shifokor / Laborant</label>
                        <select name="doctor_id" class="form-control" required>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->name }} ({{ $doc->specialty }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Muassasa / Laboratoriya</label>
                        <select name="clinic_id" class="form-control" required>
                            @foreach($clinics as $cl)
                                <option value="{{ $cl->id }}">{{ $cl->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Tahlil Turi</label>
                        <select name="analysis_type" class="form-control" required>
                            <option value="blood">Umumiy qon tahlili</option>
                            <option value="biochemistry">Biokimyoviy tahlil</option>
                            <option value="ecg">Elektrokardiogramma (EKG)</option>
                            <option value="ultrasound">Ultratovush (UZI)</option>
                            <option value="mri">MRT / KT tekshiruvi</option>
                            <option value="urine">Umumiy siydik tahlili</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Tahlil Natijasi Holati</label>
                        <select name="status" class="form-control" required>
                            <option value="normal">Normal (Me'yorda)</option>
                            <option value="abnormal">Ogohlantirish (Me'yordan chetga chiqish)</option>
                            <option value="critical">Kritik (Xavfli ko'rsatkich)</option>
                            <option value="pending">Kutilmoqda</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Tahlil / Tekshiruv Nomi</label>
                    <input type="text" name="title" class="form-control" placeholder="Masalan: Umumiy qon tahlili yoki 12-tarmoqli EKG" required>
                </div>

                <div class="form-group">
                    <label>Ko'rsatkichlar (Har birini yangi qatordan: Nomi: Qiymati)</label>
                    <textarea name="indicators_raw" class="form-control" rows="3" placeholder="Gemoglobin: 142 g/l&#10;Leykotsitlar: 6.8 x10^9/l&#10;SOE: 7 mm/soat"></textarea>
                </div>

                <div class="form-group">
                    <label>Shifokor / Laborant Xulosasi</label>
                    <textarea name="conclusion" class="form-control" rows="2" placeholder="Masalan: Ko'rsatkichlar me'yor chegarasida. O'tkir yallig'lanish belgilari aniqlanmadi."></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.85rem;">
                    Tahlil Natijasini Saqlash
                </button>
            </form>
        </div>
    </div>

    <!-- Modal: Emlash Qayd Qilish -->
    <div class="modal-backdrop" id="addVaccinationModal">
        <div class="modal-box">
            <button type="button" class="modal-close" onclick="closeModal('addVaccinationModal')">&times;</button>
            <h2 style="font-size: 1.35rem; margin-bottom: 0.4rem;">Yangi Emlashni Qayd Qilish</h2>
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.5rem;">Med-karta: {{ $activeMed->med_number }} ({{ $activeMed->user?->name ?? 'Bemor' }})</p>

            <form action="{{ route('med.addVaccination', ['medNumber' => $activeMed->med_number]) }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label>Emlagan Shifokor / Hamshira</label>
                        <select name="administered_by_doctor_id" class="form-control" required>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->name }} ({{ $doc->specialty }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Emlash Markazi / Poliklinika</label>
                        <select name="clinic_id" class="form-control" required>
                            @foreach($clinics as $cl)
                                <option value="{{ $cl->id }}">{{ $cl->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Vaksina Nomi</label>
                        <input type="text" name="vaccine_name" class="form-control" placeholder="Masalan: Gepatit B (Engerix-B)" required>
                    </div>
                    <div class="form-group">
                        <label>Doza Bosqichi</label>
                        <select name="dose_number" class="form-control" required>
                            <option value="1">1-doza (Birlamchi)</option>
                            <option value="2">2-doza</option>
                            <option value="3">3-doza (Revaksinatsiya)</option>
                            <option value="4">4-doza (Buster)</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Seriya / Partiya Raqami</label>
                        <input type="text" name="batch_number" class="form-control" placeholder="VAC-2026-889">
                    </div>
                    <div class="form-group">
                        <label>Emlangan Sana</label>
                        <input type="date" name="administered_at" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Keyingi Emlash Sanasi (Rejalashtirilgan bo'lsa)</label>
                    <input type="date" name="next_due_date" class="form-control">
                </div>

                <div class="form-group">
                    <label>Emlashdan Keyingi Holat / Izoh</label>
                    <input type="text" name="notes" class="form-control" placeholder="Asoratsiz o'tgan, nojo'ya ta'sir kuzatilmadi" value="Asoratsiz o'tgan">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.85rem;">
                    Emlash Ma'lumotini Saqlash
                </button>
            </form>
        </div>
    </div>

    <!-- Modal: Yangi Yo'llanma Berish -->
    <div class="modal-backdrop" id="addReferralModal">
        <div class="modal-box">
            <button type="button" class="modal-close" onclick="closeModal('addReferralModal')">&times;</button>
            <h2 style="font-size: 1.35rem; margin-bottom: 0.4rem;">Rasmiy Tibbiy Yo'llanma Berish</h2>
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 1.5rem;">Med-karta: {{ $activeMed->med_number }} ({{ $activeMed->user?->name ?? 'Bemor' }})</p>

            <form action="{{ route('med.addReferral', ['medNumber' => $activeMed->med_number]) }}" method="POST">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label>Yo'llayotgan Shifokor</label>
                        <select name="referring_doctor_id" class="form-control" required>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}">{{ $doc->name }} ({{ $doc->specialty }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Birlamchi Muassasa</label>
                        <select name="referring_clinic_id" class="form-control" required>
                            @foreach($clinics as $cl)
                                <option value="{{ $cl->id }}">{{ $cl->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Qabul Qiluvchi Shifoxona / Markaz</label>
                        <select name="target_clinic_id" class="form-control" required>
                            @foreach($clinics as $cl)
                                <option value="{{ $cl->id }}">{{ $cl->name }} ({{ ucfirst($cl->type) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Kerakli Mutaxassislik</label>
                        <input type="text" name="specialty_needed" class="form-control" placeholder="Masalan: Kardiolog, Nevrolog, Jarroh" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Shoshilinchlik Darajasi</label>
                        <select name="urgency" class="form-control" required>
                            <option value="routine">Rejali (Oddiy)</option>
                            <option value="urgent">Shoshilinch</option>
                            <option value="emergency">Favqulodda (Tezkor)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Amal Qilish Muddati (Kun)</label>
                        <input type="number" name="expires_days" class="form-control" value="30" min="1" max="90" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Yo'llanma Sababi va Birlamchi Tashxis</label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="Arterial bosim beqarorligi, kardiogramma nazorati va tor mutaxassis konsultatsiyasi uchun..." required></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 0.85rem;">
                    Yo'llanmani Tasdiqlash & Rasmiylashtirish
                </button>
            </form>
        </div>
    </div>
    @endif

    <!-- Modal 5: QR Kod & Bemor Tibbiy Pasporti -->
    <div class="modal-backdrop" id="qrPassModal">
        <div class="modal-box qr-modal-animated" id="qrModalBox" style="text-align: center; max-width: 520px; transition: max-width 0.3s ease;">
            <button type="button" class="modal-close" onclick="closeModal('qrPassModal')">&times;</button>
            <div style="width: 50px; height: 50px; background: rgba(56, 189, 248, 0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin: 0 auto 0.75rem auto;">
                📱
            </div>
            <h2 style="font-size: 1.35rem; color: #fff; margin-bottom: 0.25rem;">Raqamli Med-Karta QR-Kodi</h2>
            <p style="font-size: 0.82rem; color: var(--text-muted); margin-bottom: 1rem;">
                {{ $activeMed->user?->name ?? 'Bemor' }} | {{ $activeMed->med_number }}
            </p>

            <div id="qrImgBox" onclick="toggleQrZoom()" title="Kattalashtirish / Kichraytirish uchun bosing" style="background: #fff; padding: 1.25rem; border-radius: 18px; display: inline-block; box-shadow: 0 15px 35px rgba(0,0,0,0.6); margin-bottom: 0.75rem; cursor: zoom-in; transition: all 0.3s ease;">
                <img id="qrModalImg" src="https://api.qrserver.com/v1/create-qr-code/?size=450x450&data={{ urlencode(route('med.scan', $activeMed?->qr_token ?? 'default')) }}" alt="QR Code" style="display: block; width: 310px; height: 310px; max-width: 80vw; max-height: 80vw; transition: all 0.3s ease;">
            </div>

            <div style="margin-bottom: 1.25rem;">
                <button type="button" class="btn btn-secondary" onclick="toggleQrZoom()" style="font-size: 0.78rem; padding: 4px 12px; border-radius: 20px; border-color: rgba(56, 189, 248, 0.3); color: var(--primary-light);">
                    <span id="zoomIcon">🔍</span> <span id="zoomText">Maksimal Kattalashtirish (Zoom)</span>
                </button>
            </div>

            <div style="background: rgba(2, 132, 199, 0.1); border: 1px solid rgba(56, 189, 248, 0.2); border-radius: 10px; padding: 0.75rem 1rem; margin-bottom: 1.25rem; text-align: left; font-size: 0.8rem;">
                <div style="color: var(--primary-light); font-weight: 700; margin-bottom: 2px;">
                    🔗 Skaner Havolasi:
                </div>
                <a href="{{ route('med.scan', $activeMed?->qr_token ?? 'default') }}" target="_blank" style="color: #cbd5e1; word-break: break-all; text-decoration: none; font-family: 'JetBrains Mono', monospace; font-size: 0.75rem;">
                    {{ route('med.scan', $activeMed?->qr_token ?? 'default') }}
                </a>
            </div>

            <div style="display: flex; gap: 0.6rem; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('med.scan', $activeMed?->qr_token ?? 'default') }}" target="_blank" class="btn btn-primary">
                    <span>📱</span> Skaner Sahifasini Ochish
                </a>
                <button type="button" class="btn btn-secondary" onclick="shareQrTelegram()" style="background: rgba(14, 165, 233, 0.15); border-color: rgba(56, 189, 248, 0.4); color: #38bdf8;">
                    <span>✈️</span> Telegramga Yuborish
                </button>
                <button type="button" class="btn btn-secondary" onclick="printQrPass()">
                    <span>🖨️</span> Chop Etish
                </button>
                <button type="button" class="btn btn-secondary" onclick="downloadQrPng()">
                    <span>💾</span> Yuklab Olish
                </button>
            </div>
        </div>
    </div>

    <!-- Modal 6: Online Shifokorga Navbat Olish -->
    <div class="modal-backdrop" id="appointmentModal">
        <div class="modal-box" style="max-width: 650px;">
            <button type="button" class="modal-close" onclick="closeModal('appointmentModal')">&times;</button>
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                <div style="width: 44px; height: 44px; background: rgba(16, 185, 129, 0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    🎫
                </div>
                <div>
                    <h2 style="font-size: 1.3rem; color: #fff;">Online Shifokorga Navbat Olish</h2>
                    <p style="font-size: 0.78rem; color: var(--text-muted);">
                        Bemor: <strong style="color: #fff;">{{ $activeMed->user?->name ?? 'Bemor' }}</strong> ({{ $activeMed->med_number }})
                    </p>
                </div>
            </div>

            <form action="{{ route('med.appointments.book') }}" method="POST" id="appointmentForm">
                @csrf
                <input type="hidden" name="med_id" value="{{ $activeMed->id }}">
                <input type="hidden" name="appointment_time" id="selectedTimeInput" required>

                <div class="form-row">
                    <div class="form-group">
                        <label>1. Shifokorni Tanlang</label>
                        <select name="doctor_id" id="appointmentDoctorSelect" class="form-control" onchange="loadDoctorSlots()" required>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}" data-specialty="{{ $doc->specialty }}" data-clinic="{{ $doc->clinic?->name ?? 'Markaziy Shifoxona' }}">
                                    {{ $doc->name }} ({{ $doc->specialty }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>2. Qabul Sanasi</label>
                        <input type="date" name="appointment_date" id="appointmentDateInput" class="form-control" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" onchange="loadDoctorSlots()" required>
                    </div>
                </div>

                <!-- Live Doctor Details Banner -->
                <div id="doctorInfoBanner" style="background: rgba(2, 132, 199, 0.1); border: 1px solid rgba(56, 189, 248, 0.25); border-radius: 10px; padding: 0.75rem 1rem; margin-bottom: 1.25rem; font-size: 0.8rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.5rem;">
                    <div>
                        <div id="bannerDocName" style="font-weight: 700; color: #fff;">Yuklanmoqda...</div>
                        <div id="bannerDocClinic" style="color: var(--text-muted); font-size: 0.75rem;">...</div>
                    </div>
                    <div style="text-align: right;">
                        <span class="badge badge-green" id="bannerQueueCount">Navbat holati tekshirilmoqda</span>
                    </div>
                </div>

                <!-- Time Slots Section -->
                <div class="form-group">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.4rem; flex-wrap: wrap; gap: 0.3rem;">
                        <label style="margin-bottom: 0;">3. Shifokorning Bo'sh Soatlari (Qabul Vaqtini Tanlang)</label>
                        <span style="font-size: 0.72rem; color: #34d399;">● Yashil — Bo'sh vaqtlar</span>
                    </div>

                    <div id="slotsLoader" style="text-align: center; padding: 1.5rem; color: var(--text-muted); display: none;">
                        Bo'sh vaqtlar tekshirilmoqda...
                    </div>

                    <div class="slots-grid" id="slotsContainer">
                        <!-- Filled by JS -->
                    </div>
                    <div id="slotErrorNotice" style="display: none; color: #f87171; font-size: 0.78rem; margin-top: 0.4rem;">
                        Iltimos, yuqoridagi bo'sh soatlardan birini tanlang!
                    </div>
                </div>

                <!-- Selected Slot & Queue Preview Banner -->
                <div id="selectedSlotBanner" style="display: none; background: rgba(16, 185, 129, 0.12); border: 1px solid rgba(16, 185, 129, 0.3); border-radius: 10px; padding: 0.85rem 1rem; margin-bottom: 1.25rem; align-items: center; justify-content: space-between;">
                    <div>
                        <div style="font-size: 0.75rem; color: #6ee7b7; font-weight: 700; text-transform: uppercase;">Tanlangan Qabul Vaqti</div>
                        <div style="font-size: 1.15rem; font-weight: 800; color: #fff;" id="selectedSlotDisplay">--:--</div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.75rem; color: #6ee7b7;">Sizning navbatingiz:</div>
                        <div style="font-size: 1.25rem; font-weight: 800; color: #34d399;" id="estimatedQueueDisplay">№ 01</div>
                    </div>
                </div>

                <div class="form-group">
                    <label>4. Shikoyat / Qabul Sababi (Ixtiyoriy)</label>
                    <textarea name="reason" class="form-control" rows="2" placeholder="Masalan: Bosh aylanishi, yurak sohasidagi og'riq yoki profilaktik ko'rik..."></textarea>
                </div>

                <button type="submit" class="btn btn-primary" id="submitAppointmentBtn" style="width: 100%; justify-content: center; padding: 0.85rem; font-size: 0.95rem; background: linear-gradient(135deg, #10b981, #059669);" disabled>
                    🎫 Navbatni Tasdiqlash & E-Talon Olish
                </button>
            </form>
        </div>
    </div>

    <!-- Modal 7: Rasmiy E-Talon Ko'rish va Chop Etish -->
    <div class="modal-backdrop" id="ticketModal">
        <div class="modal-box" style="max-width: 480px; text-align: center;">
            <button type="button" class="modal-close" onclick="closeModal('ticketModal')">&times;</button>
            
            <div style="background: #0f172a; border: 2px dashed rgba(56, 189, 248, 0.5); border-radius: 18px; padding: 1.75rem; position: relative; margin-bottom: 1.5rem; text-align: left;" id="printableTicketArea">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.25rem;">
                    <div>
                        <div style="font-size: 0.7rem; color: var(--primary-light); font-weight: 800; letter-spacing: 1px; text-transform: uppercase;">O'ZBEKISTON RESPUBLIKASI SOG'LIQNI SAQLASH TIZIMI</div>
                        <h3 style="font-size: 1.15rem; font-weight: 800; color: #fff; margin-top: 2px;">ELEKTRON NAVBAT TALONI</h3>
                        <div id="modalTicketCode" style="font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: var(--text-dim);">NAV-2026-001</div>
                    </div>
                    <div style="width: 36px; height: 36px; background: linear-gradient(135deg, var(--primary-light), var(--primary)); border-radius: 8px; display: flex; align-items: center; justify-content: center; font-weight: 800; color: #fff; font-size: 1.1rem;">
                        ⚕
                    </div>
                </div>

                <!-- Big Queue Number Centerpiece -->
                <div style="text-align: center; background: rgba(2, 132, 199, 0.15); border: 1px solid rgba(56, 189, 248, 0.3); border-radius: 14px; padding: 1rem; margin-bottom: 1.25rem;">
                    <div style="font-size: 0.75rem; color: var(--primary-light); font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">SIZNING NAVBAT RAQAMINGIZ</div>
                    <div id="modalQueueNumber" style="font-size: 2.75rem; font-weight: 900; color: #fff; line-height: 1.1; margin: 4px 0;">№ 01</div>
                    <div id="modalAppointmentDateTime" style="font-size: 0.85rem; color: #38bdf8; font-weight: 600;">09.09.2026 soat 10:30 da</div>
                </div>

                <div style="font-size: 0.82rem; line-height: 1.7; color: #cbd5e1; margin-bottom: 1rem;">
                    <div><strong>Bemor:</strong> <span id="modalPatientName">{{ $activeMed->user?->name ?? 'Bemor' }}</span></div>
                    <div><strong>Shifokor:</strong> <span id="modalDoctorName">Dr. Rustam Yusupov</span></div>
                    <div><strong>Mutaxassislik:</strong> <span id="modalSpecialty">Kardiolog</span></div>
                    <div><strong>Shifoxona:</strong> <span id="modalClinic">Markaziy Shifoxona</span></div>
                    <div><strong>Qabul xonasi:</strong> <span id="modalRoom" style="color: #fbbf24; font-weight: 700;">204-xona</span></div>
                </div>

                <div style="border-top: 1px dashed var(--border-color); padding-top: 0.75rem; font-size: 0.72rem; color: var(--text-dim); text-align: center;">
                    ⚠️ Iltimos, qabul boshlanishidan kamida 10 daqiqa oldin xona oldida bo'ling.
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
                <button type="button" class="btn btn-primary" onclick="printTicketOnly()">
                    <span>🖨️</span> Talonni Chop Etish
                </button>
                <button type="button" class="btn btn-secondary" onclick="shareTicketTelegram()" style="background: rgba(14, 165, 233, 0.15); border-color: rgba(56, 189, 248, 0.4); color: #38bdf8;">
                    <span>✈️</span> Telegramga Yuborish
                </button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('ticketModal')">
                    Yopish
                </button>
            </div>
        </div>
    </div>

    <!-- Modal 8: AI Tibbiy Maslahatchi & Triage -->
    <div class="modal-backdrop" id="aiConsultantModal">
        <div class="modal-box" style="max-width: 650px;">
            <button type="button" class="modal-close" onclick="closeModal('aiConsultantModal')">&times;</button>
            
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.25rem;">
                <div style="width: 48px; height: 48px; border-radius: 14px; background: linear-gradient(135deg, #06b6d4, #0284c7); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; box-shadow: 0 0 20px rgba(6, 182, 212, 0.4);">
                    🤖
                </div>
                <div>
                    <h2 style="font-size: 1.35rem; font-weight: 800; color: #fff;">AI Tibbiy Maslahatchi</h2>
                    <p style="font-size: 0.78rem; color: var(--text-muted);">Semptomlarni tahlil qilish, xavf darajasini aniqlash va shifokor tavsiya etish</p>
                </div>
            </div>

            <!-- Pre-defined quick symptom chips -->
            <div style="margin-bottom: 1rem;">
                <div style="font-size: 0.75rem; color: var(--text-dim); margin-bottom: 0.4rem; text-transform: uppercase; font-weight: 600;">Tezkor shikoyat namunalari:</div>
                <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                    <span class="ai-chip" onclick="setAiSymptom('Yurak sohasida qisuvchi og\'riq bor, chap qo\'limga beryapti va nafas olishim qiyinlashmoqda.')">🫀 Yurak og'rig'i</span>
                    <span class="ai-chip" onclick="setAiSymptom('Boshim qattiq og\'rib aylanmoqda, arterial bosimim 145/95 ga ko\'tarildi, ko\'nglim ayniyapti.')">🧠 Qon bosimi ko'tarilishi</span>
                    <span class="ai-chip" onclick="setAiSymptom('Doimiy holsizlik, tez charchash, ko\'z oldim qorong\'ilashishi. Gemoglobin darajam 95 g/l ga tushgan.')">🩸 Qon kamligi (Anemiya)</span>
                    <span class="ai-chip" onclick="setAiSymptom('Tizzalarimda va belimda kuchli og\'riq bor, harakatlanishim qiyinlashib qolgan.')">🦴 Bo'g'im va bel og'rig'i</span>
                    <span class="ai-chip" onclick="setAiSymptom('Tana haroratim 38.5 daraja, quruq yo\'tal va tomoqda qattiq og\'riq bor.')">🌡️ Yuqori isitma va shamollash</span>
                </div>
            </div>

            <div class="form-group">
                <label>Shikoyatlaringiz yoki tahlil natijalaringizni batafsil yozing:</label>
                <textarea id="aiSymptomInput" class="form-control" rows="3" placeholder="Masalan: Ikki kundan beri boshim aylanib, yuragim tez urmoqda..."></textarea>
            </div>

            <button type="button" class="btn btn-primary" id="aiRunBtn" onclick="runAiConsultant()" style="width: 100%; justify-content: center; padding: 0.85rem; background: linear-gradient(135deg, #06b6d4, #0284c7); font-size: 0.95rem; font-weight: 700;">
                <span>✨</span> Sun'iy Intellekt Tahlilini Boshlash
            </button>

            <!-- Loading Indicator -->
            <div id="aiLoadingIndicator" style="display: none; text-align: center; padding: 1.5rem; color: #38bdf8;">
                <div style="font-size: 1.8rem; animation: pulse-border 1.5s infinite;">🤖</div>
                <div style="font-size: 0.85rem; margin-top: 0.5rem; font-weight: 600;">Klinik ma'lumotlar va simptomlar tahlil qilinmoqda...</div>
            </div>

            <!-- Result Box -->
            <div id="aiResultBox" style="display: none; margin-top: 1.25rem; background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 14px; padding: 1.25rem;">
                <!-- Filled dynamically by JavaScript -->
            </div>
        </div>
    </div>

    <!-- Modal 9: Rasmiy E-Tibbiy Pasport (Print & PDF) -->
    <div class="modal-backdrop" id="medicalPassportModal">
        <div class="modal-box" style="max-width: 750px;">
            <button type="button" class="modal-close" onclick="closeModal('medicalPassportModal')">&times;</button>
            
            <div id="printablePassportArea" style="background: #0f172a; border: 2px solid rgba(56, 189, 248, 0.4); border-radius: 18px; padding: 2rem; color: #fff; margin-bottom: 1.25rem;">
                
                <!-- Passport Header -->
                <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--border-color); padding-bottom: 1.25rem; margin-bottom: 1.25rem; flex-wrap: wrap; gap: 1rem;">
                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                        <div style="width: 50px; height: 50px; border-radius: 12px; background: linear-gradient(135deg, #0284c7, #0369a1); display: flex; align-items: center; justify-content: center; font-size: 1.8rem;">
                            ⚕
                        </div>
                        <div>
                            <div style="font-size: 0.72rem; color: var(--primary-light); font-weight: 800; letter-spacing: 1.5px; text-transform: uppercase;">O'ZBEKISTON RESPUBLIKASI SOG'LIQNI SAQLASH VAZIRLIGI</div>
                            <h2 style="font-size: 1.35rem; font-weight: 800; margin-top: 2px;">YAGONA ELEKTRON TIBBIY PASPORT</h2>
                            <div style="font-family: 'JetBrains Mono', monospace; font-size: 0.8rem; color: #38bdf8;">PASPORT ID: {{ $activeMed->med_number }}</div>
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode(route('med.scan', $activeMed?->qr_token ?? 'default')) }}" alt="QR" style="width: 75px; height: 75px; border-radius: 8px; background: #fff; padding: 4px; display: inline-block;">
                    </div>
                </div>

                <!-- Patient Core Info Grid -->
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; background: rgba(2, 132, 199, 0.1); border: 1px solid rgba(56, 189, 248, 0.2); border-radius: 12px; padding: 1rem 1.25rem; margin-bottom: 1.25rem; font-size: 0.85rem;">
                    <div>
                        <div style="font-size: 0.72rem; color: var(--text-dim); text-transform: uppercase;">Fuqaro / Bemor:</div>
                        <div style="font-weight: 800; font-size: 1.05rem; color: #fff;">{{ $activeMed->user?->name ?? 'Bemor' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.72rem; color: var(--text-dim); text-transform: uppercase;">JSHSHIR (PINFL):</div>
                        <div style="font-family: 'JetBrains Mono', monospace; font-weight: 700; color: #38bdf8;">{{ $activeMed->user?->pinfl ?? '32509820010025' }}</div>
                    </div>
                    <div>
                        <div style="font-size: 0.72rem; color: var(--text-dim); text-transform: uppercase;">Qon Guruhi & Rh:</div>
                        <div style="font-weight: 800; color: #ef4444;">{{ $activeMed->blood_group }} ({{ $activeMed->rhesus_factor === 'positive' ? 'Rh+' : 'Rh-' }})</div>
                    </div>
                    <div>
                        <div style="font-size: 0.72rem; color: var(--text-dim); text-transform: uppercase;">Organ Donori Statusi:</div>
                        <div style="font-weight: 700; color: #34d399;">{{ $activeMed->organ_donor ? 'Rozilik berilgan' : 'Belgilanmagan' }}</div>
                    </div>
                </div>

                <!-- Clinical Snapshot -->
                <div style="margin-bottom: 1.25rem; font-size: 0.82rem; line-height: 1.6;">
                    <div style="margin-bottom: 0.5rem;">
                        <strong style="color: #fca5a5;">⚠️ Hayotiy Muhim Allergiyalar:</strong>
                        <span>{{ !empty($activeMed->allergies) ? implode(', ', $activeMed->allergies) : 'Allergik anamnez toza' }}</span>
                    </div>
                    <div style="margin-bottom: 0.5rem;">
                        <strong style="color: #fed7aa;">📋 Surunkali Kasalliklar:</strong>
                        <span>{{ !empty($activeMed->chronic_diseases) ? implode(', ', $activeMed->chronic_diseases) : 'Surunkali kasalliklar mavjud emas' }}</span>
                    </div>
                    <div>
                        <strong>Favqulodda Aloqa Shaxsi:</strong>
                        <span>{{ $activeMed->emergency_contact_name ?? 'Ko\'rsatilmagan' }} ({{ $activeMed->emergency_contact_phone ?? 'Noma\'lum' }})</span>
                    </div>
                </div>

                <!-- Recent Vaccinations Summary -->
                <div style="margin-bottom: 1.25rem;">
                    <div style="font-size: 0.75rem; color: var(--primary-light); font-weight: 700; text-transform: uppercase; margin-bottom: 0.4rem;">Qabul Qilingan Emlashlar (Vaksinalar):</div>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.4rem;">
                        @forelse($activeMed->vaccinations->take(4) as $vac)
                            <span class="tag tag-green" style="font-size: 0.75rem;">💉 {{ $vac->vaccine_name }} ({{ $vac->dose_number }}-doza, {{ $vac->administered_at ? $vac->administered_at->format('d.m.Y') : '' }})</span>
                        @empty
                            <span style="font-size: 0.75rem; color: var(--text-dim);">Emlash qaydlari mavjud emas</span>
                        @endforelse
                    </div>
                </div>

                <!-- Official Electronic Seal / Stamp -->
                <div style="display: flex; justify-content: space-between; align-items: flex-end; border-top: 1px dashed var(--border-color); padding-top: 1rem; margin-top: 1.25rem;">
                    <div style="font-size: 0.72rem; color: var(--text-dim);">
                        Hujjat O'zbekiston E-Health yagona ma'lumotlar bazasi orqali generatsiya qilindi.<br>
                        Verifikatsiya: <span style="color: #38bdf8;">{{ route('med.scan', $activeMed?->qr_token ?? 'default') }}</span>
                    </div>
                    <div style="border: 2px solid #10b981; color: #34d399; font-weight: 900; font-size: 0.7rem; padding: 6px 12px; border-radius: 8px; text-transform: uppercase; letter-spacing: 1px; transform: rotate(-3deg); box-shadow: 0 0 10px rgba(16, 185, 129, 0.2);">
                        ✓ ELEKTRON TASDIQLANGAN
                    </div>
                </div>

            </div>

            <div style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
                <button type="button" class="btn btn-primary" onclick="printPassportOnly()" style="background: linear-gradient(135deg, #0284c7, #0369a1);">
                    <span>🖨️</span> PDF Qilib Chop Etish
                </button>
                <button type="button" class="btn btn-secondary" onclick="sharePassportTelegram()" style="background: rgba(14, 165, 233, 0.15); border-color: rgba(56, 189, 248, 0.4); color: #38bdf8;">
                    <span>✈️</span> Telegramga Yuborish
                </button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('medicalPassportModal')">
                    Yopish
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        let isQrZoomed = false;

        function openQrModal() {
            isQrZoomed = false;
            const img = document.getElementById('qrModalImg');
            const box = document.getElementById('qrModalBox');
            const text = document.getElementById('zoomText');
            const icon = document.getElementById('zoomIcon');
            const imgBox = document.getElementById('qrImgBox');

            if (img) {
                img.style.width = '310px';
                img.style.height = '310px';
            }
            if (box) box.style.maxWidth = '520px';
            if (text) text.innerText = 'Maksimal Kattalashtirish (Zoom)';
            if (icon) icon.innerText = '🔍';
            if (imgBox) imgBox.style.cursor = 'zoom-in';

            openModal('qrPassModal');
        }

        function toggleQrZoom() {
            const img = document.getElementById('qrModalImg');
            const box = document.getElementById('qrModalBox');
            const text = document.getElementById('zoomText');
            const icon = document.getElementById('zoomIcon');
            const imgBox = document.getElementById('qrImgBox');
            if (!img) return;

            isQrZoomed = !isQrZoomed;

            if (isQrZoomed) {
                img.style.width = '420px';
                img.style.height = '420px';
                if (box) box.style.maxWidth = '600px';
                if (text) text.innerText = 'Kichraytirish';
                if (icon) icon.innerText = '🔎';
                if (imgBox) imgBox.style.cursor = 'zoom-out';
            } else {
                img.style.width = '310px';
                img.style.height = '310px';
                if (box) box.style.maxWidth = '520px';
                if (text) text.innerText = 'Maksimal Kattalashtirish (Zoom)';
                if (icon) icon.innerText = '🔍';
                if (imgBox) imgBox.style.cursor = 'zoom-in';
            }
        }

        function printQrPass() {
            const scanUrl = "{{ route('med.scan', $activeMed?->qr_token ?? 'default') }}";
            window.open(scanUrl, '_blank');
        }

        function downloadQrPng() {
            const img = document.getElementById('qrModalImg');
            const a = document.createElement('a');
            a.href = img ? img.src : "https://api.qrserver.com/v1/create-qr-code/?size=500x500&data={{ urlencode(route('med.scan', $activeMed?->qr_token ?? 'default')) }}";
            a.download = "MED_QR_{{ $activeMed?->med_number ?? 'CARD' }}.png";
            a.target = '_blank';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }

        // Online Doctor Queue & Time Slots Scripts
        function openAppointmentModal() {
            openModal('appointmentModal');
            loadDoctorSlots();
        }

        let selectedSlotTime = null;

        async function loadDoctorSlots() {
            const docSelect = document.getElementById('appointmentDoctorSelect');
            const dateInput = document.getElementById('appointmentDateInput');
            const container = document.getElementById('slotsContainer');
            const loader = document.getElementById('slotsLoader');
            const submitBtn = document.getElementById('submitAppointmentBtn');
            const bannerDocName = document.getElementById('bannerDocName');
            const bannerDocClinic = document.getElementById('bannerDocClinic');
            const bannerQueueCount = document.getElementById('bannerQueueCount');
            const selectedSlotBanner = document.getElementById('selectedSlotBanner');

            const doctorId = docSelect.value;
            const date = dateInput.value;

            selectedSlotTime = null;
            document.getElementById('selectedTimeInput').value = '';
            selectedSlotBanner.style.display = 'none';
            submitBtn.disabled = true;

            if (!doctorId || !date) return;

            loader.style.display = 'block';
            container.innerHTML = '';

            try {
                const response = await fetch(`/med/appointments/slots?doctor_id=${doctorId}&date=${date}`);
                const data = await response.json();

                loader.style.display = 'none';

                if (data.status === 'success') {
                    bannerDocName.innerText = data.doctor.name + ' (' + data.doctor.specialty + ')';
                    bannerDocClinic.innerText = '🏥 ' + data.doctor.clinic_name + ' | 🚪 ' + data.doctor.room_number;
                    bannerQueueCount.innerText = `Ushbu kunda band: ${data.total_booked} kishi | Navbat №: ${data.next_queue_number}`;

                    data.slots.forEach(slot => {
                        const btn = document.createElement('div');
                        btn.className = `slot-btn ${slot.available ? 'available' : 'occupied'}`;
                        btn.innerHTML = slot.available
                            ? `<div>${slot.time}</div><div style="font-size:0.65rem; opacity:0.8;">Bo'sh</div>`
                            : `<div>${slot.time}</div><div style="font-size:0.65rem; color:#ef4444;">Band (№${slot.queue_number})</div>`;

                        if (slot.available) {
                            btn.onclick = () => selectTimeSlot(slot.time, data.next_queue_number, btn);
                        }

                        container.appendChild(btn);
                    });
                }
            } catch (err) {
                loader.innerHTML = '<span style="color:#ef4444;">Vaqtlarni yuklashda xatolik.</span>';
            }
        }

        function selectTimeSlot(time, nextQueueNum, btnElement) {
            document.querySelectorAll('.slot-btn').forEach(el => el.classList.remove('selected'));
            btnElement.classList.add('selected');

            selectedSlotTime = time;
            document.getElementById('selectedTimeInput').value = time;
            document.getElementById('selectedSlotDisplay').innerText = time + ' da';
            document.getElementById('estimatedQueueDisplay').innerText = `№ 0${nextQueueNum}`.slice(-4);
            document.getElementById('selectedSlotBanner').style.display = 'flex';
            document.getElementById('submitAppointmentBtn').disabled = false;
            document.getElementById('slotErrorNotice').style.display = 'none';
        }

        function viewTicketModal(ticketCode, queueNum, docName, specialty, clinic, room, date, time, patientName) {
            document.getElementById('modalTicketCode').innerText = ticketCode;
            document.getElementById('modalQueueNumber').innerText = '№ ' + queueNum;
            document.getElementById('modalAppointmentDateTime').innerText = date + ' soat ' + time + ' da';
            document.getElementById('modalPatientName').innerText = patientName;
            document.getElementById('modalDoctorName').innerText = docName;
            document.getElementById('modalSpecialty').innerText = specialty;
            document.getElementById('modalClinic').innerText = clinic;
            document.getElementById('modalRoom').innerText = room;
            openModal('ticketModal');
        }

        function printTicketOnly() {
            const printArea = document.getElementById('printableTicketArea');
            if (!printArea) return;
            const printContent = printArea.innerHTML;
            const iframe = document.createElement('iframe');
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            iframe.style.bottom = '0';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = '0';
            document.body.appendChild(iframe);

            const doc = iframe.contentWindow.document;
            doc.title = 'MED E-Talon — Chop Etish';
            doc.body.style.fontFamily = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
            doc.body.style.margin = '20px';
            doc.body.style.color = '#000';
            doc.body.style.background = '#fff';
            doc.body.innerHTML = '<div style="max-width: 420px; margin: 0 auto; border: 2px dashed #333; padding: 20px; border-radius: 12px;">' + printContent + '</div>';

            iframe.contentWindow.focus();
            setTimeout(() => {
                iframe.contentWindow.print();
                setTimeout(() => { document.body.removeChild(iframe); }, 1500);
            }, 300);
        }

        @if(session('appointment_success'))
            window.addEventListener('DOMContentLoaded', () => {
                const apt = @json(session('appointment_success'));
                viewTicketModal(apt.ticket_number, apt.queue_number, apt.doctor_name, apt.specialty, apt.clinic_name, apt.room_number, apt.date, apt.time, apt.patient_name);
            });
        @endif

        // Tab switching
        function switchTab(evt, tabId) {
            const contents = document.querySelectorAll('.tab-content');
            contents.forEach(el => el.classList.remove('active'));

            const buttons = document.querySelectorAll('.tab-btn');
            buttons.forEach(el => el.classList.remove('active'));

            document.getElementById(tabId).classList.add('active');
            evt.currentTarget.classList.add('active');
        }

        // Modal Helpers
        function openModal(modalId) {
            const m = document.getElementById(modalId);
            if (m) m.classList.add('active');
        }

        function closeModal(modalId) {
            const m = document.getElementById(modalId);
            if (m) m.classList.remove('active');
        }

        // Close on backdrop click
        window.onclick = function(event) {
            if (event.target.classList.contains('modal-backdrop')) {
                event.target.classList.remove('active');
            }
        };

        // ESC key to close any active modal
        window.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                document.querySelectorAll('.modal-backdrop.active').forEach(m => m.classList.remove('active'));
            }
        });

        // 103 Emergency QR Triage Simulation
        async function openEmergencyModal() {
            openModal('emergencyModal');
            const loader = document.getElementById('triageLoading');
            const content = document.getElementById('triageContent');
            loader.style.display = 'block';
            content.style.display = 'none';

            try {
                const response = await fetch('/api/v1/emergency/triage/{{ $activeMed?->qr_token ?? 'default' }}');
                const result = await response.json();

                if (result.status === 'success') {
                    const data = result.data;
                    let allergiesHtml = (data.allergies && data.allergies.length > 0)
                        ? data.allergies.map(a => `<span class="pill-item" style="background:#dc2626; color:#fff; font-size:0.8rem; padding:4px 10px;">⚠️ ${a}</span>`).join(' ')
                        : '<span style="color:#94a3b8;">Xavfli allergiya qayd etilmagan</span>';

                    let chronicHtml = (data.chronic_diseases && data.chronic_diseases.length > 0)
                        ? data.chronic_diseases.map(c => `<span class="pill-item" style="background:#b45309; color:#fff; font-size:0.8rem; padding:4px 10px;">📋 ${c}</span>`).join(' ')
                        : '<span style="color:#94a3b8;">Surunkali kasallik qayd etilmagan</span>';

                    let medsHtml = (data.active_medications && data.active_medications.length > 0)
                        ? data.active_medications.map(m => `<li><strong>${m.medication_name}</strong> (${m.dosage}) — ${m.frequency}</li>`).join('')
                        : '<li>Faol dori vositalari yo\'q</li>';

                    content.innerHTML = `
                        <div style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.3); border-radius: 12px; padding: 1.25rem; margin-bottom: 1.25rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <div style="font-size: 0.75rem; color: #fca5a5; text-transform: uppercase;">103 SHOSHILINCH TEZKOR QIDIRUV</div>
                                    <h3 style="font-size: 1.4rem; font-weight: 800; color: #fff;">${data.patient_name}</h3>
                                    <p style="font-size: 0.8rem; color: #cbd5e1;">Tug'ilgan sana: ${data.birth_date || 'Noma\'lum'} | Jinsi: ${data.gender === 'male' ? 'Erkak' : 'Ayol'}</p>
                                </div>
                                <div class="blood-badge" style="font-size: 1.3rem; padding: 8px 16px;">
                                    🩸 ${data.blood_group} (${data.rhesus_factor === 'positive' ? 'Rh+' : 'Rh-'})
                                </div>
                            </div>
                        </div>

                        <div style="margin-bottom: 1.2rem;">
                            <h4 style="font-size: 0.85rem; color: #f87171; text-transform: uppercase; margin-bottom: 0.5rem;">O'ta Xavfli Allergiyalar:</h4>
                            <div class="pill-list">${allergiesHtml}</div>
                        </div>

                        <div style="margin-bottom: 1.2rem;">
                            <h4 style="font-size: 0.85rem; color: #fbbf24; text-transform: uppercase; margin-bottom: 0.5rem;">Surunkali Kasalliklar:</h4>
                            <div class="pill-list">${chronicHtml}</div>
                        </div>

                        <div style="margin-bottom: 1.2rem;">
                            <h4 style="font-size: 0.85rem; color: #38bdf8; text-transform: uppercase; margin-bottom: 0.5rem;">Hozir Qabul Qilayotgan Dorilari:</h4>
                            <ul style="font-size: 0.85rem; color: #cbd5e1; padding-left: 1.2rem; line-height: 1.6;">
                                ${medsHtml}
                            </ul>
                        </div>

                        <div style="background: rgba(2, 132, 199, 0.1); border-radius: 10px; padding: 1rem; margin-top: 1rem;">
                            <strong style="color: #38bdf8; font-size: 0.85rem;">📞 Shoshilinch Bog'lanish (Yaqini):</strong>
                            <div style="font-size: 1.05rem; font-weight: 700; margin-top: 4px; color: #fff;">
                                ${data.emergency_contact.name || 'Ko\'rsatilmagan'} (${data.emergency_contact.relation || 'Yaqini'}) — 
                                <a href="tel:${data.emergency_contact.phone}" style="color: #38bdf8; text-decoration: none;">${data.emergency_contact.phone || 'Telefon yo\'q'}</a>
                            </div>
                        </div>
                    `;
                    loader.style.display = 'none';
                    content.style.display = 'block';
                }
            } catch (err) {
                loader.innerHTML = '<span style="color: #f87171;">Xatolik: Triage ma\'lumotlarini olib bo\'lmadi.</span>';
            }
        }

        // In-page Toast notification (no browser alert popups)
        function showToast(message, type = 'success') {
            let toast = document.getElementById('appToast');
            if (!toast) {
                toast = document.createElement('div');
                toast.id = 'appToast';
                toast.className = 'toast-notification';
                document.body.appendChild(toast);
            }
            toast.style.background = type === 'error' ? 'rgba(239, 68, 68, 0.95)' : 'rgba(16, 185, 129, 0.95)';
            toast.innerHTML = (type === 'error' ? '⚠️ ' : '✓ ') + message;
            toast.classList.add('show');
            clearTimeout(toast.timer);
            toast.timer = setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }

        // Dispense Rx via API (Instant, no prompt popups)
        async function dispenseRx(rxCode, btn = null) {
            // Find row elements
            const cell = Array.from(document.querySelectorAll('.rx-code')).find(el => el.textContent.trim() === rxCode);
            const row = btn ? btn.closest('tr') : cell?.closest('tr');
            const statusBadge = row ? row.querySelector('.status-badge') : null;
            const actionCell = btn ? btn.parentElement : row?.children[6];

            // 1. Immediately switch UI to dispensed status (Zero delay)
            if (statusBadge) {
                statusBadge.className = 'status-badge status-dispensed';
                statusBadge.innerHTML = '✓ Dorixonada berilgan';
            }
            if (actionCell) {
                const now = new Date();
                const day = String(now.getDate()).padStart(2, '0');
                const month = String(now.getMonth() + 1).padStart(2, '0');
                const year = now.getFullYear();
                actionCell.innerHTML = `<span style="font-size: 0.75rem; color: var(--text-dim);">${day}.${month}.${year}</span>`;
            }

            // 2. Non-blocking toast notification
            showToast(`Retsept ${rxCode}: dori berildi va qayd etildi!`);

            // 3. Background server update
            try {
                const response = await fetch(`/api/v1/prescriptions/${rxCode}/dispense`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                });
                const res = await response.json();
                if (res.status !== 'success') {
                    showToast(res.message || 'Xatolik yuz berdi', 'error');
                }
            } catch (e) {
                showToast('Server bilan bog\'lanishda xatolik yuz berdi.', 'error');
            }
        }

        // PWA Service Worker Registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js').catch(err => console.log('SW error:', err));
            });
        }

        // PWA Install Prompt Listener
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            const pwaBtn = document.getElementById('pwaInstallBtn');
            if (pwaBtn) pwaBtn.style.display = 'inline-flex';
        });

        function installPwa() {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then((choiceResult) => {
                    if (choiceResult.outcome === 'accepted') {
                        showToast('Ilova qurilmangizga muvaffaqiyatli o\'rnatildi!', 'success');
                    }
                    deferredPrompt = null;
                    const pwaBtn = document.getElementById('pwaInstallBtn');
                    if (pwaBtn) pwaBtn.style.display = 'none';
                });
            } else {
                showToast('Ilovani o\'rnatish uchun brauzer menyusidagi "Ilovani o\'rnatish" (Add to Home Screen) tugmasidan foydalaning.', 'info');
            }
        }

        // Telegram Sharing Helpers
        function shareTicketTelegram() {
            const code = document.getElementById('modalTicketCode')?.innerText || 'NAV-TALON';
            const num = document.getElementById('modalQueueNumber')?.innerText || '';
            const dt = document.getElementById('modalAppointmentDateTime')?.innerText || '';
            const doc = document.getElementById('modalDoctorName')?.innerText || '';
            const room = document.getElementById('modalRoom')?.innerText || '';
            const clinic = document.getElementById('modalClinic')?.innerText || '';
            
            const text = `🎫 *O'ZBEKISTON E-NAVAT TALONI*\n\n🔢 *Navbat*: ${num}\n📅 *Qabul vaqti*: ${dt}\n👨‍⚕️ *Shifokor*: ${doc}\n🚪 *Xona*: ${room}\n🏥 *Muassasa*: ${clinic}\n🆔 *Talon kodi*: ${code}\n\nElektron Med-Karta tizimi orqali tasdiqlangan.`;
            const url = window.location.href;
            window.open(`https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(text)}`, '_blank');
        }

        function shareQrTelegram() {
            const scanUrl = "{{ route('med.scan', $activeMed?->qr_token ?? 'default') }}";
            const text = `📱 *{{ $activeMed->user?->name ?? 'Bemor' }} ning Raqamli Med-Karta QR-Pass kodi*:\n\nKasalxona shifokorlari va dorixonalar ushbu havola orqali med-kartani ko'rishlari va retsept bo'yicha dori berishlari mumkin:\n${scanUrl}`;
            window.open(`https://t.me/share/url?url=${encodeURIComponent(scanUrl)}&text=${encodeURIComponent(text)}`, '_blank');
        }

        function sharePassportTelegram() {
            const text = `📄 *{{ $activeMed->user?->name ?? 'Bemor' }} — Elektron Tibbiy Pasport*\n\nQon guruhi: {{ $activeMed?->blood_group }}\nID: {{ $activeMed?->med_number }}\nJSHSHIR: {{ $activeMed?->user?->pinfl ?? '32509820010025' }}\n\nRasmiy elektron tibbiy ma'lumotlar bazasi.`;
            const scanUrl = "{{ route('med.scan', $activeMed?->qr_token ?? 'default') }}";
            window.open(`https://t.me/share/url?url=${encodeURIComponent(scanUrl)}&text=${encodeURIComponent(text)}`, '_blank');
        }

        // AI Consultant Logic
        function setAiSymptom(text) {
            const input = document.getElementById('aiSymptomInput');
            if (input) {
                input.value = text;
                input.focus();
            }
        }

        function runAiConsultant() {
            const input = document.getElementById('aiSymptomInput');
            const text = input ? input.value.trim() : '';
            if (!text) {
                showToast('Iltimos, avval shikoyatingiz yoki simptomlaringizni yozing.', 'error');
                return;
            }

            const loader = document.getElementById('aiLoadingIndicator');
            const resultBox = document.getElementById('aiResultBox');
            const btn = document.getElementById('aiRunBtn');

            btn.disabled = true;
            loader.style.display = 'block';
            resultBox.style.display = 'none';

            setTimeout(() => {
                loader.style.display = 'none';
                btn.disabled = false;

                const lower = text.toLowerCase();
                let triageLevel = 'yellow';
                let title = '🟡 O\'rtacha xavf — Mutaxassis shifokor ko\'rigi tavsiya etiladi';
                let riskBadge = '<span class="badge badge-amber" style="font-size:0.75rem;">🟡 Rejali Shifokor Nazorati</span>';
                let specialty = 'Terapevt';
                let advice = 'Shikoyatlaringiz bo\'yicha qon bosimi va umumiy holatni tekshirish maqsadga muvofiq.';
                let emergencyWarning = '';

                if (lower.includes('yurak') || lower.includes('chap qo\'l') || lower.includes('ko\'krak') || lower.includes('infarkt') || lower.includes('sanchiq')) {
                    triageLevel = 'red';
                    riskBadge = '<span class="badge badge-red" style="font-size:0.75rem; background:#dc2626; color:#fff;">🔴 Yuqori Xavf — Kardiologik Nazorat</span>';
                    title = 'Yurak-qon tomir tizimi zo\'riqishi belgilari';
                    specialty = 'Kardiolog';
                    advice = 'Yurak sohasidagi bosim yoki qisuvchi og\'riq zudlik bilan kardiogramma (EKG) tekshiruvini talab qiladi. Jismoniy zo\'riqishlardan saqlaning.';
                    emergencyWarning = '<div style="background:rgba(239,68,68,0.15); border:1px solid #ef4444; border-radius:8px; padding:8px 12px; margin-top:10px; font-size:0.8rem; color:#fca5a5;">⚠️ Og\'riq kuchayib, nafas qisishi ortsa, kechiktirmasdan <strong>103 Tez Yordam</strong> chaqiring!</div>';
                } else if (lower.includes('bosh') || lower.includes('bosim') || lower.includes('aylan') || lower.includes('ko\'ngil ayn')) {
                    triageLevel = 'yellow';
                    riskBadge = '<span class="badge badge-amber" style="font-size:0.75rem;">🟡 Arterial Gipertoniya Beligilari</span>';
                    title = 'Nevrologik & Qon bosimi disbalansi';
                    specialty = 'Nevrolog';
                    advice = 'Qon bosimini tinch holatda qayta o\'lchang (har 2 soatda). Tuzli va achchiq mahsulotlarni cheklang, yetarli suyuqlik iching va toza havoda dam oling.';
                } else if (lower.includes('gemoglobin') || lower.includes('qon kam') || lower.includes('charchoq') || lower.includes('anemiya')) {
                    triageLevel = 'yellow';
                    riskBadge = '<span class="badge badge-blue" style="font-size:0.75rem;">🔵 Anemiya / Temir Tanqisligi Ehtimoli</span>';
                    title = 'Gematologik tahlil ko\'rsatkichlari';
                    specialty = 'Terapevt';
                    advice = 'Gemoglobin 95-110 g/l oralig\'i yengil/o\'rta darajadagi anemiyaga ishora qiladi. Temirga boy parhez (mol go\'shti, jigar, anor, grechka) va temir preparatlari kursi bo\'yicha shifokor bilan maslahatlashing.';
                } else if (lower.includes('isitma') || lower.includes('yo\'tal') || lower.includes('tomoq') || lower.includes('harorat')) {
                    triageLevel = 'yellow';
                    riskBadge = '<span class="badge badge-amber" style="font-size:0.75rem;">🟡 O\'tkir respirator infeksiya (O\'RVI)</span>';
                    title = 'Shamollash va yuqori harorat sindromi';
                    specialty = 'Terapevt';
                    advice = 'Ko\'p miqdorda iliq suyuqlik (limonli choy, na’matak damlamasi) iching. Harorat 38.5°C dan oshsa, paratsetamol yoki ibuprofen qabul qilish mumkin.';
                } else {
                    triageLevel = 'green';
                    riskBadge = '<span class="badge badge-green" style="font-size:0.75rem;">🟢 Birlamchi Terapiya Tavsiyasi</span>';
                    title = 'Umumiy terapevtik konsultatsiya';
                    specialty = 'Terapevt';
                    advice = 'Kiritilgan simptomlar bo\'yicha umumiy holatingiz tahlil qilindi. To\'liq tashxis qo\'yish uchun terapevt shifokoriga ko\'rinish va umumiy qon tahlili topshirish tavsiya qilinadi.';
                }

                resultBox.innerHTML = `
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem; flex-wrap:wrap; gap:0.5rem;">
                        <div style="font-weight:800; font-size:1.05rem; color:#fff;">${title}</div>
                        <div>${riskBadge}</div>
                    </div>
                    <div style="font-size:0.83rem; line-height:1.6; color:#cbd5e1; margin-bottom:1rem;">
                        ${advice}
                    </div>
                    ${emergencyWarning}
                    <div style="margin-top:1.25rem; padding-top:1rem; border-top:1px solid rgba(255,255,255,0.08); display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:0.75rem;">
                        <div style="font-size:0.8rem; color:var(--text-muted);">
                            Tavsiya etilgan mutaxassis: <strong style="color:var(--primary-light);">${specialty}</strong>
                        </div>
                        <button type="button" class="btn btn-primary" onclick="closeModal('aiConsultantModal'); openAppointmentModal();" style="padding:6px 14px; font-size:0.82rem; background:linear-gradient(135deg, #10b981, #059669);">
                            <span>🎫</span> ${specialty}ga Navbat Olish →
                        </button>
                    </div>
                `;
                resultBox.style.display = 'block';
            }, 700);
        }

        // Pill Tracker Storage & UI
        function getPillStorageKey() {
            const today = new Date().toISOString().slice(0, 10);
            return `med_pills_{{ $activeMed->med_number }}_${today}`;
        }

        function loadPillTrackerState() {
            try {
                const key = getPillStorageKey();
                const saved = JSON.parse(localStorage.getItem(key) || '{}');
                let total = 0;
                let taken = 0;

                document.querySelectorAll('.pill-check-btn').forEach(btn => {
                    total++;
                    const id = btn.getAttribute('data-pill-id');
                    if (id && saved[id]) {
                        taken++;
                        btn.classList.add('active');
                        btn.innerHTML = '<span>✓</span> Qabul qilindi';
                        btn.closest('.pill-item-row')?.classList.add('taken');
                    } else {
                        btn.classList.remove('active');
                        btn.innerHTML = '<span>○</span> Ichildi';
                        btn.closest('.pill-item-row')?.classList.remove('taken');
                    }
                });

                const badge = document.getElementById('pillStatsBadge');
                const bar = document.getElementById('pillProgressBar');
                if (badge) badge.innerText = `${taken} / ${total} ta qabul qilindi`;
                if (bar) {
                    const pct = total > 0 ? Math.round((taken / total) * 100) : 0;
                    bar.style.width = `${pct}%`;
                }
            } catch (e) {
                console.warn('Pill tracker error:', e);
            }
        }

        function togglePillTracker(rxId, period, btn) {
            const key = getPillStorageKey();
            const saved = JSON.parse(localStorage.getItem(key) || '{}');
            const pillKey = `${rxId}_${period}`;

            saved[pillKey] = !saved[pillKey];
            localStorage.setItem(key, JSON.stringify(saved));

            loadPillTrackerState();
            if (saved[pillKey]) {
                showToast('Dori qabul qilinganligi qayd etildi!', 'success');
            }
        }

        // Print Passport Function
        function printPassportOnly() {
            const printArea = document.getElementById('printablePassportArea');
            if (!printArea) return;
            const printContent = printArea.innerHTML;
            const iframe = document.createElement('iframe');
            iframe.style.position = 'fixed';
            iframe.style.right = '0';
            iframe.style.bottom = '0';
            iframe.style.width = '0';
            iframe.style.height = '0';
            iframe.style.border = '0';
            document.body.appendChild(iframe);

            const doc = iframe.contentWindow.document;
            doc.title = 'MED Pasport — Chop Etish';
            doc.body.style.fontFamily = "-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
            doc.body.style.margin = '20px';
            doc.body.style.color = '#000';
            doc.body.style.background = '#fff';
            doc.body.innerHTML = '<div style="max-width: 750px; margin: 0 auto; border: 2px solid #333; padding: 25px; border-radius: 12px;">' + printContent + '</div>';

            iframe.contentWindow.focus();
            setTimeout(() => {
                iframe.contentWindow.print();
                setTimeout(() => { document.body.removeChild(iframe); }, 1500);
            }, 300);
        }

        window.addEventListener('DOMContentLoaded', () => {
            loadPillTrackerState();
        });
    </script>
</body>
</html>
