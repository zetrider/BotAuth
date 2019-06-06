<?php

namespace ZetRider\BotAuth\Traits;

use App\User;
use ZetRider\BotAuth\Models\BotAuth as BotAuthModel;
use BotAuth;
use Str;

trait BotAuthControllerTrait
{
    protected $secret_prefix = 'auth_';

    protected function getSecretPrefix()
    {
        return $this->secret_prefix;
    }

    /**
     * Check secret
     * @param  string $secret
     * @return mixed bool|ZetRider\BotAuth\Models\BotAuth
     */
    protected function secretIsset($secret)
    {
        if(!empty($secret))
        {
            // Find network login by secret code
            $auth = BotAuthModel::where('secret', $secret)->whereHas('user')->first();
            if($auth and $auth->user)
            {
                return $auth;
            }
        }

        return false;
    }

    /**
     * Callback hanlder
     * @param  string $provider key
     * @return void|exception
     */
    protected function callbackHandler($provider)
    {
        // Call driver
        $provider = BotAuth::driver($provider)->callback();

        // Get User data
        $userData = $provider->getUser();

        if(is_array($userData) and !empty($userData))
        {
            // Check auth code
            $secret = trim($provider->getText());
            if(!\Str::startsWith($secret, $this->getSecretPrefix()))
            {
                // To send a message back
                $provider->sendMessage(__('botauth::callback.code_isnot_correct'));
                return;
            }

            // To send a message back
            $provider->sendMessage(__('botauth::callback.return_back'));

            // Save auth code
            $botAuth = BotAuthModel::updateOrCreate(
                [
                    'provider'    => $provider->getProviderSlug(),
                    'external_id' => $userData['id'],
                ],
                [
                    'secret' => $secret,
                ]
            );

            // Find user
            $user = User::whereHas('botAuth', function($query) use ($botAuth) {
                $query->where('id', $botAuth->id);
            })->first();

            // Create user
            if(!$user)
            {
                // Bot doesn't know email
                $email = $userData['email'] ?? $provider->getProviderSlug() . $userData['id'] . '@localhost.com';

                $user = User::firstOrCreate(
                    [
                        'email' => $email,
                    ],
                    [
                        'name'     => $userData['first_name'] . ' ' .$userData['last_name'] ,
                        'password' => bcrypt(time().uniqid()),
                    ]
                );
            }

            // Save user id
            $botAuth->user_id = $user->id;
            $botAuth->save();
        }
    }
}