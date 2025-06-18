<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ForumCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ForumCategoryController extends Controller
{
    public function index()
    {
        $categories = ForumCategory::withCount('threads')->latest()->paginate(15);
        return view('admin.forum-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.forum-categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:forum_categories',
            'description' => 'nullable|string|max:1000',
            'color' => 'required|string|max:7',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        ForumCategory::create($validated);

        return redirect()->route('admin.forum-categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(ForumCategory $forumCategory)
    {
        return view('admin.forum-categories.edit', compact('forumCategory'));
    }

    public function update(Request $request, ForumCategory $forumCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:forum_categories,name,' . $forumCategory->id,
            'description' => 'nullable|string|max:1000',
            'color' => 'required|string|max:7',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->has('is_active');

        $forumCategory->update($validated);

        return redirect()->route('admin.forum-categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(ForumCategory $forumCategory)
    {
        if ($forumCategory->threads()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete category with active threads. Please move or delete the threads first.');
        }

        $forumCategory->delete();
        return redirect()->route('admin.forum-categories.index')->with('success', 'Category deleted successfully.');
    }
}