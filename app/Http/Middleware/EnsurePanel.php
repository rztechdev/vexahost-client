<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Memisahkan Admin Panel (staf internal) dan Client Panel (klien).
 *
 * Pemakaian: ->middleware('panel:admin') atau ->middleware('panel:client')
 */
class EnsurePanel
{
    public function handle(Request $request, Closure $next, string $panel): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $allowed = $panel === 'client' ? $user->isClient() : $user->isStaff();

        if (! $allowed) {
            if ($request->expectsJson()) {
                abort(403, 'Anda tidak memiliki akses ke panel ini.');
            }

            return redirect()->to($user->homeUrl());
        }

        return $next($request);
    }
}
