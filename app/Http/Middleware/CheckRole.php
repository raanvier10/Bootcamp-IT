<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!$request->user()) {
            return redirect('/login');
        }

        $userRole = strtolower($request->user()->peran ?? '');

        // Map english route roles to DB roles
        $roleMap = [
            'admin' => 'admin',
            'user' => 'pelapor',
            'officer' => 'petugas',
        ];

        $expectedRole = $roleMap[strtolower($role)] ?? strtolower($role);

        if ($userRole !== $expectedRole) {
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
