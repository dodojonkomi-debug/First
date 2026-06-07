<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Танҳо корбарони дорои нақши мувофиқро мегузаронад.
     * Истифода: ->middleware('role:superadmin,admin_region')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user || !in_array($user->role, $roles, true)) {
            return response()->json(['message' => 'Дастрасӣ манъ аст.'], 403);
        }

        return $next($request);
    }
}
