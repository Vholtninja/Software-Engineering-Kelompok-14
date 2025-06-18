@props(['thread'])

<a href="{{ route('forum.thread', [$thread->category->slug, $thread->slug]) }}" class="card text-decoration-none text-dark shadow-sm">
    <div class="card-body">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0 me-3">
                <img src="{{ $thread->user->avatar_url }}" alt="{{ $thread->user->name }}" class="avatar">
            </div>
            <div class="flex-grow-1">
                 <h6 class="fw-bold mb-1">
                    @if($thread->is_pinned)
                        <i class="bi bi-pin-angle-fill text-warning me-1"></i>
                    @endif
                    {{ $thread->title }}
                </h6>
                 <small class="text-muted">
                    <span class="fw-semibold d-inline-flex align-items-center gap-1">
                        {{ $thread->user->name }}
                        @if($thread->user->is_verified)
                            <i class="bi bi-patch-check-fill text-primary" title="Verified"></i>
                        @endif
                    </span>
                    posted in 
                    <span class="badge" style="background-color: {{ $thread->category->color }}; color: white;">{{ $thread->category->name }}</span>
                    • {{ $thread->created_at->diffForHumans() }}
                </small>
            </div>
            <div class="flex-shrink-0 text-end ms-3">
                <div class="fw-bold"><i class="bi bi-chat-left-text me-2"></i>{{ $thread->replies_count }}</div>
                <small class="text-muted">Replies</small>
            </div>
        </div>
    </div>
</a>