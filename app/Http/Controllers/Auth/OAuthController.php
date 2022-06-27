<?php

namespace Pterodactyl\Http\Controllers\Auth;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\RedirectResponse;
use Laravel\Socialite\Facades\Socialite;
use Pterodactyl\Http\Controllers\Controller;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;
use Pterodactyl\Services\Users\UserUpdateService;
use Pterodactyl\Contracts\Repository\UserRepositoryInterface;
use Pterodactyl\Exceptions\Repository\RecordNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class OAuthController extends Controller
{
    protected AuthManager $auth;

    private UserUpdateService $updateService;

    private UserRepositoryInterface $repository;

    /**
     * The route to redirect a user once linked with the OAuth provider or if the provider doesn't exist.
     */
    protected string $redirectRoute = 'account';

    /**
     * LoginController constructor.
     */
    public function __construct(AuthManager $auth, UserUpdateService $updateService, UserRepositoryInterface $repository)
    {
        $this->auth = $auth;
        $this->updateService = $updateService;
        $this->repository = $repository;
    }

    /**
     * Redirect to the provider's website.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function redirect(Request $request): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        if (!app('config')->get('oauth.enabled')) {
            throw new NotFoundHttpException();
        }

        $drivers = json_decode(app('config')->get('oauth.drivers'), true);
        $driver = $request->get('driver');

        if ($driver == null || !array_has($drivers, $driver) || !$drivers[$driver]['enabled']) {
            return redirect()->route('auth.login');
        }

        // Dirty hack
        // Can't use SocialiteProviders\Manager\Config since all providers are hardcoded for services.php
        config(['services.' . $driver => array_merge(
            array_only($drivers[$driver], ['client_id', 'client_secret']),
            ['redirect' => route('oauth.callback')]
        )]);

        $request->session()->put('oauth_driver', $driver);

        return Socialite::driver($driver)->redirect();
    }

    /**
     * Validate and login OAuth user.
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Throwable
     */
    protected function callback(Request $request): RedirectResponse
    {
        // If logged in link provider to user
        if ($request->user() != null) {
            return $this->link($request);
        }

        $driver = $request->session()->pull('oauth_driver');

        if (empty($driver)) {
            return redirect()->route('auth.login');
        }

        $drivers = json_decode(app('config')->get('oauth.drivers'), true);

        // Dirty hack
        // Can't use SocialiteProviders\Manager\Config since all providers are hardcoded for services.php
        config(['services.' . $driver => array_merge(
            array_only($drivers[$driver], ['client_id', 'client_secret']),
            ['redirect' => route('oauth.callback')]
        )]);

        $oauthUser = Socialite::driver($driver)->user();

        try {
            $user = $this->repository->findFirstWhere([['oauth->' . $driver, $oauthUser->getId()]]);
        } catch (RecordNotFoundException $e) {
            return redirect()->route('auth.login');
        }

        $this->auth->guard()->login($user, true);

        return redirect('/');
    }

    /**
     * Link OAuth id to user.
     *
     * @throws Throwable
     */
    private function link(Request $request): RedirectResponse
    {
        $driver = $request->session()->pull('oauth_linking');

        if (empty($driver)) {
            return redirect($this->redirectRoute);
        }

        $drivers = json_decode(app('config')->get('oauth.drivers'), true);

        // Dirty hack
        // Can't use SocialiteProviders\Manager\Config since all providers are hardcoded for services.php
        config(['services.' . $driver => array_merge(
            array_only($drivers[$driver], ['client_id', 'client_secret']),
            ['redirect' => route('oauth.callback')]
        )]);

        $oauthUser = Socialite::driver($driver)->user();

        $oauth = json_decode($request->user()->oauth, true);

        $oauth[$driver] = $oauthUser->getId();

        $this->updateService->handle($request->user(), ['oauth' => json_encode($oauth)]);

        return redirect($this->redirectRoute);
    }
}
