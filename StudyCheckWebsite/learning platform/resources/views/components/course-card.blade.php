@props(['course'])

<div class="col">
    <div class="card h-100 shadow-sm overflow-hidden">
        <a href="{{ route('courses.show', $course->slug) }}">
            <img src="{{ $course->thumbnail_url }}" class="card-img-top" alt="{{ $course->title }}" style="height: 200px; object-fit: cover;">
        </a>
        <div class="card-body d-flex flex-column">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge text-bg-primary-soft text-primary fw-semibold">{{ ucfirst($course->category) }}</span>
                <span class="badge text-bg-secondary-soft fw-semibold">{{ ucfirst($course->level) }}</span>
            </div>
            <h5 class="card-title fw-bold">
                <a href="{{ route('courses.show', $course->slug) }}" class="text-decoration-none text-dark stretched-link">{{ $course->title }}</a>
            </h5>
            <p class="card-text text-muted small flex-grow-1">{{ Str::limit($course->description, 100) }}</p>
            <div class="d-flex align-items-center mt-3 pt-3 border-top">
                <img src="{{ $course->instructor->avatar_url }}" alt="{{ $course->instructor->name }}" class="avatar me-2">
                <div class="flex-grow-1">
                    <div class="fw-bold small d-flex align-items-center gap-1">
                        {{ $course->instructor->name }}
                        @if($course->instructor->is_verified)
                            <i class="bi bi-patch-check-fill text-primary" title="Verified Instructor"></i>
                        @endif
                    </div>
                </div>
                <div class="fw-bold text-primary">{{ $course->formatted_price }}</div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.text-bg-primary-soft {
    background-color: rgba(var(--bs-primary-rgb), 0.1);
}
.text-bg-secondary-soft {
    background-color: rgba(var(--bs-secondary-rgb), 0.1);
    color: var(--bs-secondary) !important;
}
</style>
@endpush