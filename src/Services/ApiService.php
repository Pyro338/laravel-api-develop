<?php

namespace Gamebetr\Api\Services;

use Carbon\Carbon;
use DBD\GlobalAuth\Facades\GlobalAuth;
use DBD\Utility\Facades\Utility;
use Exception;
use Gamebetr\Api\Events\UserLoggedIn;
use Gamebetr\Api\Mail\PasswordReset;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use stdClass;
use DBD\Cdp\Cdp;

class ApiService
{
    /**
     * Get base currency.
     * @return string
     */
    public function getBaseCurrency() {
        if(!$domain = GlobalAuth::getDomain()) {
            abort(404, 'Unknown domain');
        }
        if(!$baseCurrency = $domain->variable('base_currency')) {
            abort(404, 'Unknown base currency');
        }

        return $baseCurrency;
    }

    /**
     * Enable 2fa.
     * @return \stdClass
     */
    public function enable2fa()
    {
    }

    /**
     * Login a user.
     * @param string $email
     * @param string $password
     * @param string $twoFactorKey
     * @return \stdClass
     */
    public function login(
        string $email,
        string $password,
        string $twoFactorKey = ''
    ) : stdClass {
        $validator = Validator::make([
            'email' => $email,
            'password' => $password,
        ], [
            'email' => 'required|max:255|email',
            'password' => 'required',
        ]);
        if ($validator->fails()) {
            abort(422, $validator->getMessageBag()->first());
        }

        $token = GlobalAuth::login(GlobalAuth::getDomain()->id, $email, $password);
        $user = GlobalAuth::me($token->data->attributes->token);

        if (!$twoFactorKey && $user->two_factor_enabled) {
            return (object)['data' => ['verify_2fa' => true, 'message' => 'Please verify 2FA']];
        }
        if (!$twoFactorKey || GlobalAuth::verify2fa($twoFactorKey, $token)){
            UserLoggedIn::dispatch($user);

            Cache::put('user_'.$user->id, $user, Carbon::now()->addDay());
            Cache::put('user_'.$user->uuid, $user, Carbon::now()->addDay());

            // do CDP login event here
            $cdp = new Cdp(['domainId' => GlobalAuth::getDomain()->id]);
            $cdp->track(
                [
                    'event' => 'login',
                    'userId' => $user->id,
                ]
            );

            return $token;
        }
        abort(422, 'Wrong data');
    }

    /**
     * Register a user.
     * @param string $name
     * @param string $email
     * @param string $password
     * @return \Illuminate\Contracts\Auth\Authenticatable
     */
    public function registerUser(
        string $name,
        string $email,
        string $password,
        $affiliate = null
    ) : Authenticatable {
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'affiliate' => $affiliate,
        ], [
            'name' => 'required|max:255',
            'email' => 'required|max:255|email',
            'password' => 'required|min:8|max:255',
            'affiliate' => 'nullable|integer',
        ]);
        if ($validator->fails()) {
            abort(422, $validator->getMessageBag()->first());
        }
        $user = GlobalAuth::createUser($name, $email, $password);
        $user->affiliate_id = $affiliate;
        $user->save();
        Artisan::call('api:create-accounts-for-user', ['userId' => $user->id]);

        // do CDP completes register event here
        $cdp = new Cdp(['domainId' => GlobalAuth::getDomain()->id]);
        $cdp->identify(
            [
                'userId' => $user->id,
                'traits' => [
                    'username' => $user->name,
                    'email' => $user->email,
                ]
            ]
        );
        $cdp->track(
            [
                'event' => 'completes_registration',
                'userId' => $user->id,
            ]
        );

        return $user;
    }

    /**
     * Recover password.
     * @param string $email
     * @param string $password
     */
    public function recoverPassword(string $email, string $password)
    {
        if (!$user = GlobalAuth::findUserViaEmail($email)) {
            return;
        }
        $token = Utility::randomString(64);
        Cache::put('password_recovery_'.$token, [
            'user' => $user,
            'password' => encrypt($password),
        ], Carbon::now()->addMinutes(10));
        Mail::to($user)->send(new PasswordReset($token, $user));
    }
}
