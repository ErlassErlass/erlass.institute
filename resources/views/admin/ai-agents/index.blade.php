@extends('layouts.app')

@section('title', 'Pusat Komando & Orkestrasi AI Agent Squad')

@push('styles')
<style>
    .ai-hero-card {
        background: radial-gradient(120% 120% at 50% 0%, #1e1b4b 0%, #0f172a 60%, #020617 100%);
        color: #f8fafc;
        border-radius: 1.5rem;
        padding: 2.25rem;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(99, 102, 241, 0.25);
        box-shadow: 0 20px 35px -10px rgba(15, 23, 42, 0.6), 0 0 40px -15px rgba(99, 102, 241, 0.3);
    }
    .ai-hero-card::before {
        content: '';
        position: absolute;
        top: -40%;
        right: -10%;
        width: 420px;
        height: 420px;
        background: radial-gradient(circle, rgba(139, 92, 246, 0.2) 0%, rgba(139, 92, 246, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }
    .ai-hero-card::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: 10%;
        width: 320px;
        height: 320px;
        background: radial-gradient(circle, rgba(6, 182, 212, 0.15) 0%, rgba(6, 182, 212, 0) 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .badge-live-pulse {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(16, 185, 129, 0.15);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
        padding: 0.4rem 0.85rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        backdrop-filter: blur(8px);
    }
    .pulse-dot-green {
        width: 8px;
        height: 8px;
        background-color: #10b981;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        animation: pulse-green 1.8s infinite cubic-bezier(0.66, 0, 0, 1);
    }
    .pulse-dot-red {
        width: 8px;
        height: 8px;
        background-color: #ef4444;
        border-radius: 50%;
        box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
        animation: pulse-red 1.8s infinite cubic-bezier(0.66, 0, 0, 1);
    }
    @keyframes pulse-green {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }
    @keyframes pulse-red {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    .stat-glass-pill {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(12px);
        border-radius: 1rem;
        padding: 0.85rem 1.25rem;
        transition: all 0.25s ease;
    }
    .stat-glass-pill:hover {
        background: rgba(255, 255, 255, 0.1);
        border-color: rgba(255, 255, 255, 0.25);
        transform: translateY(-2px);
    }

    .agent-card {
        border-radius: 1.25rem;
        border: 1px solid #e2e8f0;
        background: #ffffff;
        transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .agent-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 28px -8px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1;
    }
    .agent-card-header {
        padding: 1.5rem 1.5rem 1rem 1.5rem;
        position: relative;
    }
    .agent-card-body {
        padding: 0 1.5rem 1.25rem 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .agent-card-footer {
        padding: 1rem 1.5rem 1.25rem 1.5rem;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        border-radius: 0 0 1.25rem 1.25rem;
    }

    .agent-avatar {
        width: 54px;
        height: 54px;
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        box-shadow: 0 8px 16px -4px rgba(0, 0, 0, 0.18);
    }

    .skill-chip {
        display: inline-block;
        font-size: 0.72rem;
        padding: 0.2rem 0.6rem;
        background: #f1f5f9;
        color: #475569;
        border-radius: 0.5rem;
        margin-right: 0.3rem;
        margin-bottom: 0.35rem;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        border: 1px solid #e2e8f0;
    }
    .skill-chip-ui {
        background: #fdf4ff;
        color: #a21caf;
        border-color: #f5d0fe;
    }

    .console-panel {
        background: #0b1120;
        border: 1px solid #1e293b;
        border-radius: 1.25rem;
        color: #f1f5f9;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
    }
    .console-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #1e293b;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .console-dots {
        display: flex;
        gap: 6px;
    }
    .console-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
    }
    .console-dot.red { background: #ef4444; }
    .console-dot.yellow { background: #f59e0b; }
    .console-dot.green { background: #10b981; }

    .quick-prompt-btn {
        background: rgba(255, 255, 255, 0.05);
        color: #cbd5e1;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 2rem;
        padding: 0.35rem 0.85rem;
        font-size: 0.78rem;
        transition: all 0.2s ease;
        text-align: left;
    }
    .quick-prompt-btn:hover {
        background: rgba(99, 102, 241, 0.25);
        border-color: #818cf8;
        color: #ffffff;
    }

    .output-container {
        min-height: 200px;
        max-height: 520px;
        overflow-y: auto;
        background: #020617;
        border-radius: 0.85rem;
        border: 1px solid #1e293b;
        padding: 1.25rem;
        font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        font-size: 0.9rem;
        line-height: 1.65;
        color: #e2e8f0;
    }
    .output-container pre {
        background: #0f172a;
        padding: 1rem;
        border-radius: 0.5rem;
        border: 1px solid #334155;
        color: #38bdf8;
    }
    .output-container code {
        color: #f472b6;
        font-family: ui-monospace, Menlo, monospace;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4 py-4">

    <!-- 1. HERO HEADER: PUSAT KOMANDO AI AGENTS -->
    <div class="ai-hero-card mb-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span id="gateway-badge-container">
                        @if($gatewayStatus['online'])
                            <span class="badge-live-pulse">
                                <span class="pulse-dot-green"></span>
                                9ROUTER GATEWAY LIVE (PORT {{ $gatewayStatus['port'] }})
                            </span>
                        @else
                            <span class="badge-live-pulse text-danger border-danger" style="background: rgba(239, 68, 68, 0.15);">
                                <span class="pulse-dot-red"></span>
                                GATEWAY DISCONNECTED
                            </span>
                        @endif
                    </span>
                    <span class="badge bg-white bg-opacity-10 text-white rounded-pill px-3 py-1 fw-bold" style="font-size: 0.72rem; letter-spacing: 0.05em;">
                        CREWAI SQUAD v1.0
                    </span>
                    <span class="badge bg-warning bg-opacity-20 text-warning border border-warning border-opacity-30 rounded-pill px-3 py-1 fw-bold" style="font-size: 0.72rem;">
                        5 AGENTS REGISTERED
                    </span>
                </div>
                <h2 class="fw-bolder mb-2 text-white display-6">Pusat Komando & Orkestrasi AI Squad</h2>
                <p class="text-slate-300 mb-0 fs-6" style="max-width: 680px; color: #94a3b8;">
                    Arsitektur kolaborasi 5 agen cerdas terpadu di server Erlass Institute. Ditenagai 9router AI Gateway dengan 2 akun Gemini berbayar, provider fallback Cloudflare & NVIDIA, serta integrasi modul skill rekayasa modern.
                </p>
            </div>

            <div class="col-lg-5">
                <div class="row g-2">
                    <div class="col-6">
                        <div class="stat-glass-pill">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-slate-400 small text-uppercase" style="font-size: 0.7rem; color: #94a3b8;">Latensi Gateway</span>
                                <i class="bi bi-speedometer2 text-info"></i>
                            </div>
                            <div class="h4 fw-bold text-white mb-0" id="hero-latency-val">
                                {{ $gatewayStatus['online'] ? $gatewayStatus['latency_ms'] . ' ms' : 'Offline' }}
                            </div>
                            <div class="small text-emerald-400 mt-1" style="color: #34d399; font-size: 0.72rem;">
                                Endpoint lokal 127.0.0.1
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="stat-glass-pill">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-slate-400 small text-uppercase" style="font-size: 0.7rem; color: #94a3b8;">Model Tersedia</span>
                                <i class="bi bi-cpu-fill text-warning"></i>
                            </div>
                            <div class="h4 fw-bold text-white mb-0">
                                {{ count($models) }} Model
                            </div>
                            <div class="small text-slate-300 mt-1" style="color: #94a3b8; font-size: 0.72rem;">
                                Gemini, Llama, DeepSeek
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="stat-glass-pill">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-slate-400 small text-uppercase" style="font-size: 0.7rem; color: #94a3b8;">Akun Gemini</span>
                                <i class="bi bi-gem text-primary"></i>
                            </div>
                            <div class="h5 fw-bold text-white mb-0">2 Akun Paid</div>
                            <div class="small text-slate-300 mt-1 text-truncate" style="color: #94a3b8; font-size: 0.72rem;">
                                Flash High Reasoning
                            </div>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="stat-glass-pill">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="text-slate-400 small text-uppercase" style="font-size: 0.7rem; color: #94a3b8;">Skill Modul</span>
                                <i class="bi bi-tools text-success"></i>
                            </div>
                            <div class="h5 fw-bold text-white mb-0">{{ count($skillsList) }} Skill</div>
                            <div class="small text-slate-300 mt-1" style="color: #94a3b8; font-size: 0.72rem;">
                                Matt Pocock & UI/UX
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3 text-end">
                    <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3" id="btn-ping-gateway" onclick="pingAiGateway()">
                        <i class="bi bi-arrow-repeat me-1" id="ping-spinner"></i> Tes Ping 9router Sekarang
                    </button>
                    <a href="#console-section" class="btn btn-primary btn-sm rounded-pill px-3 ms-2 shadow-sm">
                        <i class="bi bi-terminal-fill me-1"></i> Buka Konsol Perintah
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. STATUS SERVER TELEMETRI RINGKAS -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-3 bg-primary bg-opacity-10 text-primary fs-4">
                        <i class="bi bi-hdd-fill"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small fw-semibold">Penyimpanan VPS</span>
                            <span class="small fw-bold {{ $serverStats['disk_percent'] > 75 ? 'text-danger' : 'text-success' }}">
                                {{ $serverStats['disk_percent'] }}%
                            </span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar {{ $serverStats['disk_percent'] > 75 ? 'bg-danger' : 'bg-primary' }}" 
                                 role="progressbar" style="width: {{ $serverStats['disk_percent'] }}%"></div>
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.7rem;">
                            {{ $serverStats['disk_used_gb'] }} GB / {{ $serverStats['disk_total_gb'] }} GB (SOP < 75%)
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-3 bg-info bg-opacity-10 text-info fs-4">
                        <i class="bi bi-memory"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-muted small fw-semibold">Memori RAM</span>
                            <span class="small fw-bold text-info">{{ $serverStats['mem_percent'] }}%</span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" role="progressbar" style="width: {{ $serverStats['mem_percent'] }}%"></div>
                        </div>
                        <div class="text-muted mt-1" style="font-size: 0.7rem;">
                            {{ $serverStats['mem_used_gb'] }} GB / {{ $serverStats['mem_total_gb'] }} GB Terpakai
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-3 bg-success bg-opacity-10 text-success fs-4">
                        <i class="bi bi-boxes"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Docker 9router Gateway</div>
                        <div class="fw-bold text-dark fs-6">Container v0.5.95</div>
                        <div class="text-success small fw-semibold" style="font-size: 0.72rem;">
                            <i class="bi bi-check-circle-fill me-1"></i> Running (Bridge Localhost)
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3 bg-white">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-3 rounded-3 bg-secondary bg-opacity-10 text-secondary fs-4">
                        <i class="bi bi-filetype-php"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-semibold">Runtime Environment</div>
                        <div class="fw-bold text-dark fs-6">PHP {{ $serverStats['php_version'] }}</div>
                        <div class="text-muted small" style="font-size: 0.72rem;">
                            Laravel {{ app()->version() }} & Nginx FPM
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. SQUAD AGENT CARDS (5 KARYAWAN VIRTUAL) -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="fw-bold mb-1 text-dark">
                <i class="bi bi-people-fill text-primary me-2"></i>Daftar 5 Karyawan Virtual (AI Agent Squad)
            </h4>
            <p class="text-muted mb-0 small">
                Setiap agen memiliki persona terdedikasi, wewenang spesifik, dan kumpulan skill rekayasa yang siap dieksekusi.
            </p>
        </div>
        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2 fw-semibold">
            Status: Semua Agen Standby & Terhubung
        </span>
    </div>

    <div class="row g-4 mb-5">
        @foreach($agents as $agent)
        <div class="col-xl-4 col-md-6">
            <div class="agent-card shadow-sm">
                <div class="agent-card-header">
                    <div class="d-flex align-items-start justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            <div class="agent-avatar" style="background: {{ $agent['gradient'] }};">
                                <i class="bi {{ $agent['icon'] }}"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge rounded-pill fw-bold" style="background: rgba(0,0,0,0.06); color: #334155; font-size: 0.68rem;">
                                        AGEN #{{ $agent['number'] }}
                                    </span>
                                    <span class="badge {{ $agent['badge_color'] }} rounded-pill" style="font-size: 0.68rem;">
                                        {{ $agent['badge'] }}
                                    </span>
                                </div>
                                <h5 class="fw-bold mb-0 text-dark">{{ $agent['name'] }}</h5>
                            </div>
                        </div>
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill fw-bold" style="font-size: 0.7rem;">
                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i> READY
                        </span>
                    </div>
                    <div class="text-muted small mt-2 fw-semibold" style="font-size: 0.8rem;">
                        {{ $agent['title'] }}
                    </div>
                </div>

                <div class="agent-card-body">
                    <p class="text-secondary small mb-3" style="line-height: 1.5; font-size: 0.84rem;">
                        {{ $agent['description'] }}
                    </p>

                    <div class="mt-auto">
                        <div class="text-muted fw-bold text-uppercase mb-2" style="font-size: 0.68rem; letter-spacing: 0.04em;">
                            Skill Terkoneksi (Local Repository):
                        </div>
                        <div>
                            @foreach($agent['skills'] as $skill)
                                <span class="skill-chip {{ $agent['id'] === 'ui_ux_craftsman' ? 'skill-chip-ui' : '' }}">
                                    {{ $skill }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="agent-card-footer d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted" style="font-size: 0.68rem;">Mesin Rekomendasi:</div>
                        <code class="text-primary fw-semibold" style="font-size: 0.75rem;">{{ $agent['default_model'] }}</code>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" 
                            onclick="selectAgentForDispatch('{{ $agent['id'] }}', '{{ $agent['name'] }}')">
                        <i class="bi bi-chat-left-dots-fill me-1"></i> Perintahkan
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- 4. KONSOL DISPATCHER & PENGUJIAN AGEN INTERAKTIF -->
    <div id="console-section" class="console-panel mb-5 p-0">
        <div class="console-header">
            <div class="d-flex align-items-center gap-3">
                <div class="console-dots">
                    <div class="console-dot red"></div>
                    <div class="console-dot yellow"></div>
                    <div class="console-dot green"></div>
                </div>
                <div class="fw-bold text-white small" style="letter-spacing: 0.05em;">
                    <i class="bi bi-terminal me-1 text-info"></i> TERMINAL PUSAT DISPATCH & PENGUJIAN AGEN (9ROUTER API)
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-slate-800 text-slate-300 border border-slate-700 rounded-pill small" style="background: #1e293b; color: #94a3b8;">
                    <i class="bi bi-shield-lock-fill text-warning me-1"></i> Guardrails Active
                </span>
            </div>
        </div>

        <div class="p-4">
            <div class="row g-4">
                <!-- FORM INPUT PERINTAH -->
                <div class="col-lg-5">
                    <form id="form-dispatch" onsubmit="handleDispatch(event)">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-slate-300 small fw-bold text-uppercase" style="color: #cbd5e1; font-size: 0.72rem;">
                                1. Pilih Karyawan Virtual (Target Agent)
                            </label>
                            <select class="form-select bg-dark text-white border-secondary rounded-3" id="select-agent" required>
                                <option value="squad_leader">👑 1. Squad Leader (CrewAI Orchestrator)</option>
                                <option value="sysops_sentry">🛡️ 2. Sysops Sentry (Linux Sysadmin)</option>
                                <option value="qa_detective">🔍 3. Log & QA Detective (Laravel Specialist)</option>
                                <option value="data_ops">📊 4. Data Ops Officer (Database & Sync)</option>
                                <option value="ui_ux_craftsman" selected>🎨 5. UI/UX Craftsman & Frontend Designer</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-slate-300 small fw-bold text-uppercase" style="color: #cbd5e1; font-size: 0.72rem;">
                                2. Pilih Model LLM (Via 9router Gateway)
                            </label>
                            <select class="form-select bg-dark text-white border-secondary rounded-3" id="select-model">
                                <option value="ag/gemini-3.8-flash-high" selected>⭐ ag/gemini-3.8-flash-high (Gemini Berbayar - Teruji)</option>
                                <option value="cf/@cf/meta/llama-3.3-70b-instruct-fp8-fast">⚡ cf/@cf/meta/llama-3.3-70b-instruct-fp8-fast (Cloudflare)</option>
                                <option value="nvidia/deepseek-ai/deepseek-v4-flash">🧠 nvidia/deepseek-ai/deepseek-v4-flash (NVIDIA)</option>
                                <option value="cf/@cf/deepseek-ai/deepseek-r1-distill-qwen-32b">🔬 cf/@cf/deepseek-ai/deepseek-r1-distill-qwen-32b (Reasoning)</option>
                                @foreach($models as $m)
                                    @if(!in_array($m['id'], ['ag/gemini-3.8-flash-high', 'cf/@cf/meta/llama-3.3-70b-instruct-fp8-fast', 'nvidia/deepseek-ai/deepseek-v4-flash', 'cf/@cf/deepseek-ai/deepseek-r1-distill-qwen-32b']))
                                        <option value="{{ $m['id'] }}">{{ $m['id'] }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label text-slate-300 small fw-bold text-uppercase mb-0" style="color: #cbd5e1; font-size: 0.72rem;">
                                    Preset Tugas Cepat:
                                </label>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <button type="button" class="quick-prompt-btn" 
                                        onclick="applyQuickPrompt('ui_ux_craftsman', 'Berikan saran styling CSS dan perbaikan micro-interactions untuk tabel invoice agar terkesan modern, elegan, dan ramah pengguna.')">
                                    🎨 Saran Styling UI/UX
                                </button>
                                <button type="button" class="quick-prompt-btn" 
                                        onclick="applyQuickPrompt('sysops_sentry', 'Jelaskan prosedur pengecekan kapasitas storage VPS dan langkah preventif sebelum disk mencapai batas bahaya 75%.')">
                                    🛡️ SOP Storage Sysops
                                </button>
                                <button type="button" class="quick-prompt-btn" 
                                        onclick="applyQuickPrompt('qa_detective', 'Bagaimana urutan diagnosa standar ketika muncul error 500 pada controller Laravel tanpa melakukan tebak-tebakan?')">
                                    🔍 Diagnosa Bug QA
                                </button>
                                <button type="button" class="quick-prompt-btn" 
                                        onclick="applyQuickPrompt('data_ops', 'Tuliskan rancangan query MySQL untuk mendeteksi inkonsistensi status invoice pending terhadap jadwal sesi mengajar.')">
                                    📊 Audit Data Ops
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-slate-300 small fw-bold text-uppercase" style="color: #cbd5e1; font-size: 0.72rem;">
                                3. Tulis Instruksi / Perintah
                            </label>
                            <textarea class="form-control bg-dark text-white border-secondary rounded-3" 
                                      id="textarea-prompt" rows="4" 
                                      placeholder="Ketik instruksi untuk agen di sini..." required>Buatkan rekomendasi komponen Blade card yang elegan dan modern untuk menampilkan status sinkronisasi AI Agent Squad.</textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary rounded-3 py-2 fw-bold" id="btn-submit-dispatch">
                                <span id="dispatch-spinner" class="spinner-border spinner-border-sm me-2 d-none" role="status"></span>
                                <i class="bi bi-send-fill me-1" id="dispatch-icon"></i> Kirim Perintah ke Agen
                            </button>
                        </div>
                    </form>
                </div>

                <!-- HASIL OUTPUT DISPATCH -->
                <div class="col-lg-7">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary bg-opacity-20 text-primary border border-primary border-opacity-30 rounded-pill px-3 py-1" id="output-agent-badge">
                                🎨 UI/UX Craftsman
                            </span>
                            <span class="text-slate-400 small" id="output-meta" style="color: #94a3b8; font-size: 0.75rem;">
                                Menunggu instruksi...
                            </span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 text-slate-300" onclick="copyConsoleOutput()">
                            <i class="bi bi-clipboard me-1"></i> Salin Respons
                        </button>
                    </div>

                    <div class="output-container" id="console-output">
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-cpu fs-1 d-block mb-2 text-slate-600" style="color: #475569;"></i>
                            <p class="mb-0 small" style="color: #64748b;">
                                Pilih agen di panel kiri, tentukan instruksi kerja, lalu klik <strong>"Kirim Perintah ke Agen"</strong>.<br>
                                Respons lengkap dari 9router AI Gateway akan ditampilkan secara langsung di sini.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. DAFTAR INVENTARIS SKILL LOKAL -->
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-folder-symlink-fill text-warning me-2"></i>Inventaris Modul Skill Rekayasa & UI/UX Terpasang
                </h5>
                <p class="text-muted mb-0 small">
                    Direktori: <code>/var/www/webapperlass/.agents/skills/</code> (Total: {{ count($skillsList) }} Modul Skill Terdeteksi)
                </p>
            </div>
            <input type="text" id="skill-search-input" class="form-control form-control-sm rounded-pill" 
                   style="max-width: 250px;" placeholder="Cari nama skill..." onkeyup="filterSkillsTable()">
        </div>

        <div class="table-responsive" style="max-height: 380px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0" id="skills-table">
                <thead class="table-light sticky-top">
                    <tr>
                        <th style="width: 28%;">Nama Skill</th>
                        <th style="width: 20%;">Kategori / Agen Terkait</th>
                        <th style="width: 52%;">Deskripsi Modul</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($skillsList as $sk)
                        <tr>
                            <td>
                                <code class="fw-bold text-primary">{{ $sk['name'] }}</code>
                            </td>
                            <td>
                                @if(str_contains($sk['name'], 'ui') || str_contains($sk['name'], 'design') || str_contains($sk['name'], 'banner') || str_contains($sk['name'], 'slide'))
                                    <span class="badge bg-fuchsia-subtle text-fuchsia rounded-pill" style="background: #fdf4ff; color: #a21caf;">
                                        🎨 UI/UX & Frontend
                                    </span>
                                @elseif(str_contains($sk['name'], 'bug') || str_contains($sk['name'], 'review') || str_contains($sk['name'], 'tdd'))
                                    <span class="badge bg-danger-subtle text-danger rounded-pill" style="background: #fef2f2; color: #b91c1c;">
                                        🔍 QA & Code Review
                                    </span>
                                @elseif(str_contains($sk['name'], 'git') || str_contains($sk['name'], 'vps') || str_contains($sk['name'], 'sys'))
                                    <span class="badge bg-cyan-subtle text-cyan rounded-pill" style="background: #ecfeff; color: #0e7490;">
                                        🛡️ Sysops & Infra
                                    </span>
                                @elseif(str_contains($sk['name'], 'data') || str_contains($sk['name'], 'sql') || str_contains($sk['name'], 'sheet'))
                                    <span class="badge bg-emerald-subtle text-emerald rounded-pill" style="background: #ecfdf5; color: #047857;">
                                        📊 Data Ops
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill">
                                        👑 Core / Orchestrator
                                    </span>
                                @endif
                            </td>
                            <td class="small text-secondary">
                                {{ $sk['description'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">
                                Tidak ada modul skill terdeteksi di <code>.agents/skills</code>.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function selectAgentForDispatch(agentId, agentName) {
        const select = document.getElementById('select-agent');
        if (select) {
            select.value = agentId;
        }
        const consoleSection = document.getElementById('console-section');
        if (consoleSection) {
            consoleSection.scrollIntoView({ behavior: 'smooth' });
        }
    }

    function applyQuickPrompt(agentId, promptText) {
        document.getElementById('select-agent').value = agentId;
        document.getElementById('textarea-prompt').value = promptText;
        document.getElementById('textarea-prompt').focus();
    }

    async function pingAiGateway() {
        const spinner = document.getElementById('ping-spinner');
        const latencyEl = document.getElementById('hero-latency-val');
        const badgeContainer = document.getElementById('gateway-badge-container');

        if (spinner) spinner.classList.add('spin-animation');

        try {
            const res = await fetch("{{ route('admin.ai-agents.ping') }}", {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            let data;
            const contentType = res.headers.get('content-type') || '';
            if (contentType.includes('application/json')) {
                data = await res.json();
            } else {
                const rawText = await res.text();
                let errMsg = `Server HTTP ${res.status}`;
                if (res.status === 403) errMsg = 'Akses ditolak: Khusus akun Webmaster.';
                throw new Error(errMsg);
            }

            if (data.ok) {
                if (latencyEl) latencyEl.innerText = data.latency_ms + ' ms';
                if (badgeContainer) {
                    badgeContainer.innerHTML = `
                        <span class="badge-live-pulse">
                            <span class="pulse-dot-green"></span>
                            9ROUTER GATEWAY LIVE (PORT ${data.port})
                        </span>
                    `;
                }
                alert('🟢 9router Gateway Siap!\nLatensi: ' + data.latency_ms + ' ms\nWaktu: ' + data.timestamp);
            } else {
                if (latencyEl) latencyEl.innerText = 'Error';
                alert('⚠️ Gangguan Gateway: ' + (data.message || 'Status tidak normal'));
            }
        } catch (e) {
            alert('❌ Gagal menghubungi endpoint ping: ' + e.message);
        } finally {
            if (spinner) spinner.classList.remove('spin-animation');
        }
    }

    async function handleDispatch(e) {
        e.preventDefault();

        const agentSelect = document.getElementById('select-agent');
        const modelSelect = document.getElementById('select-model');
        const promptInput = document.getElementById('textarea-prompt');

        const agentId = agentSelect.value;
        const agentText = agentSelect.options[agentSelect.selectedIndex].text;
        const model = modelSelect.value;
        const prompt = promptInput.value.trim();

        if (!prompt) return;

        const btnSubmit = document.getElementById('btn-submit-dispatch');
        const spinner = document.getElementById('dispatch-spinner');
        const icon = document.getElementById('dispatch-icon');
        const outputEl = document.getElementById('console-output');
        const badgeEl = document.getElementById('output-agent-badge');
        const metaEl = document.getElementById('output-meta');

        btnSubmit.disabled = true;
        spinner.classList.remove('d-none');
        icon.classList.add('d-none');

        badgeEl.innerText = agentText;
        metaEl.innerText = 'Sedang mengeksekusi instruksi via 9router (' + model + ')...';
        outputEl.innerHTML = `
            <div class="d-flex align-items-center gap-3 py-4 text-info">
                <div class="spinner-border text-info" role="status"></div>
                <div>
                    <div class="fw-bold">Menghubungi AI Gateway...</div>
                    <div class="small text-slate-400" style="color: #94a3b8;">Agen sedang memproses instruksi dengan model ${model}</div>
                </div>
            </div>
        `;

        try {
            const res = await fetch("{{ route('admin.ai-agents.dispatch') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    agent_id: agentId,
                    model: model,
                    prompt: prompt
                })
            });

            let data;
            const contentType = res.headers.get('content-type') || '';
            if (contentType.includes('application/json')) {
                data = await res.json();
            } else {
                const rawText = await res.text();
                let cleanMsg = `Server merespons kode HTTP ${res.status}`;
                if (res.status === 403) {
                    cleanMsg = 'Akses ditolak: Fitur ini khusus akun dengan role Webmaster.';
                } else if (res.status === 419) {
                    cleanMsg = 'Sesi web kedaluwarsa. Silakan refresh halaman dan coba kembali.';
                } else if (res.status === 502 || res.status === 504) {
                    cleanMsg = 'AI Gateway atau server sedang sibuk/merespons lambat (HTTP ' + res.status + '). Silakan coba sesaat lagi.';
                }
                throw new Error(cleanMsg);
            }

            if (data.success) {
                const totalTokens = data.tokens?.total ? ` | ${data.tokens.total} Tokens` : '';
                metaEl.innerHTML = `<span class="text-success"><i class="bi bi-check2-circle me-1"></i>Selesai (${data.latency_ms} ms${totalTokens})</span>`;
                
                // Format response
                const formattedHtml = formatMarkdownToHtml(data.reply);
                outputEl.innerHTML = formattedHtml;
            } else {
                metaEl.innerHTML = `<span class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Gagal (${data.latency_ms || 0} ms)</span>`;
                outputEl.innerHTML = `
                    <div class="alert alert-danger mb-0" style="background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.3); color: #fca5a5;">
                        <h6 class="fw-bold mb-1"><i class="bi bi-x-circle-fill me-1"></i> Error dari Gateway:</h6>
                        <div class="small">${escapeHtml(data.message || 'Terjadi kesalahan internal.')}</div>
                    </div>
                `;
            }
        } catch (err) {
            metaEl.innerHTML = `<span class="text-danger"><i class="bi bi-x-circle me-1"></i>Gagal Terhubung</span>`;
            outputEl.innerHTML = `
                <div class="alert alert-danger mb-0" style="background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.3); color: #fca5a5;">
                    <div class="fw-bold mb-1">Gagal mengirim permintaan:</div>
                    <div class="small">${escapeHtml(err.message)}</div>
                </div>
            `;
        } finally {
            btnSubmit.disabled = false;
            spinner.classList.add('d-none');
            icon.classList.remove('d-none');
        }
    }

    function formatMarkdownToHtml(text) {
        if (!text) return '';
        let escaped = escapeHtml(text);
        
        // Code blocks
        escaped = escaped.replace(/```([a-z0-9]*)\n([\s\S]*?)```/g, function(match, lang, code) {
            return `<pre><code class="language-${lang}">${code}</code></pre>`;
        });
        
        // Inline code
        escaped = escaped.replace(/`([^`]+)`/g, '<code>$1</code>');
        
        // Headers
        escaped = escaped.replace(/^### (.*$)/gim, '<h6 class="fw-bold text-info mt-3 mb-2">$1</h6>');
        escaped = escaped.replace(/^## (.*$)/gim, '<h5 class="fw-bold text-white mt-3 mb-2">$1</h5>');
        escaped = escaped.replace(/^# (.*$)/gim, '<h4 class="fw-bold text-white mt-3 mb-2">$1</h4>');
        
        // Bold & Italic
        escaped = escaped.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
        escaped = escaped.replace(/\*([^*]+)\*/g, '<em>$1</em>');
        
        // Lists
        escaped = escaped.replace(/^\s*-\s+(.*$)/gim, '<li class="ms-3 mb-1">$1</li>');
        escaped = escaped.replace(/^\s*\*\s+(.*$)/gim, '<li class="ms-3 mb-1">$1</li>');
        
        // Newlines to breaks where appropriate
        escaped = escaped.replace(/\n\n/g, '<br><br>');
        
        return escaped;
    }

    function escapeHtml(string) {
        const entityMap = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        };
        return String(string).replace(/[&<>"']/g, function (s) {
            return entityMap[s];
        });
    }

    function copyConsoleOutput() {
        const outputEl = document.getElementById('console-output');
        if (!outputEl) return;
        const text = outputEl.innerText;
        navigator.clipboard.writeText(text).then(() => {
            alert('📋 Respons berhasil disalin ke clipboard!');
        }).catch(() => {
            alert('Gagal menyalin ke clipboard.');
        });
    }

    function filterSkillsTable() {
        const input = document.getElementById('skill-search-input');
        const filter = input.value.toLowerCase();
        const table = document.getElementById('skills-table');
        const tr = table.getElementsByTagName('tr');

        for (let i = 1; i < tr.length; i++) {
            const tdName = tr[i].getElementsByTagName('td')[0];
            const tdDesc = tr[i].getElementsByTagName('td')[2];
            if (tdName || tdDesc) {
                const nameVal = tdName ? tdName.textContent || tdName.innerText : '';
                const descVal = tdDesc ? tdDesc.textContent || tdDesc.innerText : '';
                if (nameVal.toLowerCase().indexOf(filter) > -1 || descVal.toLowerCase().indexOf(filter) > -1) {
                    tr[i].style.display = '';
                } else {
                    tr[i].style.display = 'none';
                }
            }
        }
    }
</script>
<style>
    .spin-animation {
        animation: spin 1s linear infinite;
    }
    @keyframes spin {
        100% { transform: rotate(360deg); }
    }
</style>
@endpush
