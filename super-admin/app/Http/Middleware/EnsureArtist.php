<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureArtist
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || (! $user->is_artist && ! $user->is_super_admin)) {
            if ($request->expectsJson()) {
                abort(403, 'This account does not have artist access.');
            }

            auth()->logout();
            return redirect()->route('artist-studio.login')
                ->withErrors(['email' => 'This account does not have artist access.']);
        }

        return $next($request);
    }
}
