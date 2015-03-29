<?php

namespace Arato\Transformers;

use Underscore\Types\Arrays;

class AlertTransformer extends Transformer
{
    public $notificationTransformer;
    public $userTransformer;

    function __construct(NotificationTransformer $notificationTransformer, UserTransformer $userTransformer)
    {
        $this->notificationTransformer = $notificationTransformer;
        $this->userTransformer = $userTransformer;
    }


    public function basicTransform($item)
    {
        return [
            'id'    => $item['id'],
            'title' => $item['title']
        ];
    }

    public function extendedTransform($item)
    {
        return Arrays::merge(
            $this->basicTransform($item),
            [
                'price'      => $item['price'],
                'content'    => $item['content'],
                'user'       => $this->userTransformer->basicTransform($item->user),
                'created_at' => $item['created_at']->toIso8601String(),
                'updated_at' => $item['updated_at']->toIso8601String()
            ]);
    }

    public function fullTransform($item)
    {
        return Arrays::merge(
            $this->extendedTransform($item),
            [
                'notifications' => $this->notificationTransformer->transformCollection($item->notifications->all())
            ]);
    }
}