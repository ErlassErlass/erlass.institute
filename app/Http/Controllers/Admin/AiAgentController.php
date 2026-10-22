<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;

class AiAgentController extends Controller
{
    protected string $gatewayBaseUrl = 'http://127.0.0.1:20128';
    protected string $gatewayApiKey = 'sk-91609b8ccbd0b8c2-8yu4et-3d8f7877';

    /**
     * Authorize access strictly to Webmaster only.
     */
    protected function checkAuthorization()
    {
        $role = auth()->user()->role ?? '';
        if ($role !== 'webmaster') {
            if (request()->expectsJson() || request()->is('*/dispatch') || request()->is('*/ping') || request()->ajax()) {
                abort(response()->json([
                    'success' => false,
                    'ok' => false,
                    'message' => 'Akses ditolak: Fitur ini khusus untuk akun dengan role Webmaster.',
                ], 403));
            }
            abort(403, 'Akses khusus Webmaster.');
        }
    }

    /**
     * Display the AI Agents Squad & 9router Gateway Dashboard.
     */
    public function index()
    {
        $this->checkAuthorization();

        // 1. Health check to 9router AI Gateway
        $startTime = microtime(true);
        $gatewayStatus = [
            'online' => false,
            'latency_ms' => 0,
            'port' => 20128,
            'version' => 'v0.5.95',
            'error' => null,
        ];

        try {
            $healthResponse = Http::timeout(2)->get("{$this->gatewayBaseUrl}/api/health");
            $gatewayStatus['latency_ms'] = round((microtime(true) - $startTime) * 1000);
            if ($healthResponse->successful() && ($healthResponse->json('ok') === true)) {
                $gatewayStatus['online'] = true;
            } else {
                $gatewayStatus['error'] = 'Gateway merespons kode HTTP ' . $healthResponse->status();
            }
        } catch (\Throwable $e) {
            $gatewayStatus['error'] = 'Koneksi ke port 20128 gagal: ' . $e->getMessage();
        }

        // 2. Fetch available models from 9router
        $models = Cache::remember('ai_gateway_models_list', 120, function () {
            try {
                $response = Http::withHeaders([
                    'Authorization' => "Bearer {$this->gatewayApiKey}",
                ])->timeout(3)->get("{$this->gatewayBaseUrl}/v1/models");

                if ($response->successful()) {
                    $json = $response->json();
                    $list = $json['models'] ?? $json['data'] ?? [];
                    return array_map(function ($item) {
                        return [
                            'id' => $item['id'] ?? '',
                            'owned_by' => $item['owned_by'] ?? 'provider',
                            'context_length' => $item['context_length'] ?? null,
                        ];
                    }, $list);
                }
            } catch (\Throwable $e) {
                // fallback
            }
            return [
                ['id' => 'ag/gemini-3.8-flash-high', 'owned_by' => 'Google Gemini (Paid)', 'context_length' => 1000000],
                ['id' => 'cf/@cf/meta/llama-3.3-70b-instruct-fp8-fast', 'owned_by' => 'Cloudflare AI', 'context_length' => 24000],
                ['id' => 'nvidia/deepseek-ai/deepseek-v4-flash', 'owned_by' => 'NVIDIA AI', 'context_length' => 1000000],
            ];
        });

        // 3. Define the 5 Virtual AI Agents
        $agents = [
            [
                'id' => 'squad_leader',
                'number' => 1,
                'name' => 'Squad Leader',
                'title' => 'Chief AI Orchestrator & Strategic Planner',
                'badge' => 'Orkestrator Utama',
                'badge_color' => 'bg-indigo-subtle text-indigo',
                'icon' => 'bi-diagram-3-fill',
                'gradient' => 'linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%)',
                'default_model' => 'ag/gemini-3.8-flash-high',
                'skills' => ['agent-squad', 'concise-planning', 'subagent-orchestrator', 'architect-review', 'brainstorming'],
                'description' => 'Mengoordinasikan kerja 4 agen spesialis, merumuskan peta rencana aksi multi-langkah (actionable checklist), dan mengawasi target operasional.',
                'status' => 'Standby / Active',
                'status_indicator' => 'online',
            ],
            [
                'id' => 'sysops_sentry',
                'number' => 2,
                'name' => 'Sysops Sentry',
                'title' => 'Senior Linux Sysadmin & Server Guardian',
                'badge' => 'Infrastruktur & VPS',
                'badge_color' => 'bg-cyan-subtle text-cyan',
                'icon' => 'bi-shield-shaded',
                'gradient' => 'linear-gradient(135deg, #0284c7 0%, #0369a1 100%)',
                'default_model' => 'ag/gemini-3.8-flash-high',
                'skills' => ['vps-server-management', 'bash-linux', 'security-auditor', 'clean-code-guard'],
                'description' => 'Memantau metrik VPS (RAM, Storage < 75%, Load Average), kesehatan Nginx, PHP-FPM, Docker 9router, Redis, dan menjalankan SOP backup reguler.',
                'status' => 'Monitoring Live',
                'status_indicator' => 'online',
            ],
            [
                'id' => 'qa_detective',
                'number' => 3,
                'name' => 'Log & QA Detective',
                'title' => 'Laravel QA & Error Diagnostic Specialist',
                'badge' => 'Diagnostik & Bug',
                'badge_color' => 'bg-danger-subtle text-danger',
                'icon' => 'bi-bug-fill',
                'gradient' => 'linear-gradient(135deg, #e11d48 0%, #be123c 100%)',
                'default_model' => 'ag/gemini-3.8-flash-high',
                'skills' => ['diagnosing-bugs', 'systematic-debugging', 'tdd', 'code-review', 'clean-code'],
                'description' => 'Menganalisis stack trace laravel.log tanpa tebak-tebakan, melacak akar kegagalan query/controller, dan merancang unit test perbaikan otomatis.',
                'status' => 'Listening Logs',
                'status_indicator' => 'online',
            ],
            [
                'id' => 'data_ops',
                'number' => 4,
                'name' => 'Data Ops Officer',
                'title' => 'Data Integrity & Sync Reconciliation Specialist',
                'badge' => 'Database & Sinkronisasi',
                'badge_color' => 'bg-emerald-subtle text-emerald',
                'icon' => 'bi-database-fill-gear',
                'gradient' => 'linear-gradient(135deg, #059669 0%, #047857 100%)',
                'default_model' => 'ag/gemini-3.8-flash-high',
                'skills' => ['database-design', 'sql-sentinel', 'data-quality-frameworks', 'gsheets-sync'],
                'description' => 'Memastikan integritas database MySQL, kelancaran auto-sync feed 6 tab Google Spreadsheet, dan audit draf invoice operasional.',
                'status' => 'Reconciling Data',
                'status_indicator' => 'online',
            ],
            [
                'id' => 'ui_ux_craftsman',
                'number' => 5,
                'name' => 'UI/UX Craftsman & Frontend Designer',
                'title' => 'Senior Frontend Designer & Design System Architect',
                'badge' => 'UI/UX & Desain Web',
                'badge_color' => 'bg-fuchsia-subtle text-fuchsia',
                'icon' => 'bi-palette-fill',
                'gradient' => 'linear-gradient(135deg, #c026d3 0%, #7e22ce 100%)',
                'default_model' => 'ag/gemini-3.8-flash-high',
                'skills' => ['ui-ux-pro-max', 'frontend-design', 'design-taste-frontend', 'design-system', 'ui-review', 'ui-a11y', 'iconsax-library'],
                'description' => 'Merancang antarmuka Blade modern, CSS design tokens, tipografi harmonis, mikro-interaksi responsif, dan memastikan kepatuhan aksesibilitas (A11y).',
                'status' => 'Design Ready',
                'status_indicator' => 'online',
            ],
        ];

        // 4. Skills directory scan
        $skillsPath = base_path('.agents/skills');
        $skillsList = [];
        if (File::isDirectory($skillsPath)) {
            $dirs = File::directories($skillsPath);
            foreach ($dirs as $dir) {
                $name = basename($dir);
                $hasSkillMd = File::exists($dir . '/SKILL.md');
                $desc = '';
                if ($hasSkillMd) {
                    $content = File::get($dir . '/SKILL.md');
                    if (preg_match('/description:\s*(.+)/i', $content, $m)) {
                        $desc = trim($m[1], " '\"\t\n\r\0\x0B");
                    }
                }
                $skillsList[] = [
                    'name' => $name,
                    'description' => $desc ?: 'Modul instruksi rekayasa terstruktur',
                    'path' => $dir,
                ];
            }
        }

        // 5. Server system overview
        $serverStats = $this->getServerStats();

        return view('admin.ai-agents.index', compact(
            'gatewayStatus',
            'models',
            'agents',
            'skillsList',
            'serverStats'
        ));
    }

