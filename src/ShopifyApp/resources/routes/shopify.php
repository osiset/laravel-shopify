<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| All the routes for the Shopify App setup.
|
*/

use Illuminate\Support\Facades\Route;
use Osiset\ShopifyApp\Util;

// Check if manual routes override is to be use
$manualRoutes = Util::getShopifyConfig('manual_routes');

if ($manualRoutes) {
    // Get a list of route names to exclude
    $manualRoutes = explode(',', $manualRoutes);
}

// Route which require ITP checks
Route::group(['prefix' => Util::getShopifyConfig('prefix'), 'middleware' => ['itp', 'web']], function () use ($manualRoutes) {
    /*
    |--------------------------------------------------------------------------
    | Home Route
    |--------------------------------------------------------------------------
    |
    | Homepage for an authenticated store. Store is checked with the
    | auth.shopify middleware and redirected to login if not.
    |
    */

    if (Util::registerPackageRoute('home', $manualRoutes)) {
        Route::get(
            '/',
            'Osiset\ShopifyApp\Http\Controllers\HomeController@index'
        )
        ->middleware(['auth.shopify', 'billable'])
        ->name(Util::getShopifyConfig('route_names.home'));
    }

    /*
    |--------------------------------------------------------------------------
    | ITP
    |--------------------------------------------------------------------------
    |
    | Handles ITP and issues with it.
    |
    */

    if (Util::registerPackageRoute('itp', $manualRoutes)) {
        Route::get('/itp', 'Osiset\ShopifyApp\Http\Controllers\ItpController@attempt')
            ->name(Util::getShopifyConfig('route_names.itp'));
    }

    if (Util::registerPackageRoute('itp.ask', $manualRoutes)) {
        Route::get('/itp/ask', 'Osiset\ShopifyApp\Http\Controllers\ItpController@ask')
            ->name(Util::getShopifyConfig('route_names.itp.ask'));
    }
});

// Routes without ITP checks
Route::group(['prefix' => Util::getShopifyConfig('prefix'), 'middleware' => ['web']], function () use ($manualRoutes) {
    /*
    |--------------------------------------------------------------------------
    | Authenticate Method
    |--------------------------------------------------------------------------
    |
    | Authenticates a shop.
    |
    */

    if (Util::registerPackageRoute('authenticate', $manualRoutes)) {
        Route::match(
            ['get', 'post'],
            '/authenticate',
            'Osiset\ShopifyApp\Http\Controllers\AuthController@authenticate'
        )
        ->name(Util::getShopifyConfig('route_names.authenticate'));
    }

    /*
    |--------------------------------------------------------------------------
    | Authenticate OAuth
    |--------------------------------------------------------------------------
    |
    | Redirect to Shopify's OAuth screen.
    |
    */

    if (Util::registerPackageRoute('authenticate.oauth', $manualRoutes)) {
        Route::get(
            '/authenticate/oauth',
            'Osiset\ShopifyApp\Http\Controllers\AuthController@oauth'
        )
        ->name(Util::getShopifyConfig('route_names.authenticate.oauth'));
    }

    /*
    |--------------------------------------------------------------------------
    | Billing Handler
    |--------------------------------------------------------------------------
    |
    | Billing handler. Sends to billing screen for Shopify.
    |
    */

    if (Util::registerPackageRoute('billing', $manualRoutes)) {
        Route::get(
            '/billing/{plan?}',
            'Osiset\ShopifyApp\Http\Controllers\BillingController@index'
        )
        ->middleware(['auth.shopify'])
        ->where('plan', '^([0-9]+|)$')
        ->name(Util::getShopifyConfig('route_names.billing'));
    }

    /*
    |--------------------------------------------------------------------------
    | Billing Processor
    |--------------------------------------------------------------------------
    |
    | Processes the customer's response to the billing screen.
    |
    */

    if (Util::registerPackageRoute('billing.process', $manualRoutes)) {
        Route::get(
            '/billing/process/{plan?}',
            'Osiset\ShopifyApp\Http\Controllers\BillingController@process'
        )
        ->middleware(['auth.shopify'])
        ->where('plan', '^([0-9]+|)$')
        ->name(Util::getShopifyConfig('route_names.billing.process'));
    }

    /*
    |--------------------------------------------------------------------------
    | Billing Processor for Usage Charges
    |--------------------------------------------------------------------------
    |
    | Creates a usage charge on a recurring charge.
    |
    */

    if (Util::registerPackageRoute('billing.usage_charge', $manualRoutes)) {
        Route::match(
            ['get', 'post'],
            '/billing/usage-charge',
            'Osiset\ShopifyApp\Http\Controllers\BillingController@usageCharge'
        )
        ->middleware(['auth.shopify'])
        ->name(Util::getShopifyConfig('route_names.billing.usage_charge'));
    }
});
