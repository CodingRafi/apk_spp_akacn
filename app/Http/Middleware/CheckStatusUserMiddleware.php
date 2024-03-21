<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckStatusUserMiddleware
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
        if (!Auth::user()->hasRole('admin')) {
            if (
                $request->isMethod('post') ||
                $request->isMethod('patch') ||
                $request->isMethod('put') ||
                $request->isMethod('delete')
            ) {
                $role = getRole();
                $status = Auth::user()->{$role->name}->status;
                
                if ($status == '0') {
                    return response()->json([
                        'message' => 'Your account has been deactivated. Please contact the administrator.',
                    ], 400);
                }
            }
        }
        
        return $next($request);
    }
}
