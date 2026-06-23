<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ArtistApiController;
use App\Http\Controllers\ArtistStudioController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\FanAuthController;
use App\Http\Controllers\SuperAdminController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/super-admin');

Route::prefix('api/catalog')->name('catalog.')->group(function () {
    Route::get('/',       [CatalogController::class, 'index'])->name('index');
    Route::get('/{key}',  [CatalogController::class, 'show'])->name('show');
});

Route::prefix('api/fan')->name('fan-api.')->group(function () {
    Route::get('/csrf', fn () => response()->json(['token' => csrf_token()]))->name('csrf');
    Route::post('/register', [FanAuthController::class, 'register'])->name('register');
    Route::post('/login', [FanAuthController::class, 'login'])->name('login');

    Route::post('/checkout',                       [CheckoutController::class, 'initiate'])->name('checkout.initiate');
    Route::get('/checkout/{reference}/status',     [CheckoutController::class, 'status'])->name('checkout.status');

    Route::middleware('auth')->group(function () {
        Route::post('/logout', [FanAuthController::class, 'logout'])->name('logout');
        Route::get('/me', [FanAuthController::class, 'me'])->name('me');
        Route::post('/profile', [FanAuthController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [FanAuthController::class, 'updatePassword'])->name('password.update');
    });
});

Route::prefix('api/artist')->name('artist-api.')->group(function () {
    Route::get('/csrf', fn () => response()->json(['token' => csrf_token()]))->name('csrf');
    Route::post('/check-email', [ArtistApiController::class, 'checkEmail'])->name('check-email');
    Route::post('/login', [ArtistApiController::class, 'login'])->name('login');

    Route::middleware('auth')->group(function () {
        Route::get('/me', [ArtistApiController::class, 'me'])->name('me');
        Route::post('/logout', [ArtistApiController::class, 'logout'])->name('logout');
        Route::get('/releases', [ArtistApiController::class, 'releases'])->name('releases');
        Route::post('/releases', [ArtistApiController::class, 'storeRelease'])->name('releases.store');
        Route::post('/releases/{asset}/tracks/{track}/sample-played', [ArtistApiController::class, 'samplePlayed'])->whereNumber('track')->name('sample-played');
    });
});

// PesaPal IPN webhook (CSRF exempted in bootstrap/app.php).
Route::post('webhooks/pesapal', [CheckoutController::class, 'pesapalIpn']);

Route::prefix('super-admin')->name('super-admin.')->group(function () {
    Route::controller(SuperAdminController::class)->group(function () {
        Route::get('/',            'dashboard')->name('dashboard');
        Route::get('/redemptions', 'redemptions')->name('redemptions');
        Route::get('/users',       'users')->name('users');
        Route::get('/revenue',     'revenue')->name('revenue');
        Route::get('/system',      'system')->name('system');
        Route::get('/audit',       'audit')->name('audit');
        Route::get('/settings',    'settings')->name('settings');
    });

    Route::resource('assets', AssetController::class);
});

Route::prefix('artist-studio')->name('artist-studio.')->group(function () {
    Route::get('login',  [ArtistStudioController::class, 'showLogin'])->name('login');
    Route::post('login', [ArtistStudioController::class, 'login'])->name('login.submit');

    Route::middleware(['auth', 'artist'])->group(function () {
        Route::post('logout', [ArtistStudioController::class, 'logout'])->name('logout');

        Route::get('/',           [ArtistStudioController::class, 'dashboard'])->name('dashboard');
        Route::get('analytics',   [ArtistStudioController::class, 'analytics'])->name('analytics');
        Route::get('payments',    [ArtistStudioController::class, 'payments'])->name('payments');
        Route::get('settings',           [ArtistStudioController::class, 'settings'])->name('settings');
        Route::post('settings/profile',  [ArtistStudioController::class, 'updateProfile'])->name('settings.profile');
        Route::put('settings/password',  [ArtistStudioController::class, 'updatePassword'])->name('settings.password');

        Route::get('releases',                 [ArtistStudioController::class, 'releases'])->name('releases.index');
        Route::get('releases/create',          [ArtistStudioController::class, 'createRelease'])->name('releases.create');
        Route::post('releases',                [ArtistStudioController::class, 'storeRelease'])->name('releases.store');
        Route::get('releases/{asset}/edit',    [ArtistStudioController::class, 'editRelease'])->name('releases.edit');
        Route::put('releases/{asset}',         [ArtistStudioController::class, 'updateRelease'])->name('releases.update');
        Route::delete('releases/{asset}',      [ArtistStudioController::class, 'destroyRelease'])->name('releases.destroy');
    });
});
