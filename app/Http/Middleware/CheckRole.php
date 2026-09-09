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
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.'
                ], 401);
            }
            return redirect('/login');
        }

        $userRole = strtolower($request->user()->peran ?? '');

        // Map english route roles to DB roles
        $roleMap = [
            'admin' => 'admin',
            'user' => 'pelapor',
            'pelapor' => 'pelapor',
            'officer' => 'petugas',
            'petugas' => 'petugas',
        ];

        $expectedRole = $roleMap[strtolower($role)] ?? strtolower($role);

        if ($userRole !== $expectedRole) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Akses ditolak: Hak akses tidak sesuai peran.'
                ], 403);
            }
            abort(403, 'Akses ditolak.');
        }

        return $next($request);
    }
}
