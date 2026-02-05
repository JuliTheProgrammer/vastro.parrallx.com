<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

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
        $logo = config('app.logo');
        $logoUrl = $logo ?: '/logo.svg';
        if ($logoUrl && ! str_starts_with($logoUrl, 'http')) {
            $publicPath = public_path(ltrim($logoUrl, '/'));
            if (! file_exists($publicPath)) {
                $logoUrl = '/logo.svg';
            }

            $logoUrl = asset($logoUrl);
        }

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'logo' => $logoUrl,
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
