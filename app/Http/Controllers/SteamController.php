<?php

namespace App\Http\Controllers;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Invisnik\LaravelSteamAuth\SteamAuth;

class SteamController extends Controller
{
    /**
     * The SteamAuth instance.
     *
     * @var SteamAuth
     */
    protected SteamAuth $steam;

    /**
     * The redirect URL.
     *
     * @var string
     */
    protected string $redirectURL = '/';

    /**
     * AuthController constructor.
     *
     * @param SteamAuth $steam
     */
    public function __construct(SteamAuth $steam)
    {
        $this->steam = $steam;
    }

    /**
     * Redirect the user to the authentication page.
     *
     * @return RedirectResponse
     */
    public function redirectToSteam(): RedirectResponse
    {
        $this->steam->setRedirectUrl(route('auth.steam.handle'));

        return $this->steam->redirect();
    }

    public function unlink(Request $request): RedirectResponse
    {
        $request->user()->steam()->delete();

        return redirect()->route('profile');
    }

    /**
     * Get user info and log in.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function handle(Request $request): RedirectResponse
    {
        try {
            if ($this->steam->validate()) {
                $info = $this->steam->getUserInfo();

                if ( ! is_null($info)) {
                    $request->user()->steam()->create([
                        'steam_id' => $info->steamID64,
                        'name' => $info->personaname,
                        'profile_url' => $info->profileurl,
                    ]);

                    return redirect()->route('profile');
                }
            }
        } catch (GuzzleException|Exception $e) {
            Log::error('Error handling steam connection', [$e]);
        }

        return $this->redirectToSteam();
    }
}
