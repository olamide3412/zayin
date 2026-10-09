<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $this->getAuthUser('web'),
                'client' => $this->getAuthUser('client'),
                'check' => $this->isAuthenticated(),
                'type' => $this->getAuthType(),
            ],
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
                'message' => fn() => $request->session()->get('message'),
                'success_tracking' => fn() => $request->session()->get('success_tracking'),
            ],
            'site' => [
                'name' => \App\Models\Setting::get('company_name', 'Zayin Technologies'),
                'tagline' => \App\Models\Setting::get('tagline', 'Solar, Starlink & DSTV Solutions'),
                'phone' => \App\Models\Setting::get('phone', '+234 803 123 4567'),
                'whatsapp' => \App\Models\Setting::get('whatsapp', '+234 803 123 4567'),
                'email' => \App\Models\Setting::get('email', 'support@zayin.ng'),
                'address' => \App\Models\Setting::get('address', 'Abraka, Ethiope East, Delta State, Nigeria'),
                'coverage_scope' => \App\Models\Setting::get('coverage_scope', 'Nationwide Service — Operating across all 36 States & FCT'),
                'bank_name' => \App\Models\Setting::get('bank_name', 'Zenith Bank Plc'),
                'bank_account_name' => \App\Models\Setting::get('bank_account_name', 'Zayin Technologies Nigeria Ltd'),
                'bank_account_number' => \App\Models\Setting::get('bank_account_number', '1012345678'),
            ],
            'support' => [
                'phone' => \App\Models\Setting::get('phone', '+234 803 123 4567'),
                'phone_whatsapp' => \App\Models\Setting::get('phone_whatsapp', '2348031234567'),
                'email' => \App\Models\Setting::get('email', 'support@zayin.ng'),
                'location' => \App\Models\Setting::get('address', 'Abraka, Ethiope East, Delta State, Nigeria'),
                'coverage_scope' => \App\Models\Setting::get('coverage_scope', 'Nationwide Service — Operating across all 36 States & FCT'),
            ],
            'nav' => [
                'service_categories' => fn() => \App\Models\ServiceCategory::with(['services' => fn($q) => $q->published()->select('id', 'category_id', 'name', 'slug')])->get(['id', 'name', 'slug', 'icon']),
                'product_categories' => fn() => \App\Models\ProductCategory::select('id', 'name', 'slug', 'icon')->get(),
            ],
            'cart' => [
                'count' => fn() => $this->getCartCount($request),
            ],
            'csrf_token' => csrf_token(),
            'turnstileSiteKey' => config('services.turnstile.site_key'),
        ];
    }

    protected function getCartCount(Request $request): int
    {
        $sessionId = $request->session()->getId();
        $cart = \App\Models\Cart::where('session_id', $sessionId)
            ->when(Auth::check(), fn($q) => $q->orWhere('user_id', Auth::id()))
            ->first();
        return $cart ? $cart->items()->sum('qty') : 0;
    }

    protected function getAuthUser($guard)
    {
        return fn() => Auth::guard($guard)->check() ? Auth::guard($guard)->user() : null;
    }

    protected function isAuthenticated()
    {
        return fn() => Auth::guard('web')->check() || Auth::guard('client')->check();
    }

    protected function getAuthType()
    {
        if (Auth::guard('web')->check())
            return 'staff';
        if (Auth::guard('client')->check())
            return 'client';
        return null;
    }
}
