<?php

use Boparaiamrit\Mixpanel\Http\Controllers\StripeWebhooksController;

Route::post('mixpanel/stripe', StripeWebhooksController::class . '@postTransaction');
