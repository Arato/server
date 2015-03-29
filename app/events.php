<?php

use Arato\Push\PushService;
use models\enum\NotificationType;
use Underscore\Types\Arrays;
use Arato\Transformers\NotificationEntryTransformer;
use Arato\Transformers\NotificationTransformer;

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
    $notificationEntryTransformer = new NotificationEntryTransformer();
    $notificationTransformer = new NotificationTransformer($notificationEntryTransformer);
    $pushService->emit('php.alert.created', $notificationTransformer->extendedTransform($notification));
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
    $notificationEntryTransformer = new NotificationEntryTransformer();
    $notificationTransformer = new NotificationTransformer($notificationEntryTransformer);
    $pushService->emit('php.alert.updated', $notificationTransformer->extendedTransform($notification));
});

Alert::deleted(function ($alert) {
    $notification = new Notification([
        'type' => NotificationType::REMOVAL
    ]);

    $notification = $alert->notifications()->save($notification);

    $pushService = new PushService();
    $notificationEntryTransformer = new NotificationEntryTransformer();
    $notificationTransformer = new NotificationTransformer($notificationEntryTransformer);
    $pushService->emit('php.alert.deleted', $notificationTransformer->extendedTransform($notification));
});