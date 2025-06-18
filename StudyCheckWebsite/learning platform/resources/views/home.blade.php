<x-app-layout>
    <x-slot name="title">Online Learning Platform - {{ config('app.name') }}</x-slot>

    @push('styles')
    <style>
        .hero-section {
            background: linear-gradient(to right, rgba(var(--bs-primary-rgb), 0.05), rgba(var(--bs-info-rgb), 0.05));
        }
        .search-form-wrapper {
            background: #fff;
            padding: 0.5rem;
            border-radius: 0.75rem;
            box-shadow: 0 10px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        .stat-card {
            background: #fff;
            border-radius: 0.75rem;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05);
        }
        .feature-section {
            background: url('https://images.unsplash.com/photo-1557804506-669a67965ba0?q=80&w=1974&auto=format&fit=crop') no-repeat center center;
            background-size: cover;
            position: relative;
        }
        .feature-section::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(var(--bs-primary-rgb), 0.8);
            backdrop-filter: blur(5px);
        }
        .feature-item {
            background: rgba(255,255,255,0.1);
            padding: 1.5rem;
            border-radius: 0.75rem;
            color: white;
            border: 1px solid rgba(255,255,255,0.2);
        }
        .discussion-section {
            background-color: var(--bs-light);
        }
        .thread-item a {
            text-decoration: none;
            color: var(--bs-dark);
            transition: all 0.2s ease-in-out;
        }
        .thread-item:hover {
            transform: translateX(5px);
            background-color: #fff;
        }
    </style>
    @endpush

    <div class="container-fluid gx-0">
        <section class="hero-section">
            <div class="container py-5 text-center">
                <div class="py-5">
                    <h1 class="display-3 fw-bolder mb-3">Find Your Next Course</h1>
                    <p class="lead text-muted mb-4 mx-auto" style="max-width: 700px;">Unlock your potential with expert-led courses. Start your learning journey today and achieve your goals faster than ever.</p>
                    <div class="search-form-wrapper">
                        <form action="{{ route('courses.index') }}" method="GET">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control form-control-lg border-0" placeholder="What do you want to learn today?">
                                <button class="btn btn-primary btn-lg" type="submit">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5">
            <div class="container">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="stat-card">
                            <h3 class="fw-bolder text-primary">{{ number_format($stats['total_courses']) }}+</h3>
                            <p class="text-muted mb-0 fw-semibold">Online Courses</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                             <h3 class="fw-bolder text-primary">{{ number_format($stats['total_teachers']) }}+</h3>
                            <p class="text-muted mb-0 fw-semibold">Expert Instructors</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card">
                             <h3 class="fw-bolder text-primary">{{ number_format($stats['total_students']) }}+</h3>
                            <p class="text-muted mb-0 fw-semibold">Active Students</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="py-5 bg-white">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="fw-bold">Newest Courses</h2>
                    <p class="text-muted">Be the first to explore our latest course offerings.</p>
                </div>
                 @if($newestCourses->count() > 0)
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
                        @foreach($newestCourses as $course)
                            <x-course-card :course="$course" />
                        @endforeach
                    </div>
                    <div class="text-center mt-5">
                        <a href="{{ route('courses.index') }}" class="btn btn-primary">
                            View All Courses <i class="bi bi-arrow-right ms-2"></i>
                        </a>
                    </div>
                @else
                    <p class="text-center text-muted">New courses will be shown here soon.</p>
                @endif
            </div>
        </section>

        <section class="feature-section py-5">
            <div class="container py-5 position-relative">
                <div class="row g-5 align-items-center">
                    <div class="col-lg-6">
                        <h2 class="display-5 fw-bolder text-white">Why Choose Our Platform?</h2>
                        <p class="lead text-white-50 mt-3">We provide a complete learning ecosystem designed for your success.</p>
                    </div>
                    <div class="col-lg-6">
                        <div class="d-grid gap-4">
                            <div class="feature-item">
                                <h5 class="fw-bold mb-2"><i class="bi bi-person-video3 me-3"></i>Flexible Learning</h5>
                                <p class="mb-0 text-white-50">Learn at your own pace, anytime and anywhere. Access all materials on your desktop or mobile device.</p>
                            </div>
                            <div class="feature-item">
                                <h5 class="fw-bold mb-2"><i class="bi bi-lightbulb-fill me-3"></i>Interactive Content</h5>
                                <p class="mb-0 text-white-50">Solidify your understanding with quizzes, homework help, and hands-on final projects.</p>
                            </div>
                            <div class="feature-item">
                                <h5 class="fw-bold mb-2"><i class="bi bi-shield-check me-3"></i>Verified Experts</h5>
                                <p class="mb-0 text-white-50">Get answers and guidance not just from instructors, but also from a community of verified experts.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="discussion-section py-5">
             <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8 text-center">
                         <h2 class="fw-bold">Join The Conversation</h2>
                         <p class="text-muted mb-4">See what the community is talking about right now.</p>
                         
                         <div class="card shadow-sm">
                             <div class="list-group list-group-flush">
                                 @forelse($recentThreads as $thread)
                                     <div class="list-group-item p-3 thread-item">
                                         <a href="{{ route('forum.thread', [$thread->category->slug, $thread->slug]) }}">
                                             <div class="d-flex align-items-center">
                                                 <img src="{{ $thread->user->avatar_url }}" alt="{{ $thread->user->name }}" class="avatar me-3">
                                                 <div class="flex-grow-1 text-start">
                                                     <h6 class="mb-0 fw-bold">{{ Str::limit($thread->title, 60) }}</h6>
                                                     <small class="text-muted">
                                                         by {{ $thread->user->name }} in <span class="fw-semibold" style="color: {{ $thread->category->color }}">{{ $thread->category->name }}</span>
                                                     </small>
                                                 </div>
                                                 <span class="text-muted small d-none d-md-block">{{ $thread->created_at->diffForHumans() }}</span>
                                             </div>
                                         </a>
                                     </div>
                                 @empty
                                     <p class="text-muted py-4">No discussions yet. Start one now!</p>
                                 @endforelse
                             </div>
                         </div>
                         <a href="{{ route('forum.index') }}" class="btn btn-primary mt-4">
                             Visit Forum <i class="bi bi-arrow-right ms-2"></i>
                         </a>
                    </div>
                </div>
            </div>
        </section>

    </div>
</x-app-layout>