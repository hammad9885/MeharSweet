<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use App\Models\UserPermission;
use App\Models\Module;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $permissions = [];
        if ($request->user()) {
            $user = $request->user();
            try {
                if ($user->is_admin) {
                    $permissions = Module::pluck('name')->toArray();
                } else {
                    $query = UserPermission::join('module', 'module.id', '=', 'user_permission.module_id')
                        ->where('user_permission.userid', $user->id)
                        ->where('user_permission.read', 1);

                    if ($user->cid) {
                        $query->where('user_permission.cid', $user->cid);
                    }

                    $permissions = $query->pluck('module.name')->toArray();
                }
            } catch (\Throwable $e) {
                $permissions = [];
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'permissions' => $permissions,
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
                'status' => fn () => $request->session()->get('status'),
            ],
        ];
    }
}
