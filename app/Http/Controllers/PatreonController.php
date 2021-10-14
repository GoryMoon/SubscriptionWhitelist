<?php

namespace App\Http\Controllers;

use App\Patreon\PatreonAPI;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use stdClass;

class PatreonController extends Controller
{
    /**
     * @return RedirectResponse
     */
    public function redirectToPatreon(): RedirectResponse
    {
        return Socialite::driver('patreon')
            ->redirectUrl(route('auth.patreon.handle'))
            ->setScopes(['identity', 'campaigns', 'campaigns.members'])
            ->redirect();
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function unlink(Request $request): RedirectResponse
    {
        $request->user()->patreon()->delete();

        return redirect()->route('profile');
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function handle(Request $request): RedirectResponse
    {
        $patreonUser = Socialite::driver('patreon')->user();

        $user = $request->user()->patreon()->create([
            'patreon_id' => $patreonUser->getId(),
            'access_token' => $patreonUser->token,
            'refresh_token' => $patreonUser->refreshToken,
        ]);

        $api = new PatreonAPI($user);
        $campaigns = $api->getCampaigns(['fields[campaign]' => ['url', 'vanity']])->data;
        if (count($campaigns) > 1) {
            return redirect()->route('patreon.campaigns.view');
        } elseif (count($campaigns) <= 0) {
            return redirect()->route('profile');
        }
        $user->campaign_id = $campaigns[0]->id;
        $user->vanity = $campaigns[0]->attributes->vanity;
        $user->url = $campaigns[0]->attributes->url;
        $user->save();

        return redirect()->route('profile');
    }

    public function viewCampaigns(Request $request): View
    {
        $api = new PatreonAPI($request->user()->patreon);

        $campaigns = [];

        $options = [
            'fields[user]' => ['thumb_url'],
            'fields[campaign]' => ['url', 'vanity', 'creation_name', 'image_url', 'patron_count', 'summary'],
            'include' => ['creator'],
        ];

        $cursor = null;
        while (true) {
            if ( ! is_null($cursor)) {
                $options['page[cursor]'] = $cursor;
            }
            $response = $api->getCampaigns($options);

            if (isset($response->meta->pagination->cursors) && isset($response->meta->pagination->cursors->next)) {
                $cursor = $response->meta->pagination->cursors->next;
            } else {
                $cursor = null;
            }

            foreach ($response->data as $data) {
                $campaign = new stdClass();
                $campaign->id = $data->id;
                $campaign->vanity = $data->attributes->vanity;
                $campaign->creation_name = $data->attributes->creation_name;
                $campaign->patron_count = $data->attributes->patron_count;
                $campaign->url = $data->attributes->url;
                $campaign->summary = $data->attributes->summary;
                $campaign->image = $data->attributes->image_url;
                $user_id = $data->relationships->creator->data->id;
                foreach ($response->included as $included) {
                    if ($included->id == $user_id) {
                        $campaign->user_image = $included->attributes->thumb_url;
                        break;
                    }
                }
                $campaign->hash = hash_hmac('sha256', $campaign->id . $campaign->vanity . $campaign->url, config('app.key'));
                $campaigns[] = $campaign;
            }

            if (is_null($cursor)) {
                break;
            }
        }

        return view('broadcaster.patreon.campaign', [
            'campaigns' => $campaigns,
        ]);
    }

    public function setCampaign(Request $request): RedirectResponse
    {
        $inputs = $request->validate([
            'id' => 'required|numeric',
            'vanity' => 'required|string',
            'url' => 'required|url',
            'hash' => 'required|string',
        ]);
        $signature = hash_hmac('sha256', $inputs['id'] . $inputs['vanity'] . $inputs['url'], config('app.key'));
        if ( ! hash_equals($signature, $inputs['hash'])) {
            return redirect()->back(400);
        }
        $user = $request->user()->patreon;
        $user->campaign_id = $inputs['id'];
        $user->vanity = $inputs['vanity'];
        $user->url = $inputs['url'];
        $user->save();

        return redirect()->route('profile');
    }
}
