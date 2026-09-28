<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Pemakaian di rute: ->middleware('role:admin') atau 'role:admin,kasir'
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        if (! in_array($user->role, $roles, true)) {
            return response()->view('errors.akses-ditolak', [
                'role' => $user->role,
                'dibutuhkan' => implode(' / ', $roles),
            ], 403);
        }

        return $next($request);
    }
}