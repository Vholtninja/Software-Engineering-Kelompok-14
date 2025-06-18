<x-app-layout>
    <x-slot name="title">{{ $thread->title }} - Forum - {{ config('app.name') }}</x-slot>

    <div class="container py-4">
        <nav aria-label="breadcrumb" class="mb-4">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('forum.index') }}">Forum</a></li>
                <li class="breadcrumb-item"><a href="{{ route('forum.category', $category->slug) }}">{{ $category->name }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ Str::limit($thread->title, 50) }}</li>
            </ol>
        </nav>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="mb-1 fw-bold">
                                {{ $thread->title }}
                            </h4>
                            <div>
                                <span class="badge" style="background-color: {{ $category->color }}; color: white;">{{ $category->name }}</span>
                                @if($thread->is_locked)
                                    <span class="badge bg-secondary ms-2"><i class="bi bi-lock-fill"></i> Locked</span>
                                @endif
                            </div>
                        </div>
                        
                        @if(auth()->check() && (auth()->id() === $thread->user_id || auth()->user()->isModerator()))
                            <div class="dropdown" style="position: relative; z-index: 2;">
                                <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @if(auth()->id() === $thread->user_id || auth()->user()->isAdmin())
                                    <li><a class="dropdown-item" href="{{ route('forum.edit', [$category->slug, $thread->slug]) }}"><i class="bi bi-pencil-square me-2"></i>Edit</a></li>
                                    @endif
                                    @if(auth()->user()->isModerator())
                                        <li>
                                            <button class="dropdown-item" onclick="document.getElementById('toggle-lock-form').submit();">
                                                <i class="bi bi-lock-fill me-2"></i>{{ $thread->is_locked ? 'Unlock' : 'Lock' }} Thread
                                            </button>
                                        </li>
                                    @endif
                                    @if(auth()->id() === $thread->user_id || auth()->user()->isModerator())
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <button type="button" class="dropdown-item text-danger" onclick="if(confirm('Are you sure you want to delete this thread?')) document.getElementById('delete-form').submit();">
                                            <i class="bi bi-trash-fill me-2"></i>Delete Thread
                                        </button>
                                    </li>
                                    @endif
                                </ul>
                                <form id="delete-form" action="{{ route('forum.destroy', [$category->slug, $thread->slug]) }}" method="POST" class="d-none">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                 <form id="toggle-lock-form" action="{{ route('forum.toggle-lock', [$category->slug, $thread->slug]) }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </div>
                        @endif
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <img src="{{ $thread->user->avatar_url }}" alt="{{ $thread->user->name }}" class="avatar me-3">
                            <div>
                                <div class="fw-bold d-flex align-items-center gap-1">
                                    {{ $thread->user->name }}
                                    @if($thread->user->is_verified)
                                        <i class="bi bi-patch-check-fill text-primary" title="Verified"></i>
                                    @endif
                                </div>
                                <div class="text-muted small">
                                    {{ ucfirst($thread->user->role) }} • {{ $thread->created_at->format('M d, Y') }}
                                </div>
                            </div>
                        </div>
                        <div class="fs-5 lh-lg">
                            {!! nl2br(e($thread->content)) !!}
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0">{{ $replies->total() }} Replies</h5>
                    </div>
                    <div class="card-body p-4">
                        @forelse($replies as $reply)
                            <div class="reply-item d-flex mb-4" id="reply-{{ $reply->id }}">
                                <img src="{{ $reply->user->avatar_url }}" alt="{{ $reply->user->name }}" class="avatar me-3 mt-1">
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <span class="fw-bold d-inline-flex align-items-center gap-1">
                                                {{ $reply->user->name }}
                                                @if($reply->user->is_verified)
                                                    <i class="bi bi-patch-check-fill text-primary" title="Verified"></i>
                                                @endif
                                            </span>
                                            <span class="text-muted small ms-2">• {{ $reply->created_at->diffForHumans() }}</span>
                                        </div>
                                        @if($reply->is_best_answer)
                                            <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Best Answer</span>
                                        @endif
                                    </div>
                                    <div class="mt-2 mb-2">{!! nl2br(e($reply->content)) !!}</div>
                                    <div class="d-flex align-items-center gap-2">
                                        @auth
                                            @php
                                                $hasVoted = $userVotedReplyIds->contains($reply->id);
                                            @endphp
                                            <button class="btn btn-sm {{ $hasVoted ? 'btn-primary' : 'btn-light' }}" onclick="upvoteReply({{ $reply->id }})" id="upvote-btn-{{ $reply->id }}">
                                                <i class="bi bi-arrow-up"></i> <span id="upvote-count-{{ $reply->id }}">{{ $reply->upvotes }}</span>
                                            </button>
                                            <button class="btn btn-sm btn-light" onclick="toggleReplyForm({{ $reply->id }})"><i class="bi bi-reply"></i> Reply</button>
                                            @if(auth()->id() === $thread->user_id && !$reply->is_best_answer)
                                                <button class="btn btn-sm btn-outline-success" onclick="markBestAnswer({{ $reply->id }})"><i class="bi bi-check-lg"></i> Mark as Best</button>
                                            @endif
                                        @endauth
                                    </div>
                                    <div id="reply-form-{{ $reply->id }}" class="mt-3" style="display: none;">
                                        <form action="{{ route('forum.reply', [$category->slug, $thread->slug]) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                                            <textarea name="content" class="form-control" rows="2" placeholder="Write your reply..." required></textarea>
                                            <div class="d-flex justify-content-end mt-2 gap-2">
                                                <button type="button" class="btn btn-sm btn-secondary" onclick="toggleReplyForm({{ $reply->id }})">Cancel</button>
                                                <button type="submit" class="btn btn-sm btn-primary">Post Reply</button>
                                            </div>
                                        </form>
                                    </div>
                                    @if($reply->children->count() > 0)
                                        <div class="mt-3 ms-4 border-start ps-3">
                                            @foreach($reply->children as $childReply)
                                                <div class="d-flex mb-3">
                                                    <img src="{{ $childReply->user->avatar_url }}" alt="{{ $childReply->user->name }}" class="avatar me-2" style="width: 30px; height: 30px;">
                                                    <div class="flex-grow-1">
                                                        <div>
                                                            <span class="fw-bold small d-inline-flex align-items-center gap-1">
                                                                {{ $childReply->user->name }}
                                                                @if($childReply->user->is_verified)
                                                                    <i class="bi bi-patch-check-fill text-primary" title="Verified"></i>
                                                                @endif
                                                            </span>
                                                            <span class="text-muted small ms-2">• {{ $childReply->created_at->diffForHumans() }}</span>
                                                        </div>
                                                        <div class="small">{!! nl2br(e($childReply->content)) !!}</div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-4">No replies yet. Be the first to join the discussion!</p>
                        @endforelse
                        <div class="d-flex justify-content-center mt-4">
                            {{ $replies->links() }}
                        </div>
                    </div>
                </div>

                @auth
                    @if(!$thread->is_locked)
                        <div class="card mt-4 shadow-sm">
                            <div class="card-header bg-white py-3">
                                <h5 class="fw-bold mb-0">Post Your Reply</h5>
                            </div>
                            <div class="card-body p-4">
                                <form action="{{ route('forum.reply', [$category->slug, $thread->slug]) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <textarea name="content" class="form-control" rows="5" placeholder="Write your thoughts..." required>{{ old('content') }}</textarea>
                                    </div>
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary"><i class="bi bi-send-fill me-2"></i>Post Reply</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning mt-4"><i class="bi bi-lock-fill me-2"></i>This thread is locked. No new replies can be posted.</div>
                    @endif
                @else
                    <div class="card mt-4 shadow-sm text-center p-4">
                        <h5 class="fw-bold">Join the Discussion!</h5>
                        <p class="text-muted">You must be logged in to post a reply.</p>
                        <a href="{{ route('login') }}" class="btn btn-primary">Login to Reply</a>
                    </div>
                @endauth
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-header bg-white py-3">
                        <h6 class="fw-bold mb-0">Thread Stats</h6>
                    </div>
                    <div class="card-body">
                         <div class="row text-center">
                            <div class="col-4">
                                <i class="bi bi-eye-fill fs-4 text-primary"></i>
                                <div class="fw-bold">{{ $thread->views }}</div>
                                <small class="text-muted">Views</small>
                            </div>
                            <div class="col-4">
                                <i class="bi bi-chat-left-text-fill fs-4 text-success"></i>
                                <div class="fw-bold">{{ $thread->replies_count }}</div>
                                <small class="text-muted">Replies</small>
                            </div>
                            <div class="col-4">
                                <i class="bi bi-person-fill fs-4 text-info"></i>
                                <div class="fw-bold">{{ $thread->replies->unique('user_id')->count() }}</div>
                                <small class="text-muted">Users</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function upvoteReply(replyId) {
            fetch(`/forum/reply/${replyId}/upvote`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.upvotes !== undefined) {
                    document.getElementById(`upvote-count-${replyId}`).textContent = data.upvotes;
                    const btn = document.getElementById(`upvote-btn-${replyId}`);
                    if (data.voted) {
                        btn.classList.add('btn-primary');
                        btn.classList.remove('btn-light');
                    } else {
                        btn.classList.add('btn-light');
                        btn.classList.remove('btn-primary');
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function markBestAnswer(replyId) {
            if (confirm('Mark this as the best answer?')) {
                fetch(`/forum/reply/${replyId}/best-answer`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    }
                })
                .catch(error => console.error('Error:', error));
            }
        }

        function toggleReplyForm(replyId) {
            const form = document.getElementById(`reply-form-${replyId}`);
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
    </script>
    @endpush
</x-app-layout>