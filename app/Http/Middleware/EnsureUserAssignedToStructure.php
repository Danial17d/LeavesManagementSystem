<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserAssignedToStructure
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if($user->hasRole('Employee') && !$user->structure){
            return redirect()->route('structure-requests.create')->with('error', 'You are not assigned to any structure.');
        }
        return $next($request);
    }
}
