<?php

namespace App\Http\Controllers;

use App\Utils\TwitchUtils;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MainController extends Controller
{
    /**
     * @return View
     */
    public function home(): View
    {
        return view('home', [
            'hasUser' => Auth::check(),
        ]);
    }

    /**
     * @param Request $request
     *
     * @return View
     */
    public function dashboard(Request $request): View
    {
        $user = $request->user();
        $enabled = false;
        if ( ! is_null($user->channel)) {
            $enabled = $user->channel->enabled;
        }

        return view('dashboard', [
            'isBroadcaster' => $user->broadcaster,
            'disabled' => ! $enabled,
        ]);
    }

    /**
     * @return View
     */
    public function profile(): View
    {
        $user = Auth::user();

        return view('profile', [
            'display_name' => $user->display_name,
            'name' => $user->name,
            'steam' => $user->steam,
        ]);
    }

    public function profileDelete(): RedirectResponse
    {
        try {
            $user = Auth::user();
            if ( ! is_null($user)) {
                $channel = $user->channel;
                if ( ! is_null($channel)) {
                    $channel->delete();
                }
                $user->delete();
            }
        } catch (Exception $e) {
        }
        TwitchUtils::revokeToken(Auth::user()->access_token);
        Auth::logout();

        return redirect()->route('login')->with('success', 'Successfully removed account');
    }

    /**
     * @return View
     */
    public function privacy(): View
    {
        return view('privacy_tos', [
            'home' => route('home'),
        ]);
    }

    /**
     * @return View
     */
    public function about(): View
    {
        return view('about');
    }
}
