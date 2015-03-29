<?php


namespace Arato\Transformers;


use Underscore\Types\Arrays;

class NotificationTransformer extends Transformer
{
    public $notificationEntryTransformer;

    function __construct(NotificationEntryTransformer $notificationEntryTransformer)
    {
        $this->notificationEntryTransformer = $notificationEntryTransformer;
    }

    public function basicTransform($item)
    {
        return [
            'id'              => $item['id'],
            'type'            => $item['type'],
            'notifiable_id'   => $item['notifiable_id'],
            'notifiable_type' => $item['notifiable_type']
        ];
    }

    public function extendedTransform($item)
    {
        return Arrays::merge(
            $this->basicTransform($item),
            [
                'entries'    => $this->notificationEntryTransformer->transformCollection($item->entries->all()),
                'created_at' => $item['created_at']->toIso8601String(),
                'updated_at' => $item['updated_at']->toIso8601String()
            ]);
    }

    public function fullTransform($item)
    {
        return $this->basicTransform($item);
    }
}