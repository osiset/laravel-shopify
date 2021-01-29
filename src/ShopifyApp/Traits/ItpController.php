<?php

namespace Osiset\ShopifyApp\Traits;

use Illuminate\Contracts\View\View as ViewView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;

/**
 * Responsible for handling ITP issues.
 */
trait ItpController
{
    /**
     * First-pass of ITP mitigation.
     * Attempt to set ITP cookie.
     *
     * @param Request $request The request object.
     *
     * @return RedirectResponse
     */
    public function attempt(Request $request)
    {
        // Create samesite cookie
        Cookie::queue('itp', true, 6000);

        return Redirect::route('home', [
            'shop' => $request->query('shop'),
            'itp'  => true,
        ]);
    }

    /**
     * Second-pass of ITP mitigation.
     * Ask the user for cookie/storage permissions.
     *
     * @return ViewView
     */
    public function ask(): ViewView
    {
        return View::make('shopify-app::itp.ask', [
            'redirect' => URL::route('home'),
        ]);
    }
}
