<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $allowedRoles = explode('|', $roles);
        $hasAccess = false;

        foreach ($allowedRoles as $role) {
            if ($user->role_type === $role || $user->hasRole($role)) {
                $hasAccess = true;
                break;
            }
        }

        if (!$hasAccess) {
            $redirectRoute = $this->getRedirectRoute($user->role_type);
            return redirect($redirectRoute)->with('error', 'You do not have access to this page.');
        }

        return $next($request);
    }

    protected function getRedirectRoute(?string $roleType): string
    {
        return match ($roleType) {
            'superadmin' => 'dashboard',
            'admin' => 'dashboard',
            'accountant' => 'dashboard',
            default => 'home',
        };
    }
}
