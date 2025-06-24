<?php

namespace Webkul\PhoneAuth\Http\Controllers\Api\Traits;

use Illuminate\Http\Request;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

trait ProvideUser
{
    /**
     * Set default auth driver for shop (customer).
     *
     * @return void
     */
    public function setShopAuthDriver(Request $request)
    {
        if (class_exists(EnsureFrontendRequestsAreStateful::class) && EnsureFrontendRequestsAreStateful::fromFrontend($request)) {
            auth()->setDefaultDriver('customer');
        }
    }

    /**
     * Resolve shop user (customer).
     *
     * @return \Webkul\PhoneAuth\Models\Customer|null
     */
    public function resolveShopUser(Request $request)
    {
        if (class_exists(EnsureFrontendRequestsAreStateful::class) && EnsureFrontendRequestsAreStateful::fromFrontend($request)) {
            return auth('customer')->user();
        }

        return $request->user();
    }
}
