<div class="card border-0 shadow-sm">
    <div class="card-body d-flex flex-column align-items-center justify-content-center text-center py-10">
        <img src="{{ asset('assets/images/empty.png') }}" alt="{{ $alt }}" style="max-width: 180px; height: auto;" class="mb-4 opacity-75">
        <h4 class="fw-semibold text-muted mb-1">{{ $title }}</h4>
        <p class="text-secondary mb-4">{{ $message }}</p>
        <a href="{{ $link }}" class="btn btn-primary">
            <i class="ti tabler-plus me-2"></i> {{ $button }}
        </a>
    </div>
</div>
