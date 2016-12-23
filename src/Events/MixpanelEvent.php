<?php namespace Boparaiamrit\Mixpanel\Events;


use Illuminate\Queue\SerializesModels;

class MixpanelEvent
{
	use SerializesModels;
	
	public $eventName;
	public $charge;
	public $profileData;
	public $trackingData;
	public $user;
	
	public function __construct($eventName, $user, array $trackingData, int $charge = 0, array $profileData = [])
	{
		$this->eventName    = $eventName;
		$this->user         = $user;
		$this->profileData  = $profileData;
		$this->charge       = $charge;
		$this->trackingData = $this->addTimestamp($trackingData);
	}
	
	private function addTimestamp(array $trackingData): array
	{
		return array_map(function ($data) {
			if (!array_key_exists('time', $data)) {
				$data['time'] = time();
			}
			
			return $data;
		}, $trackingData);
	}
}