    /**
     * AJAX endpoint to ping 9router Gateway in real time.
     */
    public function pingGateway()
    {
        $this->checkAuthorization();

        $startTime = microtime(true);
        try {
            $response = Http::timeout(3)->get("{$this->gatewayBaseUrl}/api/health");
            $latency = round((microtime(true) - $startTime) * 1000);

            if ($response->successful() && ($response->json('ok') === true)) {
                return response()->json([
                    'ok' => true,
                    'latency_ms' => $latency,
                    'port' => 20128,
                    'timestamp' => now()->toIso8601String(),
                    'message' => '9router AI Gateway beroperasi normal.',
                ]);
            }

            return response()->json([
                'ok' => false,
                'latency_ms' => $latency,
                'message' => 'Status kode gateway tidak normal: ' . $response->status(),
            ], 502);
        } catch (\Throwable $e) {
            return response()->json([
                'ok' => false,
                'latency_ms' => round((microtime(true) - $startTime) * 1000),
                'message' => 'Koneksi gagal: ' . $e->getMessage(),
            ], 503);
        }
    }

    /**
     * Dispatch an interactive prompt to a specific agent via 9router.
     */
    public function dispatchTask(Request $request)
    {
        $this->checkAuthorization();

        $validated = $request->validate([
            'agent_id' => 'required|string',
            'prompt' => 'required|string|max:4000',
            'model' => 'nullable|string',
        ]);

        $agentId = $validated['agent_id'];
        $userPrompt = $validated['prompt'];
        $model = $validated['model'] ?: 'ag/gemini-3.8-flash-high';

        // Persona & System Instructions
        $personas = [
            'squad_leader' => [
                'name' => 'Squad Leader (CrewAI Orchestrator)',
                'system' => "Anda adalah Squad Leader CrewAI untuk ekosistem Erlass Institute. Tugas utama Anda adalah merancang rencana eksekusi sistematis, mengurai masalah kompleks menjadi langkah konkret, dan membagi tugas ke agen bawahan (Sysops, QA, Data Ops, UI/UX). Berikan jawaban yang terstruktur rapi dengan markdown, tajam, dan langsung dapat ditindaklanjuti.",
            ],
            'sysops_sentry' => [
                'name' => 'Sysops Sentry (Linux Sysadmin)',
                'system' => "Anda adalah Sysops Sentry untuk server Linux Ubuntu Erlass. Anda bertugas menjaga stabilitas VPS, disk space < 75%, memori, layanan Nginx, PHP 8.3 FPM, Redis, Docker 9router, dan memastikan kepatuhan SOP server. Berikan analisis teknis Linux yang presisi dan aman.",
            ],
            'qa_detective' => [
                'name' => 'Log & QA Detective (Laravel Specialist)',
                'system' => "Anda adalah Log & QA Detective untuk aplikasi web Laravel 11 Erlass. Anda menganalisis error log, mencari akar masalah (root cause) tanpa spekulasi, dan merumuskan langkah penanganan berbasis TDD dan kode bersih. Berikan panduan debugging yang mendalam.",
            ],
            'data_ops' => [
                'name' => 'Data Ops Officer (Database & Sync)',
                'system' => "Anda adalah Data Ops Officer untuk Erlass Institute. Anda bertugas mengawasi integritas data MySQL, sinkronisasi Google Sheets 6 tab, konsistensi billing invoice, dan audit anomali data. Berikan analisis data yang teliti dan berbasis fakta database.",
            ],
            'ui_ux_craftsman' => [
                'name' => 'UI/UX Craftsman & Frontend Designer',
                'system' => "Anda adalah Senior UI/UX Craftsman & Frontend Designer untuk Erlass Institute. Anda menguasai desain visual modern (gradient halus, dark mode elegan, glassmorphism, spasi harmonis), konsistensi Blade templates, micro-animations, dan standar aksesibilitas (WCAG/A11y). Hasilkan saran antarmuka atau komponen kode yang memukau dan siap pakai.",
            ],
        ];

        $persona = $personas[$agentId] ?? [
            'name' => 'AI Specialist',
            'system' => 'Anda adalah asisten AI teknis profesional untuk sistem Erlass Institute.',
        ];

        @set_time_limit(120);
        $startTime = microtime(true);

        try {
            $payload = [
                'model' => $model,
                'messages' => [
                    ['role' => 'system', 'content' => $persona['system']],
                    ['role' => 'user', 'content' => $userPrompt],
                ],
                'temperature' => 0.4,
                'stream' => false,
            ];

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer {$this->gatewayApiKey}",
            ])->timeout(60)->post("{$this->gatewayBaseUrl}/v1/chat/completions", $payload);

