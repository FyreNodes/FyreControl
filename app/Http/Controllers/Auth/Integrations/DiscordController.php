<?php

namespace Pterodactyl\Http\Controllers\Auth\Integrations;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use JetBrains\PhpStorm\ArrayShape;
use Pterodactyl\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\RedirectResponse;
use Pterodactyl\Http\Controllers\Controller;

class DiscordController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function construct(): JsonResponse
    {
        if (!config('integrations.discord.enabled')) return new JsonResponse(json_encode(['success' => false]), 200, [], null, true);
        $url = 'https://discord.com/api/v9/oauth2/authorize?client_id=' . config('integrations.discord.client_id') . '&redirect_uri=' . urlencode(config('integrations.discord.callback_url')) . '&response_type=code&scope=identify%20email%20guilds%20guilds.join';
        return new JsonResponse(json_encode(['success' => true, 'url' => $url]), 200, [], null, true);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function callback(Request $request): RedirectResponse
    {
        if ($request->input('error') === 'access_denied') {
            return redirect('/auth/login');
        }

        $code = Http::asForm()->post('https://discord.com/api/v9/oauth2/token', [
            'client_id' => config('integrations.discord.client_id'),
            'client_secret' => config('integrations.discord.client_secret'),
            'grant_type' => 'authorization_code',
            'code' => $request->input('code'),
            'redirect_uri' => config('integrations.discord.callback_url')
        ]);

        if (!$code->ok()) {
            return redirect('/auth/login');
        }

        $req = json_decode($code->body());

        if (preg_match("(email|guilds|identify|guilds.join)", $req->scope) !== 1) {
            return redirect('/account');
        }

        $user_info = json_decode(Http::withHeaders(["Authorization" => "Bearer " . $req->access_token])->asForm()->get('https://discord.com/api/users/@me')->body());
        $banned = Http::withHeaders(["Authorization" => "Bot " . config('integrations.discord.bot_token')])->get('https://discord.com/api/guilds/' . config('integrations.discord.guild_id') . '/bans/' . $user_info->id);

        if ($banned->ok()) {
            return redirect('/account');
        }

        if (Auth::check()) {
            User::query()->where('id', '=', Auth::user()->id)->update(['discord' => true, 'discord_id' => $user_info->id, 'discord_name' => $user_info->username.'#'.$user_info->discriminator]);
            Http::withHeaders(["Authorization" => "Bot ".config('integrations.discord.bot_token')])->put('https://discord.com/api/v9/guilds/'.config('integrations.discord.guild_id').'/members/'.$user_info->id, ['access_token' => $req->access_token]);
            Http::withHeaders(["Authorization" => "Bot ".config('integrations.discord.bot_token'), "X-Audit-Log-Reason" => "Automated Action: FyreID Linked"])->put('https://discord.com/api/v9/guilds/'.config('integrations.discord.guild_id').'/members/'.$user_info->id.'/roles/'.config('integrations.discord.verified'));
            return redirect('/account')->with('message', 'Success');
        } else {
            try {$user = User::query()->where('discord_id', '=', $user_info->id)->firstOrFail();} catch (Exception $e) {return redirect('/account');}
            if (isset($user->id)) {
                User::query()->where('discord_id', '=', $user->id)->update(['discord_name' => $user_info->username.'#'.$user_info->discriminator]);
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
        Http::withHeaders(["Authorization" => "Bot ".config('integrations.discord.bot_token'), "X-Audit-Log-Reason" => "Automated Action: FyreID Unlinked"])->delete('https://discord.com/api/v9/guilds/'.config('integrations.discord.guild_id').'/members/'.$request->user()->discord_id.'/roles/'.config('integrations.discord.verified'));
        User::query()->where('id', '=', $request->user()->id)->update(['discord_id' => null, 'discord_name' => null]);
        return [
            'success' => true,
            'data' => []
        ];
    }
}
