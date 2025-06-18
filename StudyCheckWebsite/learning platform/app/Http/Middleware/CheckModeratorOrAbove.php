<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModeratorOrAbove
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $allowedRoles = ['moderator', 'admin'];
        
        if (!in_array(auth()->user()->role, $allowedRoles)) {
            abort(403, 'You need moderator or admin privileges to access this page.');
        }

        return $next($request);
    }
}