            $latency = round((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['choices'][0]['message']['content'] ?? null;
                if (!$reply && isset($data['choices'][0]['delta']['content'])) {
                    $reply = $data['choices'][0]['delta']['content'];
                }
                if (!$reply) {
                    $reply = 'Tidak ada respons teks yang dihasilkan dari model LLM.';
                }
                $usage = $data['usage'] ?? [];

                return response()->json([
                    'success' => true,
                    'agent_name' => $persona['name'],
                    'model' => $model,
                    'reply' => $reply,
                    'latency_ms' => $latency,
                    'tokens' => [
                        'prompt' => $usage['prompt_tokens'] ?? null,
                        'completion' => $usage['completion_tokens'] ?? null,
                        'total' => $usage['total_tokens'] ?? null,
                    ],
                ]);
            }

            $errMsg = $response->json('error.message') ?? $response->json('message') ?? ('Status HTTP ' . $response->status());
            return response()->json([
                'success' => false,
                'message' => '9router AI Gateway merespons error: ' . $errMsg,
                'latency_ms' => $latency,
            ], 502);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghubungi 9router: ' . $e->getMessage(),
                'latency_ms' => round((microtime(true) - $startTime) * 1000),
            ], 500);
        }
    }

    /**
     * Gather basic server telemetry.
     */
    protected function getServerStats(): array
    {
        $freeDisk = disk_free_space('/');
        $totalDisk = disk_total_space('/');
        $usedDisk = $totalDisk - $freeDisk;
        $diskPercent = $totalDisk > 0 ? round(($usedDisk / $totalDisk) * 100, 1) : 0;

        // RAM estimation via /proc/meminfo
        $memTotal = 0;
        $memAvailable = 0;
        if (File::exists('/proc/meminfo')) {
            $meminfo = File::get('/proc/meminfo');
            if (preg_match('/MemTotal:\s+(\d+)\s+kB/', $meminfo, $m)) {
                $memTotal = (int)$m[1] * 1024;
            }
            if (preg_match('/MemAvailable:\s+(\d+)\s+kB/', $meminfo, $m)) {
                $memAvailable = (int)$m[1] * 1024;
            }
        }
        $memUsed = $memTotal - $memAvailable;
        $memPercent = $memTotal > 0 ? round(($memUsed / $memTotal) * 100, 1) : 0;

        return [
            'php_version' => PHP_VERSION,
            'disk_percent' => $diskPercent,
            'disk_used_gb' => round($usedDisk / (1024 * 1024 * 1024), 1),
            'disk_total_gb' => round($totalDisk / (1024 * 1024 * 1024), 1),
            'mem_percent' => $memPercent,
            'mem_used_gb' => round($memUsed / (1024 * 1024 * 1024), 2),
            'mem_total_gb' => round($memTotal / (1024 * 1024 * 1024), 2),
        ];
    }
}
