<?php namespace Boparaiamrit\Mixpanel\Console\Commands;


use Boparaiamrit\Mixpanel\Providers\MixpanelServiceProvider;
use Illuminate\Console\Command;

class Publish extends Command
{
	protected $signature   = 'mixpanel:publish {--assets}';
	protected $description = 'Publish various assets of the mixpanel package.';
	
	public function handle()
	{
		if ($this->option('assets')) {
			$this->call('vendor:publish', [
				'--provider' => MixpanelServiceProvider::class,
				'--tag'      => ['assets'],
				'--force'    => true,
			]);
		}
	}
}
