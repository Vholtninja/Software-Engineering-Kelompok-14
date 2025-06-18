<?php

namespace App\Http\Controllers;

use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Models\ForumReply;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ForumController extends Controller
{
    use AuthorizesRequests;

    private function getThreadsQuery()
    {
        $query = ForumThread::query();
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            $query->where('is_locked', false);
        }
        return $query;
    }

    public function index()
    {
        $categories = ForumCategory::where('is_active', true)
                                  ->withCount(['threads' => function ($query) {
                                      if (!auth()->check() || !auth()->user()->isAdmin()) {
                                          $query->where('is_locked', false);
                                      }
                                  }])
                                  ->orderBy('order')
                                  ->get();

        $recentThreads = $this->getThreadsQuery()->with(['user', 'category'])
                                   ->latest()
                                   ->take(10)
                                   ->get();

        $popularThreads = $this->getThreadsQuery()->with(['user', 'category'])
                                    ->orderBy('views', 'desc')
                                    ->take(5)
                                    ->get();
        return view('forum.index', compact('categories', 'recentThreads', 'popularThreads'));
    }

    public function category($slug)
    {
        $category = ForumCategory::where('slug', $slug)->firstOrFail();
        $threads = $this->getThreadsQuery()
                             ->where('category_id', $category->id)
                             ->with(['user', 'replies' => function($query) {
                                 $query->latest()->take(1);
                             }])
                             ->withCount('replies')
                             ->latest()
                             ->paginate(20);

        return view('forum.category', compact('category', 'threads'));
    }

    public function show($categorySlug, $threadSlug)
    {
        $category = ForumCategory::where('slug', $categorySlug)->firstOrFail();
        $thread = $this->getThreadsQuery()
                            ->where('slug', $threadSlug)
                            ->where('category_id', $category->id)
                            ->with(['user', 'replies.user', 'replies.children.user'])
                            ->firstOrFail();
        
        $viewedThreads = session()->get('viewed_threads', []);
        if (!in_array($thread->id, $viewedThreads)) {
            $thread->increment('views');
            session()->push('viewed_threads', $thread->id);
        }

        $replies = $thread->replies()
                         ->whereNull('parent_id')
                         ->with(['user', 'children.user', 'voters'])
                         ->orderBy('is_best_answer', 'desc')
                         ->orderBy('upvotes', 'desc')
                         ->orderBy('created_at', 'asc')
                         ->paginate(10);
        
        $userVotedReplyIds = collect();
        if(auth()->check()) {
            $userVotedReplyIds = auth()->user()->votedReplies()->whereIn('forum_reply_id', $replies->pluck('id'))->pluck('forum_reply_id');
        }

        return view('forum.thread', compact('category', 'thread', 'replies', 'userVotedReplyIds'));
    }

    public function create(Request $request)
    {
        $categories = ForumCategory::where('is_active', true)
                                  ->orderBy('order')
                                  ->get();
        $selectedCategory = null;
        if ($request->has('category')) {
            $selectedCategory = ForumCategory::where('slug', 'like', $request->category)->first();
        }

        return view('forum.create-thread', compact('categories', 'selectedCategory'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'category_id' => 'required|exists:forum_categories,id',
        ]);
        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . time();

        $thread = ForumThread::create($validated);
        return redirect()->route('forum.thread', [
            $thread->category->slug, 
            $thread->slug
        ])->with('success', 'Thread created successfully!');
    }

    public function reply(Request $request, $categorySlug, $threadSlug)
    {
        $thread = ForumThread::where('slug', $threadSlug)->firstOrFail();
        if ($thread->is_locked && !auth()->user()->isAdmin()) {
            return redirect()->back()->with('error', 'This thread is locked.');
        }

        $validated = $request->validate([
            'content' => 'required|string|min:5',
            'parent_id' => 'nullable|exists:forum_replies,id',
        ]);
        $validated['user_id'] = auth()->id();
        $validated['thread_id'] = $thread->id;

        ForumReply::create($validated);

        return redirect()->back()->with('success', 'Reply posted successfully!');
    }

    public function upvote(Request $request, $replyId)
    {
        $reply = ForumReply::findOrFail($replyId);
        $user = auth()->user();

        $hasVoted = $user->votedReplies()->where('forum_reply_id', $replyId)->exists();

        if ($hasVoted) {
            $user->votedReplies()->detach($replyId);
            $reply->decrement('upvotes');
            $voted = false;
        } else {
            $user->votedReplies()->attach($replyId);
            $reply->increment('upvotes');
            $voted = true;
        }
        
        return response()->json([
            'upvotes' => $reply->upvotes,
            'voted' => $voted,
        ]);
    }

    public function markBestAnswer(Request $request, $replyId)
    {
        $reply = ForumReply::findOrFail($replyId);
        $thread = $reply->thread;

        if ($thread->user_id !== auth()->id() && !auth()->user()->isModerator()) {
            abort(403);
        }

        ForumReply::where('thread_id', $thread->id)->update(['is_best_answer' => false]);
        $reply->update(['is_best_answer' => true]);

        return response()->json(['success' => true]);
    }

    public function edit($categorySlug, $threadSlug)
    {
        $category = ForumCategory::where('slug', $categorySlug)->firstOrFail();
        $thread = $this->getThreadsQuery()->where('slug', $threadSlug)->firstOrFail();
        
        if (auth()->id() !== $thread->user_id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $categories = ForumCategory::where('is_active', true)->orderBy('order')->get();
        return view('forum.edit-thread', compact('category', 'thread', 'categories'));
    }

    public function update(Request $request, $categorySlug, $threadSlug)
    {
        $thread = $this->getThreadsQuery()->where('slug', $threadSlug)->firstOrFail();
        if (auth()->id() !== $thread->user_id && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|min:10',
            'category_id' => 'required|exists:forum_categories,id',
        ]);
        if ($validated['title'] !== $thread->title) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . time();
        }

        $thread->update($validated);
        $newCategory = ForumCategory::find($validated['category_id']);

        return redirect()->route('forum.thread', [
            $newCategory->slug, 
            $thread->slug
        ])->with('success', 'Thread updated successfully!');
    }

    public function destroy($categorySlug, $threadSlug)
    {
        $thread = $this->getThreadsQuery()->where('slug', $threadSlug)->firstOrFail();
        if ($thread->user_id !== auth()->id() && !auth()->user()->isModerator()) {
            abort(403);
        }

        $thread->delete();

        return redirect()->route('forum.category', $categorySlug)
                        ->with('success', 'Thread deleted successfully!');
    }
    
    public function toggleLock($categorySlug, $threadSlug)
    {
        $thread = ForumThread::where('slug', $threadSlug)->firstOrFail();
        if (!auth()->user()->isModerator()) {
            abort(403);
        }
        $thread->update(['is_locked' => !$thread->is_locked]);
        $message = $thread->is_locked ? 'Thread has been locked.' : 'Thread has been unlocked.';
        return redirect()->back()->with('success', $message);
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        if (!$query) {
            return redirect()->route('forum.index');
        }

        $threads = $this->getThreadsQuery()
                             ->where(function($q) use ($query) {
                                 $q->where('title', 'like', "%{$query}%")
                                   ->orWhere('content', 'like', "%{$query}%");
                             })
                             ->with(['user', 'category'])
                             ->paginate(20);

        return view('forum.search', compact('threads', 'query'));
    }
}