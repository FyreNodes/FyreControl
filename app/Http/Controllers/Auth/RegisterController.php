<?php

namespace Pterodactyl\Http\Controllers\Auth;

use Ramsey\Uuid\Uuid;
use Pterodactyl\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use Pterodactyl\Notifications\Welcome;
use Illuminate\Contracts\Hashing\Hasher;
use Pterodactyl\Exceptions\DisplayException;
use Pterodactyl\Http\Requests\Auth\RegisterRequest;
use Illuminate\Contracts\View\Factory as ViewFactory;

class RegisterController extends AbstractLoginController
{
    private Hasher $hasher;
    private ViewFactory $view;

    /**
     * LoginController constructor.
     *
     * @param ViewFactory $view
     * @param Hasher $hasher
     */
    public function __construct(ViewFactory $view, Hasher $hasher) {
        parent::__construct();
        $this->view = $view;
        $this->hasher = $hasher;
    }

    /**
     * @return View
     */
    public function index(): View
    {
        return $this->view->make('templates/auth.core');
    }

    /**
     * @param RegisterRequest $request
     * @return JsonResponse
     * @throws DisplayException
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $authorized = [
            'thefallenspirit@outlook.com' => 'thefallenspirit@outlook.com',
            'liamlabell21@gmail.com' => 'liamlabell21@gmail.com'
        ];
        if (!array_key_exists($request->input('email'), $authorized)) throw new DisplayException('You do not have access to FyreControl.');

        $data = [
            'uuid' => Uuid::uuid4()->toString(),
            'username' => $request->input('username'),
            'email' => $request->input('email'),
            'name_first' => $request->input('name_first'),
            'name_last' => $request->input('name_last'),
            'password' => $this->hasher->make($request->input('password')),
            'root_admin' => false
        ];

        $user = User::forceCreate($data);
        $user->notify(new Welcome($user));

        return new JsonResponse([
            'data' => [
                'complete' => true,
                'intended' => $this->redirectPath(),
            ],
        ]);
    }
}
