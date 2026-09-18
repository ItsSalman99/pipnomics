<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserOnlineStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($user = $request->user()) {
            // If user's last_seen_at is older than 2 minutes or not marked online, update it
            if (!$user->is_online || !$user->last_seen_at || $user->last_seen_at->diffInMinutes(now()) >= 2) {
                $user->update([
                    'is_online' => true,
                    'last_seen_at' => now(),
                ]);
            }
        }

        return $next($request);
    }
}
