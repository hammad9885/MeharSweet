<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserPermission;

class LoadUserPermissions
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->is_admin) {
                $permissions = \App\Models\Module::pluck('name')->toArray();
            } else {
                $query = UserPermission::join('module', 'module.id', '=', 'user_permission.module_id')
                    ->where('user_permission.userid', $user->id)
                    ->where('user_permission.read', 1);

                if ($user->cid) {
                    $query->where('user_permission.cid', $user->cid);
                }

                $permissions = $query->pluck('module.name')->toArray();
            }

            view()->share('permissions', $permissions);
        }

        return $next($request);
    }
}