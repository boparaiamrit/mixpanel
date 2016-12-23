<?php namespace Boparaiamrit\Mixpanel\Listeners;


use Boparaiamrit\Mixpanel\Events\MixpanelEvent as Event;
use Illuminate\Database\Eloquent\Model;

class MixpanelEvent
{
	public function handle(Event $event)
	{
		/** @var Model $user */
		$user      = $event->user;
		$eventName = $event->eventName;
		
		$profileData = $this->getProfileData($user);
		$profileData = array_merge($profileData, $event->profileData);
		
		app('mixpanel')->identify($user->getKey());
		app('mixpanel')->people->set($user->getKey(), $profileData, request()->ip());
		
		if ($event->charge !== 0) {
			app('mixpanel')->people->trackCharge($user->getKey(), $event->charge);
		}
		
		array_map(function ($data) use ($eventName) {
			app('mixpanel')->track($eventName, $data);
		}, $event->trackingData);
	}
	
	private function getProfileData($user): array
	{
		$firstName = $user->first_name;
		$lastName  = $user->last_name;
		
		if ($user->name) {
			$nameParts = explode(' ', $user->name);
			array_filter($nameParts);
			$lastName  = array_pop($nameParts);
			$firstName = implode(' ', $nameParts);
		}
		
		$data = [
			'$first_name' => $firstName,
			'$last_name'  => $lastName,
			'$name'       => $user->name,
			'$email'      => $user->email,
			'$created'    => ($user->created_at
				? $user->created_at->format('Y-m-d\Th:i:s')
				: null),
			'$domain' => (request()->header('referer')
				? parse_url(request()->header('referer'))['host']
				: null)
		];
		array_filter($data);
		
		return $data;
	}
}
