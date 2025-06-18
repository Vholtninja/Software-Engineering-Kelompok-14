<!-- resources/views/forum/category.blade.php -->
<x-app-layout>
    <x-slot name="title">{{ $category->name }} - Forum - {{ config('app.name') }}</x-slot>

    <div class="container py-4">
        <!-- Breadcrumb -->
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('forum.index') }}">Forum</a></li>
                <li class="breadcrumb-item active">{{ $category->name }}</li>
            </ol>
        </nav>

        <!-- Category Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle me-3" style="width: 50px; height: 50px; background-color: {{ $category->color }};">
                        <i class="bi bi-chat-dots text-white d-flex align-items-center justify-content-center h-100 fs-4"></i>
                    </div>
                    <div>
                        <h2 class="mb-1">{{ $category->name }}</h2>
                        @if($category->description)
                            <p class="text-muted mb-0">{{ $category->description }}</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                @auth
                    <a href="{{ route('forum.create', ['category' => $category->slug]) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>New Thread
                    </a>
                @endauth
            </div>
        </div>

        <!-- Threads -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Discussions ({{ $threads->total() }})</h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary active">Recent</button>
                        <button class="btn btn-outline-secondary">Popular</button>
                        <button class="btn btn-outline-secondary">Unanswered</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if($threads->count() > 0)
                    @foreach($threads as $thread)
                        <div class="d-flex align-items-start p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <img src="{{ $thread->user->avatar_url }}" alt="{{ $thread->user->name }}" class="avatar me-3">
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            @if($thread->is_pinned)
                                                <i class="bi bi-pin-angle-fill text-warning me-1"></i>
                                            @endif
                                            <a href="{{ route('forum.thread', [$category->slug, $thread->slug]) }}" class="text-decoration-none">
                                                {{ $thread->title }}
                                            </a>
                                            @if($thread->is_locked)
                                                <i class="bi bi-lock text-muted ms-2"></i>
                                            @endif
                                        </h6>
                                        <p class="text-muted small mb-2">{{ Str::limit(strip_tags($thread->content), 100) }}</p>
                                        <div class="d-flex align-items-center text-muted small">
                                            <span>by {{ $thread->user->name }}</span>
                                            <span class="mx-2">•</span>
                                            <span>{{ $thread->created_at->diffForHumans() }}</span>
                                            <span class="mx-2">•</span>
                                            <i class="bi bi-eye me-1"></i>{{ $thread->views }}
                                            <span class="mx-2">•</span>
                                            <i class="bi bi-chat me-1"></i>{{ $thread->replies_count }}
                                            @if($thread->replies->isNotEmpty())
                                                <span class="mx-2">•</span>
                                                <span>Last reply {{ $thread->replies->first()->created_at->diffForHumans() }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-end ms-3">
                                        @if($thread->replies->where('is_best_answer', true)->isNotEmpty())
                                            <i class="bi bi-check-circle-fill text-success" title="Has best answer"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $threads->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-chat-dots display-4 text-muted"></i>
                        <h6 class="text-muted mt-3">No discussions yet</h6>
                        <p class="text-muted">Be the first to start a discussion in this category!</p>
                        @auth
                            <a href="{{ route('forum.create', ['category' => $category->slug]) }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Start Discussion
                            </a>
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>