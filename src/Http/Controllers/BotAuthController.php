<?php

namespace ZetRider\BotAuth\Http\Controllers;

use App\Http\Controllers\Controller;

use ZetRider\BotAuth\Traits\BotAuthControllerTrait;

use Illuminate\Http\Request;
use Auth;

class BotAuthController extends Controller
{
    use BotAuthControllerTrait;

    /**
     * Show auth form.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $secret = $this->getSecretPrefix() . uniqid();
        $logins = null;
        if(Auth::check())
        {
            $logins = Auth::User()->botAuth()->get();
        }
        return view('botauth::botauth', [
            'secret' => $secret,
            'logins' => $logins,
        ]);
    }

    /**
     * Confirm code
     *
     * @param  Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function check(Request $request)
    {
        $success = false;
        $message = __('User not found');

        if($auth = $this->secretIsset($request->secret))
        {
            // Login user
            Auth::login($auth->user, true);

            $success = true;
            $message = __('Success');
        }

        if($request->ajax())
        {
            return response()->json([
                'success' => $success
            ]);
        }

        return redirect()->back()->with('message', $message);
    }

    /**
     * Catch callback
     *
     * @param  string $provider driver slug
     * @return mixed
     */
    public function callback($provider)
    {
        $this->callbackHandler($provider);
    }
}
