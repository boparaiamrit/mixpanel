<?php namespace Boparaiamrit\Mixpanel\Providers;


use Boparaiamrit\Mixpanel\Console\Commands\Publish;
use Boparaiamrit\Mixpanel\Events\MixpanelEvent;
use Boparaiamrit\Mixpanel\Listeners\MixpanelEvent as MixpanelEventListeners;
use Boparaiamrit\Mixpanel\Listeners\MixpanelEventHandler;
use Boparaiamrit\Mixpanel\Listeners\MixpanelUserObserver;
use Boparaiamrit\Mixpanel\Mixpanel;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Support\ServiceProvider;

class MixpanelServiceProvider extends ServiceProvider
{
	protected $defer = true;
	
	public function boot(Guard $Guard)
	{
		include __DIR__ . '/../../routes/api.php';
		
		$this->loadViewsFrom(__DIR__ . '/../../resources/views', 'mixpanel');
		$this->publishes([
			__DIR__ . '/../../public' => public_path(),
		], 'assets');
		
		if (config('services.mixpanel.enable-default-tracking')) {
			app('events')->subscribe(new MixpanelEventHandler($Guard));
			app('events')->listen(MixpanelEvent::class, MixpanelEventListeners::class);
		}
	}
	
	public function register()
	{
		$this->mergeConfigFrom(__DIR__ . '/../../config/services.php', 'services');
		$this->commands(Publish::class);
		
		$this->app->singleton('mixpanel', Mixpanel::class);
		
		$authModel = config('auth.providers.contacts.model');
		$this->app->make($authModel)->observe(new MixpanelUserObserver());
	}
	
	/**
	 * @return array
	 */
	public function provides()
	{
		return ['mixpanel'];
	}
}
