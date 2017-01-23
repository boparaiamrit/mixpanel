<?php namespace Boparaiamrit\Mixpanel\Listeners;


use Boparaiamrit\Mixpanel\Events\MixpanelEvent;
use Illuminate\Auth\Events\Attempting;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Events\Dispatcher;

class MixpanelEventHandler
{
	protected $Guard;
	
	public function __construct(Guard $Guard)
	{
		$this->Guard = $Guard;
	}
	
	
	public function onUserLoginAttempt($event)
	{
		$email    = $event->credentials['email'] ?? '';
		$password = $event->credentials['password'] ?? '';
		
		$authModel = config('auth.providers.admins.model');
		$user      = app($authModel)->where('email', $email)
									->first();
		
		if (empty($user)) {
			$authModel = config('auth.providers.contacts.model');
			$user      = app($authModel)->where('email', $email)
										->first();
		}
		
		$trackingData = [
			['Session', ['Status' => 'Login Attempt Succeeded']],
		];
		
		/** @noinspection PhpUndefinedMethodInspection */
		if ($user
			&& !$this->Guard->getProvider()->validateCredentials($user, ['email' => $email, 'password' => $password])
		) {
			$trackingData = [
				['Session', ['Status' => 'Login Attempt Failed']],
			];
		} elseif (empty($user)){
             return redirect()->back()
                ->withInput(['email' => $email])
                ->withErrors(['email' => 'Email or Password doesn\'t Match.']);
        }
		
		event(new MixpanelEvent('Login Attempt', $user, $trackingData));
	}
	
	public function onUserLogin($login)
	{
		$user = $login->user;
		
		$trackingData = [
			['Session', ['Status' => 'Logged In']],
		];
		event(new MixpanelEvent('User Login', $user, $trackingData));
	}
	
	public function onUserLogout($logout)
	{
		$user = $logout->user;
		
		$trackingData = [
			['Session', ['Status' => 'Logged Out']],
		];
		event(new MixpanelEvent('User Logout', $user, $trackingData));
	}
	
	public function subscribe(Dispatcher $events)
	{
		$events->listen(Attempting::class, self::class . '@onUserLoginAttempt');
		$events->listen(Login::class, self::class . '@onUserLogin');
		$events->listen(Logout::class, self::class . '@onUserLogout');
	}
}
