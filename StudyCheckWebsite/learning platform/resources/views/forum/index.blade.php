<!-- resources/views/forum/index.blade.php -->
<x-app-layout>
    <x-slot name="title">Forum - {{ config('app.name') }}</x-slot>

    <div class="container py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-8">
                <h2>Community Forum</h2>
                <p class="text-muted">Connect, discuss, and learn together with our community</p>
            </div>
            <div class="col-md-4 text-md-end">
                @auth
                    <a href="{{ route('forum.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Start Discussion
                    </a>
                @endauth
            </div>
        </div>

        <!-- Search -->
        <div class="card mb-4">
            <div class="card-body">
                <form action="{{ route('forum.search') }}" method="GET">
                    <div class="input-group">
                        <input type="text" name="q" class="form-control" placeholder="Search discussions..." value="{{ request('q') }}">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <!-- Categories -->
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Discussion Categories</h5>
                    </div>
                    <div class="card-body">
                        @if($categories->count() > 0)
                            @foreach($categories as $category)
                                <div class="d-flex align-items-center p-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                    <div class="rounded-circle me-3" style="width: 40px; height: 40px; background-color: {{ $category->color }};">
                                        <i class="bi bi-chat-dots text-white d-flex align-items-center justify-content-center h-100"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <a href="{{ route('forum.category', $category->slug) }}" class="text-decoration-none">
                                                {{ $category->name }}
                                            </a>
                                        </h6>
                                        @if($category->description)
                                            <p class="text-muted small mb-0">{{ $category->description }}</p>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-light text-dark">{{ $category->threads_count }} threads</span>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4">
                                <i class="bi bi-chat-dots display-4 text-muted"></i>
                                <h6 class="text-muted mt-3">No categories yet</h6>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Recent Threads -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Recent Discussions</h6>
                    </div>
                    <div class="card-body">
                        @if($recentThreads->count() > 0)
                            @foreach($recentThreads as $thread)
                                <div class="mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                                    <h6 class="mb-1">
                                        <a href="{{ route('forum.thread', [$thread->category->slug, $thread->slug]) }}" class="text-decoration-none">
                                            {{ Str::limit($thread->title, 50) }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        by {{ $thread->user->name }} • {{ $thread->created_at->diffForHumans() }}
                                    </small>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">No recent discussions</p>
                        @endif
                    </div>
                </div>

                <!-- Popular Threads -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Popular This Week</h6>
                    </div>
                    <div class="card-body">
                        @if($popularThreads->count() > 0)
                            @foreach($popularThreads as $thread)
                                <div class="mb-3 {{ !$loop->last ? 'pb-3 border-bottom' : '' }}">
                                    <h6 class="mb-1">
                                        <a href="{{ route('forum.thread', [$thread->category->slug, $thread->slug]) }}" class="text-decoration-none">
                                            {{ Str::limit($thread->title, 50) }}
                                        </a>
                                    </h6>
                                    <small class="text-muted">
                                        <i class="bi bi-eye me-1"></i>{{ $thread->views }} views
                                        <span class="mx-1">•</span>
                                        <i class="bi bi-chat me-1"></i>{{ $thread->replies_count }} replies
                                    </small>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted text-center">No popular discussions yet</p>
                        @endif
                    </div>
                </div>

                @guest
                    <!-- Sign Up CTA -->
                    <div class="card mt-4">
                        <div class="card-body text-center">
                            <h6>Join the Discussion!</h6>
                            <p class="text-muted small">Sign up to participate in discussions and get help from the community.</p>
                            <a href="{{ route('register') }}" class="btn btn-primary btn-sm">Sign Up Free</a>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </div>
</x-app-layout>