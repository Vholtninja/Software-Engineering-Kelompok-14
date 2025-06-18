<x-app-layout>
    <x-slot name="title">Edit Profile - {{ config('app.name') }}</x-slot>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="mb-4">
                    <h1 class="fw-bold">Edit Profile</h1>
                    <p class="text-muted">Perbarui informasi profil Anda yang akan ditampilkan ke pengguna lain.</p>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="fw-bold mb-0">Profile Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            @method('patch')
                            <div class="row mb-4">
                                <div class="col-md-4 text-center">
                                    <img src="{{ $user->avatar_url }}" alt="Avatar" class="avatar rounded-circle mb-3" id="avatar-preview" style="width: 120px; height: 120px;">
                                    <div>
                                        <label for="avatar" class="btn btn-sm btn-outline-primary">Change Avatar</label>
                                        <input type="file" class="d-none" id="avatar" name="avatar" accept="image/*" onchange="previewAvatar(this)">
                                        <div class="form-text mt-2">JPG, PNG, GIF (Max 2MB)</div>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="name" class="form-label fw-semibold">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label fw-semibold">Email Address</label>
                                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                            <div class="text-warning small mt-2">
                                                Your email address is unverified.
                                                <button form="send-verification" class="btn btn-link btn-sm p-0 m-0 align-baseline">Resend verification email.</button>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="bio" class="form-label fw-semibold">Bio</label>
                                <textarea class="form-control" id="bio" name="bio" rows="4">{{ old('bio', $user->bio) }}</textarea>
                                <div class="form-text">Tell us a little about yourself.</div>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="institution" class="form-label fw-semibold">Institution/School</label>
                                    <input type="text" class="form-control" id="institution" name="institution" value="{{ old('institution', $user->institution) }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="level" class="form-label fw-semibold">Experience Level</label>
                                    <select class="form-select" id="level" name="level" required>
                                        <option value="beginner" {{ old('level', $user->level) === 'beginner' ? 'selected' : '' }}>Beginner</option>
                                        <option value="intermediate" {{ old('level', $user->level) === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                        <option value="advanced" {{ old('level', $user->level) === 'advanced' ? 'selected' : '' }}>Advanced</option>
                                    </select>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('profile.show', auth()->id()) }}" class="btn btn-secondary me-2">Cancel</a>
                                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle-fill me-2"></i>Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
        <form id="send-verification" method="post" action="{{ route('verification.send') }}">
            @csrf
        </form>
    @endif

    @push('scripts')
    <script>
        function previewAvatar(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
    @endpush
</x-app-layout>