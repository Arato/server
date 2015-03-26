<?php

/*
|--------------------------------------------------------------------------
| Register The Laravel Class Loader
|--------------------------------------------------------------------------
|
| In addition to using Composer, you may use the Laravel class loader to
| load your controllers and models. This is useful for keeping all of
| your classes in the "global" namespace without Composer updating.
|
*/

use Arato\Push\PushService;
use models\enum\NotificationType;
use Underscore\Types\Arrays;

ClassLoader::addDirectories([

    app_path() . '/commands',
    app_path() . '/controllers',
    app_path() . '/models',
    app_path() . '/database/seeds',

]);

/*
|--------------------------------------------------------------------------
| Application Error Logger
|--------------------------------------------------------------------------
|
| Here we will configure the error logger setup for the application which
| is built on top of the wonderful Monolog library. By default we will
| build a basic log file setup which creates a single file for logs.
|
*/
if (App::environment('production')) {
    Log::useFiles('php://stderr');
} else {
    Log::useFiles(storage_path() . '/logs/laravel.log');
}

/*
|--------------------------------------------------------------------------
| Application Error Handler
|--------------------------------------------------------------------------
|
| Here you may handle any errors that occur in your application, including
| logging them or displaying custom views for specific errors. You may
| even register several error handlers to handle different types of
| exceptions. If nothing is returned, the default error view is
| shown, which includes a detailed stack trace during debug.
|
*/

App::error(function (Exception $exception, $code) {
    Log::error($exception);
});

/*
|--------------------------------------------------------------------------
| Maintenance Mode Handler
|--------------------------------------------------------------------------
|
| The "down" Artisan command gives you the ability to put an application
| into maintenance mode. Here, you will define what is displayed back
| to the user if maintenance mode is in effect for the application.
|
*/

App::down(function () {
    return Response::make("Be right back!", 503);
});

/*
|--------------------------------------------------------------------------
| Require The Filters File
|--------------------------------------------------------------------------
|
| Next we will load the filters file for the application. This gives us
| a nice separate location to store our route and application filter
| definitions instead of putting them all in the main routes file.
|
*/

require app_path() . '/filters.php';


Alert::created(function ($alert) {
    $entries = Arrays::invoke($alert->getNotifiableProperties(), function ($key) use ($alert) {
        return new NotificationEntry([
            'field'         => $key,
            'previousValue' => null,
            'newValue'      => $alert->{$key}
        ]);
    });

    $notification = new Notification([
        'type' => NotificationType::CREATION
    ]);

    $notification = $alert->notifications()->save($notification);
    if (count($entries)) {
        try {
            $notification->entries()->saveMany($entries);
        }
        catch (Exception $e) {
            $notification->delete();
        }
    }

    $pushService = new PushService();
    $pushService->emit('php.alert.created', $notification);
});

Alert::updating(function ($alert) {
    $previousAlert = Alert::find($alert->id);
    $entries = Arrays::from($alert->getNotifiableProperties())
        ->filter(function ($key) use ($alert, $previousAlert) {
            return $previousAlert->{$key} !== $alert->{$key};
        })
        ->invoke(function ($key) use ($alert, $previousAlert) {
            return new NotificationEntry([
                'field'         => $key,
                'previousValue' => $previousAlert->{$key},
                'newValue'      => $alert->{$key}
            ]);
        })
        ->obtain();

    $notification = new Notification([
        'type' => NotificationType::UPDATE
    ]);

    $notification = $alert->notifications()->save($notification);
    if (count($entries)) {
        try {
            $notification->entries()->saveMany($entries);
        }
        catch (Exception $e) {
            $notification->delete();
        }
    }

    $pushService = new PushService();
    $pushService->emit('php.alert.updated', $notification);
});

Alert::deleted(function ($alert) {
    $notification = new Notification([
        'type' => NotificationType::REMOVAL
    ]);

    $notification = $alert->notifications()->save($notification);

    $pushService = new PushService();
    $pushService->emit('php.alert.deleted', $notification);
});