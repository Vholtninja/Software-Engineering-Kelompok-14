<x-guest-layout>
    <div class="row g-0 min-vh-100">
        <div class="col-lg-6 d-none d-lg-flex auth-brand-col">
            <div class="text-white text-center p-5">
                <i class="bi bi-mortarboard-fill" style="font-size: 80px;"></i>
                <h1 class="display-4 fw-bolder mt-3">{{ config('app.name') }}</h1>
                <p class="lead mt-2">Unlock Your Potential. Start your learning journey with us.</p>
            </div>
        </div>

        <div class="col-12 col-lg-6 auth-form-col">
            <div class="w-100" style="max-width: 450px;">
                <div class="text-center mb-4">
                    <h3 class="fw-bold">Welcome Back!</h3>
                    <p class="text-muted">Sign in to continue your learning.</p>
                </div>

                @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                @endif
                
                @error('email')
                    <div class="alert alert-danger" role="alert">
                        {{ $message }}
                    </div>
                @enderror

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email</label>
                        <input type="email" class="form-control form-control-lg" id="email" name="email" value="{{ old('email') }}" required autofocus>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <input type="password" class="form-control form-control-lg" id="password" name="password" required>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                            <label class="form-check-label" for="remember_me">Remember me</label>
                        </div>
                        @if (Route::has('password.request'))
                            {{-- <a href="{{ route('password.request') }}" class="small text-decoration-none">Forgot password?</a> --}}
                        @endif
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">Sign In</button>
                    </div>
                </form>

                <p class="text-center text-muted mt-4">
                    Don't have an account? <a href="{{ route('register') }}" class="fw-bold text-decoration-none">Sign up</a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>