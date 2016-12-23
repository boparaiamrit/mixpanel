<?php namespace Boparaiamrit\Mixpanel\Listeners;


use Boparaiamrit\Mixpanel\Events\MixpanelEvent;

class MixpanelUserObserver
{
	public function created($user)
	{
		$trackingData = [
			['User', ['Status' => 'Registered']],
		];
		event(new MixpanelEvent('User Registered', $user, $trackingData));
	}
	
	public function saving($user)
	{
		$trackingData = [
			['User', ['Status' => 'Updated']],
		];
		event(new MixpanelEvent('User Updated', $user, $trackingData));
	}
	
	public function deleting($user)
	{
		$trackingData = [
			['User', ['Status' => 'Deactivated']],
		];
		event(new MixpanelEvent('User Deleted', $user, $trackingData));
	}
	
	public function restored($user)
	{
		$trackingData = [
			['User', ['Status' => 'Reactivated']],
		];
		event(new MixpanelEvent('User Restored', $user, $trackingData));
	}
}
