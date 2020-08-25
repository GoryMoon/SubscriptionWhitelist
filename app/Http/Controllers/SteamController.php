<?php

namespace App\Http\Controllers;

use App\Utils\TwitchUtils;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Log;
use Invisnik\LaravelSteamAuth\SteamAuth;

class SteamController extends Controller
{
    /**
     * The SteamAuth instance.
     *
     * @var SteamAuth
     */
    protected $steam;

    /**
     * The redirect URL.
     *
     * @var string
     */
    protected $redirectURL = '/';

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
     * Redirect the user to the authentication page
     *
     * @return RedirectResponse|Redirector
     */
    public function redirectToSteam()
    {
        $this->steam->setRedirectUrl(route('auth.steam.handle'));
        return $this->steam->redirect();
    }

    public function unlink()
    {
        $user = TwitchUtils::getDbUser();
        $user->steam()->delete();
        return redirect()->route('profile');
    }

    /**
     * Get user info and log in
     *
     * @return RedirectResponse|Redirector
     */
    public function handle()
    {
        try {
            if ($this->steam->validate()) {
                $info = $this->steam->getUserInfo();

                if (!is_null($info)) {
                    $user = TwitchUtils::getDbUser();
                    $user->steam()->create([
                        'steam_id' => $info->steamID64,
                        'name' => $info->personaname,
                        'profile_url' => $info->profileurl
                    ]);

                    return redirect()->route('profile');
                }
            }
        } catch (GuzzleException | Exception $e) {
            Log::error("Error handling steam connection", array($e));
        }
        return $this->redirectToSteam();
    }
}
