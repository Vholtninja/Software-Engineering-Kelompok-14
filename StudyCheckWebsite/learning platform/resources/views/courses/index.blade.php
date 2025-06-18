<x-app-layout>
    <x-slot name="title">Explore Courses - {{ config('app.name') }}</x-slot>

    <div class="container py-5">
        <div class="row align-items-center mb-5">
            <div class="col-md-8">
                <h1 class="fw-bold">Explore Courses</h1>
                <p class="text-muted fs-5">Find the perfect course to achieve your personal and professional goals.</p>
            </div>
            <div class="col-md-4 text-md-end">
                @auth
                    @if(auth()->user()->isTeacher())
                        <a href="{{ route('courses.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle-fill me-2"></i>Create New Course
                        </a>
                    @endif
                @endauth
            </div>
        </div>

        <div class="card mb-4 shadow-sm">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('courses.index') }}">
                    <div class="row g-3">
                        <div class="col-md-5">
                             <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-search"></i></span>
                                <input type="text" name="search" class="form-control border-start-0" placeholder="Search courses by title or description..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="category" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                                        {{ ucfirst($category) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="level" class="form-select">
                                <option value="">All Levels</option>
                                <option value="beginner" {{ request('level') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ request('level') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ request('level') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Filter</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($courses->count() > 0)
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                @foreach($courses as $course)
                    <x-course-card :course="$course" />
                @endforeach
            </div>

            <div class="d-flex justify-content-center mt-5">
                {{ $courses->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-binoculars-fill" style="font-size: 5rem; color: #e0e0e0;"></i>
                </div>
                <h4 class="fw-bold">No Courses Found</h4>
                <p class="text-muted">Try adjusting your search criteria or check back later for new courses.</p>
                @if(request()->hasAny(['search', 'category', 'level']))
                    <a href="{{ route('courses.index') }}" class="btn btn-primary mt-3">Clear Filters</a>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>