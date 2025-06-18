<!-- resources/views/admin/index.blade.php -->
<x-app-layout>
    <x-slot name="title">Admin Panel - {{ config('app.name') }}</x-slot>

    <div class="container py-4">
        <h2>Admin Panel</h2>
        <p>Welcome to the admin panel. More features coming soon!</p>
        
        <div class="row">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>User Management</h5>
                        <p>Manage user accounts and roles</p>
                        <a href="#" class="btn btn-primary">Manage Users</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Course Management</h5>
                        <p>Review and moderate courses</p>
                        <a href="{{ route('courses.index') }}" class="btn btn-primary">View Courses</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5>Forum Moderation</h5>
                        <p>Moderate forum discussions</p>
                        <a href="{{ route('forum.index') }}" class="btn btn-primary">Forum</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>