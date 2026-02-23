<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/health', function () {
    $checks = [];
    $overall_status = 'healthy';

    // Database connectivity check
    try {
        DB::connection()->getPdo();
        $checks['database'] = [
            'status' => 'healthy',
            'message' => 'Database connection successful',
        ];
    } catch (Exception $e) {
        $checks['database'] = [
            'status' => 'unhealthy',
            'message' => 'Database connection failed: '.$e->getMessage(),
        ];
        $overall_status = 'unhealthy';
    }

    // Cache connectivity check
    try {
        $test_key = 'health_check_'.time();
        Cache::put($test_key, 'test', 1);
        $cached_value = Cache::get($test_key);
        Cache::forget($test_key);

        if ($cached_value === 'test') {
            $checks['cache'] = [
                'status' => 'healthy',
                'message' => 'Cache system working',
            ];
        } else {
            throw new Exception('Cache read/write failed');
        }
    } catch (Exception $e) {
        $checks['cache'] = [
            'status' => 'unhealthy',
            'message' => 'Cache system failed: '.$e->getMessage(),
        ];
        $overall_status = 'unhealthy';
    }

    // Storage accessibility check
    try {
        $test_file = 'health_check_'.time().'.txt';
        Storage::disk('public')->put($test_file, 'test');
        $file_exists = Storage::disk('public')->exists($test_file);
        Storage::disk('public')->delete($test_file);

        if ($file_exists) {
            $checks['storage'] = [
                'status' => 'healthy',
                'message' => 'Storage system working',
            ];
        } else {
            throw new Exception('Storage read/write failed');
        }
    } catch (Exception $e) {
        $checks['storage'] = [
            'status' => 'unhealthy',
            'message' => 'Storage system failed: '.$e->getMessage(),
        ];
        $overall_status = 'unhealthy';
    }

    // Disk space check
    $disk_usage = disk_free_space('/') / disk_total_space('/') * 100;
    if ($disk_usage > 10) {
        $checks['disk_space'] = [
            'status' => 'healthy',
            'message' => 'Disk space sufficient: '.round(100 - $disk_usage, 2).'% used',
        ];
    } else {
        $checks['disk_space'] = [
            'status' => 'warning',
            'message' => 'Disk space low: '.round(100 - $disk_usage, 2).'% used',
        ];
        if ($overall_status === 'healthy') {
            $overall_status = 'warning';
        }
    }

    // Memory usage check
    $memory_usage = memory_get_usage(true);
    $memory_limit = ini_get('memory_limit');
    $memory_limit_bytes = return_bytes($memory_limit);
    $memory_percent = ($memory_usage / $memory_limit_bytes) * 100;

    if ($memory_percent < 80) {
        $checks['memory'] = [
            'status' => 'healthy',
            'message' => 'Memory usage normal: '.round($memory_percent, 2).'%',
        ];
    } else {
        $checks['memory'] = [
            'status' => 'warning',
            'message' => 'Memory usage high: '.round($memory_percent, 2).'%',
        ];
        if ($overall_status === 'healthy') {
            $overall_status = 'warning';
        }
    }

    $response = [
        'status' => $overall_status,
        'timestamp' => now()->toISOString(),
        'version' => config('app.version', '1.0.0'),
        'environment' => config('app.env'),
        'checks' => $checks,
    ];

    $status_code = $overall_status === 'healthy' ? 200 : ($overall_status === 'warning' ? 200 : 503);

    return response()->json($response, $status_code);
});

if (! function_exists('return_bytes')) {
    function return_bytes($val)
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val) - 1]);
        switch ($last) {
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }
}
