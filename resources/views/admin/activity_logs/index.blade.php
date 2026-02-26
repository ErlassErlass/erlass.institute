@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Log Aktivitas Sistem</h1>
    </div>

    {{-- Filter Section --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body py-3">
            <form action="{{ route('admin.activity-logs.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label for="user_id" class="form-label small fw-bold">User</label>
                    <select name="user_id" id="user_id" class="form-select form-select-sm">
                        <option value="">Semua User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->nama_lengkap }} ({{ $user->role }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="action" class="form-label small fw-bold">Aksi</label>
                    <select name="action" id="action" class="form-select form-select-sm">
                        <option value="">Semua Aksi</option>
                        @foreach($actions as $act)
                            <option value="{{ $act }}" {{ request('action') == $act ? 'selected' : '' }}>
                                {{ ucfirst($act) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date" class="form-label small fw-bold">Tanggal</label>
                    <input type="text" class="form-control form-control-sm datepicker" name="date" value="{{ request('date') }}" placeholder="DD-MM-YYYY">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-filter me-1"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu</th>
                            <th>User</th>
                            <th>Aksi</th>
                            <th>Deskripsi</th>
                            <th>IP Address</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td class="small">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td>
                                @if($log->user)
                                    <span class="fw-bold">{{ $log->user->nama_lengkap }}</span><br>
                                    <small class="text-muted">{{ $log->user->role }}</small>
                                @else
                                    <span class="text-muted">System/Guest</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary">{{ $log->action }}</span></td>
                            <td>
                                {{ $log->description }}
                                @if($log->subject_type)
                                    <br><small class="text-muted">Ref: {{ class_basename($log->subject_type) }} #{{ $log->subject_id }}</small>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $log->ip_address }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">Tidak ada log aktivitas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-3">
                {{ $logs->appends(request()->except('page'))->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
