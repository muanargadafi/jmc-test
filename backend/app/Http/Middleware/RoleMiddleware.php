<?php

namespace App\Http\Middleware;

use App\Enums\RoleNameEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response) $next
     * @param string ...$roles
     * @return Response
     */

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(Response::HTTP_UNAUTHORIZED, 'Unauthenticated.');
        }

        $roleEnums = array_map(
            static fn(string $role) => RoleNameEnum::from($role),
            $roles
        );

        if (!$user->hasRole(...$roleEnums)) {
            abort(Response::HTTP_FORBIDDEN, 'You do not have permission to access this resource.');
        }

        return $next($request);
    }
}
