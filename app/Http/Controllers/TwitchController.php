<?php

namespace App\Http\Controllers;

use App\Jobs\SyncChannel;
use App\Jobs\SyncUser;
use App\Utils\TwitchUtils;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use romanzipp\Twitch\Enums\Scope;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TwitchController extends Controller
{

    /**
     * @return View|RedirectResponse
     */
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('login');
    }

    /**
     * @return RedirectResponse
     */
    public function authorizeTwitch(): RedirectResponse
    {
        return Socialite::driver('twitch')
            ->setScopes([Scope::CHANNEL_READ_SUBSCRIPTIONS, "user:read:subscriptions"])
            ->redirect();
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function token(Request $request): RedirectResponse
    {
        // Check if there where an error authing the user
        $error = $request->get('error');
        if (!is_null($error)) {
            if ($error == "access_denied") {
                return $this->redirectError(['Authorization canceled']);
            } else {
                return $this->redirectError([$request->get('error_description')]);
            }
        }

        // Get user from socialite driver
        try {
            $twitchUser = Socialite::driver('twitch')->user();
        } catch (InvalidStateException $e) {
            report($e);
            return $this->redirectError(['Invalid session, try refreshing before retrying']);
        } catch (Exception $e) {
            report($e);
            return $this->redirectError(['Unknown error', $e->getMessage()]);
        }

        // Create/Update and login user
        if (($user = TwitchUtils::handleDbUserLogin($twitchUser)) && !isset($user)) {
            return $this->redirectError(['Error creating user']);
        }

        // Sync user subscription status and channel
        $channel = $user->channel;
        if (isset($channel) && $channel->enabled && $channel->sync) {
            SyncChannel::dispatch($channel);
        }
        SyncUser::dispatch($user, $twitchUser->token);

        // Redirect back or to dashboard
        $redirect = Session::get('redirect');
        if (!is_null($redirect)) {
            Session::remove('redirect');
            return redirect($redirect);
        } else {
            return redirect()->route('dashboard');
        }
    }

    /**
     * @param string[] $message
     * @return RedirectResponse
     */
    private function redirectError($message = ['Something went wrong']): RedirectResponse
    {
        Auth::logout();
        $errors = new ViewErrorBag;
        return redirect()->route('login')->with('errors', $errors->put('default', new MessageBag($message)));
    }

    /**
     * @return RedirectResponse
     */
    public function logout(): RedirectResponse
    {
        Auth::logout();
        return redirect()->route('login');
    }

}
