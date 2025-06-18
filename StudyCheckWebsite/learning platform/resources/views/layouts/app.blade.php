<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'Learning Platform') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --bs-primary: #4F46E5;
            --bs-primary-rgb: 79, 70, 229;
            --bs-secondary: #6B7280;
            --bs-success: #10B981;
            --bs-info: #3B82F6;
            --bs-warning: #F59E0B;
            --bs-danger: #EF4444;
            --bs-light: #F9FAFB;
            --bs-dark: #1F2937;
            --bs-font-sans-serif: 'Plus Jakarta Sans', sans-serif;
            --bs-body-color: #374151;
            --bs-body-bg: #F9FAFB;
        }
        body {
            font-family: var(--bs-font-sans-serif);
        }
        .navbar {
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            z-index: 1040;
        }
        .navbar-brand {
            font-weight: 800;
        }
        .nav-link {
            font-weight: 600;
            color: var(--bs-dark);
            transition: color 0.2s ease-in-out;
        }
        .nav-link:hover, .nav-link.active {
            color: var(--bs-primary);
        }
        .dropdown-menu {
            border-radius: 0.75rem;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            border: 1px solid #e5e7eb;
        }
        .dropdown-item {
            font-weight: 600;
        }
        .dropdown-item:active {
            background-color: var(--bs-primary);
        }
        .dropdown-item i {
            width: 20px;
        }
        .dropdown-user-info {
            border-bottom: 1px solid #e5e7eb;
        }
        .btn {
            font-weight: 700;
            border-radius: 0.5rem;
            padding: 0.625rem 1.25rem;
        }
        .btn-primary {
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }
        .btn-primary:hover {
            opacity: 0.9;
        }
        .card {
            border: none;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.05), 0 2px 4px -2px rgb(0 0 0 / 0.05);
            transition: all 0.3s ease-in-out;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }
        .card-header, .card-footer {
            background-color: #fff;
            border-bottom: 1px solid #e5e7eb;
        }
        .card-header {
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
        }
        .avatar {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
        }
        footer {
            background-color: var(--bs-dark);
            color: #9CA3AF;
        }
        footer h5 {
            color: #fff;
            font-weight: 700;
        }
        footer a {
            text-decoration: none;
            color: #9CA3AF;
            transition: color 0.2s ease;
        }
        footer a:hover {
            color: #fff;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-light">
    <div id="app">
        <nav class="navbar navbar-expand-lg bg-white sticky-top">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <i class="bi bi-mortarboard-fill text-primary"></i>
                    {{ config('app.name', 'Learning Platform') }}
                </a>

                <div class="d-lg-none ms-auto">
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-primary">Log In</a>
                    @else
                        <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasNavbar" aria-controls="offcanvasNavbar">
                            <span class="navbar-toggler-icon"></span>
                        </button>
                    @endguest
                </div>

                <div class="collapse navbar-collapse" id="mainNavbar">
                    <ul class="navbar-nav mx-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('courses.*') ? 'active' : '' }}" href="{{ route('courses.index') }}">Courses</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('forum.*') ? 'active' : '' }}" href="{{ route('forum.index') }}">Forum</a>
                        </li>
                        @auth
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('homework.*') ? 'active' : '' }}" href="{{ route('homework.index') }}">Homework</a>
                            </li>
                        @endauth
                    </ul>
                    <div class="d-none d-lg-flex align-items-center">
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Log In</a>
                            <a href="{{ route('register') }}" class="btn btn-primary">Register</a>
                        @else
                            <div class="dropdown">
                                <a href="#" class="nav-link dropdown-toggle d-flex align-items-center" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="avatar me-2">
                                    <span class="fw-bold">{{ auth()->user()->name }}</span>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end mt-2">
                                    <li class="px-3 py-2 dropdown-user-info">
                                        <div class="fw-bold d-flex align-items-center gap-2">
                                            {{ auth()->user()->name }}
                                            @if(auth()->user()->is_verified)
                                                <i class="bi bi-patch-check-fill text-primary" title="Verified"></i>
                                            @endif
                                        </div>
                                        <div class="text-muted small">{{ auth()->user()->email }}</div>
                                    </li>
                                    <li><a class="dropdown-item py-2" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('profile.show', auth()->id()) }}"><i class="bi bi-person-circle"></i> View Profile</a></li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger py-2"><i class="bi bi-box-arrow-right"></i> Logout</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @endguest
                    </div>
                </div>
            </div>
        </nav>

        <div class="offcanvas offcanvas-end d-lg-none" tabindex="-1" id="offcanvasNavbar" aria-labelledby="offcanvasNavbarLabel">
            <div class="offcanvas-header border-bottom">
                @auth
                <div class="d-flex align-items-center">
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="avatar me-2">
                    <div>
                        <div class="fw-bold d-flex align-items-center gap-2">
                            {{ auth()->user()->name }}
                            @if(auth()->user()->is_verified)
                                <i class="bi bi-patch-check-fill text-primary" title="Verified"></i>
                            @endif
                        </div>
                        <div class="text-muted small">{{ auth()->user()->role }}</div>
                    </div>
                </div>
                @else
                 <h5 class="offcanvas-title fw-bold" id="offcanvasNavbarLabel">{{ config('app.name') }}</h5>
                @endauth
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body">
                <ul class="navbar-nav justify-content-end flex-grow-1 pe-3">
                    <li class="nav-item"><a class="nav-link fs-5 py-2 {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link fs-5 py-2 {{ request()->routeIs('courses.*') ? 'active' : '' }}" href="{{ route('courses.index') }}">Courses</a></li>
                    <li class="nav-item"><a class="nav-link fs-5 py-2 {{ request()->routeIs('forum.*') ? 'active' : '' }}" href="{{ route('forum.index') }}">Forum</a></li>
                    @auth
                        <li class="nav-item"><a class="nav-link fs-5 py-2 {{ request()->routeIs('homework.*') ? 'active' : '' }}" href="{{ route('homework.index') }}">Homework</a></li>
                        <li class="nav-item mt-3 pt-3 border-top"><a class="nav-link fs-5 py-2 {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="nav-item"><a class="nav-link fs-5 py-2 {{ request()->routeIs('profile.show') ? 'active' : '' }}" href="{{ route('profile.show', auth()->id()) }}">View Profile</a></li>
                        <li class="nav-item">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-link fs-5 py-2 text-danger">Logout</button>
                            </form>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>

        <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055">
            <div id="flash-toast-container">
                 @if(session('success'))
                    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header text-bg-success">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            <strong class="me-auto">Success</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            {{ session('success') }}
                        </div>
                    </div>
                @endif
                @if(session('error'))
                    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header text-bg-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong class="me-auto">Error</strong>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                            {{ session('error') }}
                        </div>
                    </div>
                @endif
                 @if ($errors->any())
                    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                        <div class="toast-header text-bg-danger">
                             <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong class="me-auto">Validation Error</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                        </div>
                        <div class="toast-body">
                           <ul class="mb-0 ps-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <main>
            {{ $slot }}
        </main>

        <footer>
            <div class="container py-5">
                <div class="row gy-4">
                    <div class="col-lg-4">
                        <h5>{{ config('app.name', 'Learning Platform') }}</h5>
                        <p class="mt-3">Platform pembelajaran online untuk meningkatkan keahlian dan pengetahuan Anda ke level berikutnya.</p>
                        <div class="d-flex gap-3 mt-4">
                            <a href="#"><i class="bi bi-twitter-x fs-5"></i></a>
                            <a href="#"><i class="bi bi-facebook fs-5"></i></a>
                            <a href="#"><i class="bi bi-instagram fs-5"></i></a>
                            <a href="#"><i class="bi bi-linkedin fs-5"></i></a>
                        </div>
                    </div>
                    <div class="col-lg-2 col-6">
                        <h5>Platform</h5>
                        <ul class="list-unstyled mt-3 d-grid gap-2">
                            <li><a href="{{ route('courses.index') }}">Courses</a></li>
                            <li><a href="{{ route('forum.index') }}">Forum</a></li>
                            <li><a href="{{ route('homework.index') }}">Homework</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-2 col-6">
                        <h5>Support</h5>
                        <ul class="list-unstyled mt-3 d-grid gap-2">
                            <li><a href="#">Help Center</a></li>
                            <li><a href="#">Contact Us</a></li>
                            <li><a href="#">FAQ</a></li>
                        </ul>
                    </div>
                    <div class="col-lg-4">
                        <h5>Subscribe Newsletter</h5>
                        <p class="mt-3">Dapatkan update terbaru mengenai kursus dan fitur langsung di email Anda.</p>
                        <form>
                            <div class="input-group">
                                <input type="email" class="form-control" placeholder="Your email address" style="padding: 0.625rem 1.25rem;">
                                <button class="btn btn-primary" type="button">Subscribe</button>
                            </div>
                        </form>
                    </div>
                </div>
                <hr class="my-4">
                <div class="text-center">
                    <p class="mb-0">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
                </div>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'));
            var toastList = toastElList.map(function(toastEl) {
                return new bootstrap.Toast(toastEl, { autohide: true, delay: 5000 });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>