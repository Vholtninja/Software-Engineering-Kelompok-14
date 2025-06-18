<x-app-layout>
    <x-slot name="title">Manage Forum Categories</x-slot>

    <div class="container py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold">Forum Categories</h1>
            <a href="{{ route('admin.forum-categories.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle-fill me-2"></i>Add New Category
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Threads</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($categories as $category)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3 rounded-circle" style="width: 20px; height: 20px; background-color: {{ $category->color }};"></div>
                                            <div>
                                                <div class="fw-bold">{{ $category->name }}</div>
                                                <small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $category->threads_count }}</td>
                                    <td>
                                        @if($category->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.forum-categories.edit', $category) }}" class="btn btn-outline-primary">Edit</a>
                                            <button type="button" class="btn btn-outline-danger" onclick="document.getElementById('delete-form-{{ $category->id }}').submit();">
                                                Delete
                                            </button>
                                            <form id="delete-form-{{ $category->id }}" action="{{ route('admin.forum-categories.destroy', $category) }}" method="POST" class="d-none">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No categories found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>