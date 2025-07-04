<?php

namespace App\Http\Middleware;

use auth;
use Closure;
use Illuminate\Http\Request;

class isAAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->user()->role_as =='1') {
            return $next($request);
        }else{
            return response()->json([
                'status' => 403,
                'message' => 'you do not have admin access',
                'role' => 'user'
            ],403);
        } 
       // return $next($request);
    }
}
