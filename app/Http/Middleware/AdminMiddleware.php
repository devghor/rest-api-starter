<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use App\Enums\StatusCodeEnum;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user->hasRole([RoleEnum::SUPER_ADMIN_ROLE_NAME])) {
            return response('You have no permission', StatusCodeEnum::HTTP_FORBIDDEN);
        }
        return $next($request);
    }
}
