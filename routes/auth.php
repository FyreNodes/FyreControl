<?php

use Illuminate\Support\Facades\Route;
use Pterodactyl\Http\Controllers\Auth;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| Endpoint: /auth
|
*/

// These routes are defined so that we can continue to reference them programatically.
// They all route to the same controller function which passes off to React.
Route::get('/login', [Auth\LoginController::class, 'index'])->name('auth.login');
Route::get('/password', [Auth\LoginController::class, 'index'])->name('auth.forgot-password');
Route::get('/password/reset/{token}', [Auth\LoginController::class, 'index'])->name('auth.reset');

// Apply a throttle to authentication action endpoints, in addition to the
// recaptcha endpoints to slow down manual attack spammers even more. 🤷
//
// @see \Pterodactyl\Providers\RouteServiceProvider
Route::middleware(['throttle:authentication'])->group(function () {
    // Login endpoints.
    Route::post('/login', [Auth\LoginController::class, 'login'])->middleware('recaptcha');
    Route::post('/login/checkpoint', Auth\LoginCheckpointController::class)->name('auth.login-checkpoint');

    Route::get('/register', [Auth\RegisterController::class, 'index'])->name('auth.register');
    Route::post('/register', [Auth\RegisterController::class, 'register'])->middleware('recaptcha');

    // Forgot password route. A post to this endpoint will trigger an
    // email to be sent containing a reset token.
    Route::post('/password', [Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])
        ->name('auth.post.forgot-password')
        ->middleware('recaptcha');
});

// Password reset routes. This endpoint is hit after going through
// the forgot password routes to acquire a token (or after an account
// is created).
Route::post('/password/reset', Auth\ResetPasswordController::class)->name('auth.reset-password');

// Remove the guest middleware and apply the authenticated middleware to this endpoint
// so it cannot be used unless you're already logged in.
Route::post('/logout', [Auth\LoginController::class, 'logout'])->withoutMiddleware('guest')->middleware('auth')->name('auth.logout');

/*
|--------------------------------------------------------------------------
| Integrations Control Routes
|--------------------------------------------------------------------------
|
| Endpoint: /auth/integrations
|
*/
Route::group(['prefix' => 'integrations'], function () {
    Route::group(['prefix' => 'discord'], function () {
        Route::get('/callback', [Auth\Integrations\DiscordController::class, 'callback'])->name('auth.discord.callback');

        Route::post('/unlink', [Auth\Integrations\DiscordController::class, 'unlink'])->name('auth.discord.unlink');
        Route::post('/construct', [Auth\Integrations\DiscordController::class, 'construct'])->name('auth.discord.construct');
    });
    Route::group(['prefix' => 'github'], function () {
        Route::get('/callback', [Auth\Integrations\GitHubController::class, 'callback'])->name('auth.github.callback');

        Route::post('/unlink', [Auth\Integrations\GitHubController::class, 'unlink'])->name('auth.github.unlink');
        Route::post('/construct', [Auth\Integrations\GitHubController::class, 'construct'])->name('auth.github.construct');
    });
});

// Catch any other combinations of routes and pass them off to the Vuejs component.
Route::fallback([Auth\LoginController::class, 'index']);
