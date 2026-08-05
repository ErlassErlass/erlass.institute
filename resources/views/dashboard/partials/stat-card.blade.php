<div class="card shadow-sm border-0 h-100 {{ $bg }}">
    <div class="card-body">
        <div class="d-flex align-items-center mb-2">
            <div class="bg-white bg-opacity-25 p-2 rounded-3 text-white me-3">
                <i class="{{ $icon }} fs-4"></i>
            </div>
            <h6 class="card-subtitle mb-0" style="color: rgba(255, 255, 255, 0.88);">{{ $title }}</h6>
        </div>
        <h3 class="card-title fw-bold mb-0 text-white">{{ $value }}</h3>
        <small style="color: rgba(255, 255, 255, 0.88);">{{ $subtitle }}</small>
    </div>
</div>
