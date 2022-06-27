<?php

namespace Pterodactyl\Http\Controllers\Auth\Integrations;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Pterodactyl\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Pterodactyl\Http\Controllers\Controller;

class GitHubController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function construct(): JsonResponse
    {
        if (!config('integrations.github.enabled')) return new JsonResponse(json_encode(['success' => false]), 200, [], null, true);
        $url = 'https://github.com/login/oauth/authorize?client_id='.config('integrations.github.client_id').'&redirect_uri='.config('integrations.github.callback_url').'&scope=read:user&allow_signup=false';
        return new JsonResponse(json_encode(['success' => true, 'url' => $url]), 200, [], null, true);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function callback(Request $request): RedirectResponse
    {
        $code = Http::withHeaders(["Accept" => "application/json"])->asForm()->post('https://github.com/login/oauth/access_token', [
            'client_id' => config('integrations.github.client_id'),
            'client_secret' => config('integrations.github.client_secret'),
            'code' => $request->input('code'),
            'redirect_uri' => config('integrations.github.callback_url')
        ]);

        if (!$code->ok()) {
            return redirect('/account');
        }

        $req = json_decode($code->body());

        if (preg_match("(read:user)", $req->scope) !== 1) {
            return redirect('/account');
        }

        $user_info = json_decode(Http::withHeaders(["Authorization" => "token " . $req->access_token])->asForm()->get('https://api.github.com/user')->body());

        if (Auth::check()) {
            User::query()->where('id', '=', Auth::user()->id)->update(['github_id' => $user_info->id, 'github_name' => $user_info->login]);
            return redirect('/account/integrations')->with('message', 'Success');
        } else {
            try {$user = User::query()->where('github_id', '=', $user_info->id)->firstOrFail();} catch (Exception $e) {return redirect('/auth/login');}
            if (isset($user->id)) {
                User::query()->where('github_id', '=', $user->id)->update(['github_name' => $user_info->login]);
                Auth::loginUsingId($user->id, true);
            }
            return redirect('/');
        }
    }

    /**
     * @param Request $request
     * @return array
     */
    public function unlink(Request $request): array
    {
        User::query()->where('id', '=', $request->user()->id)->update(['github_id' => null, 'github_name' => null]);
        return [
            'success' => true,
            'data' => []
        ];
    }
}
