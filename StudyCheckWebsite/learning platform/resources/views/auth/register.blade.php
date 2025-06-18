<x-guest-layout>
     <div class="row g-0 min-vh-100">
        <div class="col-lg-6 d-none d-lg-flex auth-brand-col">
            <div class="text-white text-center p-5">
                <i class="bi bi-mortarboard-fill" style="font-size: 80px;"></i>
                <h1 class="display-4 fw-bolder mt-3">{{ config('app.name') }}</h1>
                <p class="lead mt-2">Join a community of learners and experts today.</p>
            </div>
        </div>

        <div class="col-12 col-lg-6 auth-form-col">
            <div class="w-100" style="max-width: 450px;">
                <div class="text-center mb-4">
                    <h3 class="fw-bold">Create Your Account</h3>
                    <p class="text-muted">It's free and only takes a minute.</p>
                </div>
                
                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Full Name</label>
                        <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label fw-semibold">Email Address</label>
                        <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                     <div class="mb-3">
                        <label for="role" class="form-label fw-semibold">Register as a</label>
                        <select class="form-select form-select-lg @error('role') is-invalid @enderror" id="role" name="role" required>
                            <option value="" disabled {{ old('role') ? '' : 'selected' }}>Select your role</option>
                            <option value="student" {{ old('role') === 'student' ? 'selected' : '' }}>Student</option>
                            <option value="expert" {{ old('role') === 'expert' ? 'selected' : '' }}>Expert / Tutor</option>
                        </select>
                        @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">Password</label>
                        <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" id="password" name="password" required>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
                        <input type="password" class="form-control form-control-lg" id="password_confirmation" name="password_confirmation" required>
                    </div>

                    <div class="d-grid my-4">
                        <button type="submit" class="btn btn-primary btn-lg">Create Account</button>
                    </div>
                </form>

                <p class="text-center text-muted mt-4">
                    Already have an account? <a href="{{ route('login') }}" class="fw-bold text-decoration-none">Sign in</a>
                </p>
            </div>
        </div>
    </div>
</x-guest-layout>